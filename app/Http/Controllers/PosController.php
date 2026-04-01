<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Customer;
use App\Models\ProductLot;
use App\Models\Supplier;
use App\Models\DrugType;
use App\Models\DosageForm;
use App\Models\ProductCategory;

class PosController extends Controller
{
    public function index()
    {
        $customers = Customer::where('id', '!=', 1)->get();
        return view('pos.index', compact('customers'));
    }

    public function receiveStockForm()
    {
        $products = Product::where('is_disabled', false)->orderBy('trade_name')->get();
        $suppliers = Supplier::where('is_disabled', false)->orderBy('name')->get();

        return view('pos.receive_stock', compact('products', 'suppliers'));
    }

    public function receiveStock(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'receive_date' => 'required|date',
            'invoice_no' => 'nullable|string|max:100',

            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'lot_number' => 'required|array|min:1',
            'lot_number.*' => 'required|string|max:100',
            'manufactured_date' => 'nullable|array',
            'manufactured_date.*' => 'nullable|date',
            'expiry_date' => 'required|array|min:1',
            'expiry_date.*' => 'required|date',
            'cost_price' => 'required|array|min:1',
            'cost_price.*' => 'required|numeric|min:0',
            'sell_price' => 'required|array|min:1',
            'sell_price.*' => 'required|numeric|min:0',
            'qty_received' => 'required|array|min:1',
            'qty_received.*' => 'required|integer|min:1',
            'note' => 'nullable|array',
            'note.*' => 'nullable|string|max:255',
        ]);

        $rows = count($data['product_id']);

        DB::transaction(function () use ($data, $rows) {
            for ($i = 0; $i < $rows; $i++) {
                $productId = $data['product_id'][$i];
                $lotNumber = $data['lot_number'][$i];
                $manufacturedDate = $data['manufactured_date'][$i] ?? null;
                $expiryDate = $data['expiry_date'][$i];
                $costPrice = $data['cost_price'][$i];
                $sellPrice = $data['sell_price'][$i];
                $qtyReceived = $data['qty_received'][$i];
                $itemNote = $data['note'][$i] ?? null;

                if (!$productId || !$lotNumber || !$expiryDate || !$qtyReceived) {
                    continue;
                }

                $lot = ProductLot::firstOrNew([
                    'product_id' => $productId,
                    'lot_number' => $lotNumber,
                ]);

                $prevOnHand = $lot->exists ? $lot->qty_on_hand : 0;

                $lot->supplier_id = $data['supplier_id'] ?? null;
                $lot->manufactured_date = $manufacturedDate;
                $lot->expiry_date = $expiryDate;
                $lot->cost_price = $costPrice;
                $lot->sell_price = $sellPrice;
                $lot->qty_received = ($lot->qty_received ?: 0) + $qtyReceived;
                $lot->qty_on_hand = ($lot->qty_on_hand ?: 0) + $qtyReceived;
                $lot->note = $itemNote;
                $lot->is_closed = false;
                $lot->save();

                DB::table('stock_movements')->insert([
                    'product_id' => $lot->product_id,
                    'lot_id' => $lot->id,
                    'movement_type' => 'receive',
                    'ref_type' => 'stock_receive',
                    'ref_id' => null,
                    'qty_change' => $qtyReceived,
                    'qty_before' => $prevOnHand,
                    'qty_after' => $lot->qty_on_hand,
                    'unit_cost' => $costPrice,
                    'note' => 'รับยาเข้าสต๊อค (เลขที่เอกสาร: ' . ($data['invoice_no'] ?? '-') . ')',
                    'created_by' => optional(auth()->user())->id,
                    'created_at' => now(),
                ]);
            }
        });

        return redirect()->route('pos.stock.receive')->with('success', 'บันทึกการรับยาเข้าสต๊อคเรียบร้อยแล้ว');
    }

    public function receiveStockHistory(Request $request)
    {
        $query = DB::table('stock_movements')
            ->join('products', 'stock_movements.product_id', '=', 'products.id')
            ->join('product_lots', 'stock_movements.lot_id', '=', 'product_lots.id')
            ->leftJoin('suppliers', 'product_lots.supplier_id', '=', 'suppliers.id')
            ->leftJoin('users', 'stock_movements.created_by', '=', 'users.id')
            ->where('stock_movements.movement_type', 'receive')
            ->select([
                'stock_movements.id',
                'stock_movements.created_at',
                'stock_movements.qty_change',
                'stock_movements.unit_cost',
                'stock_movements.note',
                'products.trade_name',
                'products.barcode',
                'products.code',
                'product_lots.lot_number',
                'product_lots.expiry_date',
                'suppliers.name as supplier_name',
                'users.name as created_by_name',
            ]);

        // Filters
        if ($request->filled('date_from')) {
            $query->whereDate('stock_movements.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('stock_movements.created_at', '<=', $request->date_to);
        }
        if ($request->filled('supplier_id')) {
            $query->where('product_lots.supplier_id', $request->supplier_id);
        }
        if ($request->filled('product_id')) {
            $query->where('stock_movements.product_id', $request->product_id);
        }

        $movements = $query->orderBy('stock_movements.created_at', 'desc')->paginate(50);

        $suppliers = Supplier::where('is_disabled', false)->orderBy('name')->get();
        $products = Product::where('is_disabled', false)->orderBy('trade_name')->get();

        return view('pos.receive_stock_history', compact('movements', 'suppliers', 'products'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $products = Product::where('is_disabled', false)
            ->where(function($q) use ($query) {
                $q->where('trade_name', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%")
                  ->orWhere('search_keywords', 'like', "%{$query}%")
                  ->orWhere('code', 'like', "%{$query}%");
            })
            ->with(['lots' => function($q) {
                $q->where('qty_on_hand', '>', 0)
                  ->orderBy('expiry_date', 'asc');
            }, 'unit'])
            ->get();

        return response()->json($products);
    }

    public function productIndex(Request $request)
    {
        $q = trim($request->get('q', ''));

        // Sorting
        $allowedSortBy = ['id', 'code', 'trade_name', 'price_retail'];
        $sort_by = $request->get('sort_by', 'id');
        if (!in_array($sort_by, $allowedSortBy)) {
            $sort_by = 'id';
        }
        $sort_dir = strtolower($request->get('sort_dir', 'desc'));
        if (!in_array($sort_dir, ['asc', 'desc'])) {
            $sort_dir = 'desc';
        }

        $products = Product::when($q, function($query) use ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('trade_name', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
            });
        })->orderBy($sort_by, $sort_dir)->paginate(20);

        return view('pos.products_index', compact('products', 'q', 'sort_by', 'sort_dir'));
    }

    public function editProduct(Product $product)
    {
        $product->load('productUnits');
        $drugTypes   = DrugType::where('is_disabled', false)->orderBy('name_th')->get();
        $dosageForms = DosageForm::where('is_disabled', false)->orderBy('name_th')->get();
        $categories = ProductCategory::active()->get();
        $baseUnitName = optional($product->productUnits->firstWhere('is_base_unit', true))->unit_name
            ?? optional($product->productUnits->firstWhere('qty_per_base', '1.0000'))->unit_name
            ?? optional($product->productUnits->first())->unit_name;

        return view('pos.edit_product', compact('product', 'drugTypes', 'dosageForms', 'categories', 'baseUnitName'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $data = $request->validate([
            'barcode'           => 'nullable|string|max:50|unique:products,barcode,' . $product->id,
            'barcode2'          => 'nullable|string|max:50',
            'code'              => 'nullable|string|max:50|unique:products,code,' . $product->id,
            'trade_name'        => 'required|string|max:255',
            'name_for_print'    => 'nullable|string|max:255',
            'category_id'       => 'nullable|integer|exists:product_categories,id',
            'dosage_form_id'    => 'nullable|integer|exists:dosage_forms,id',
            'base_unit_name'    => 'required|string|max:50',
            'price_retail'      => 'required|numeric|min:0',
            'price_wholesale1'  => 'nullable|numeric|min:0',
            'price_wholesale2'  => 'nullable|numeric|min:0',
            'is_vat'            => 'nullable|boolean',
            'is_not_discount'   => 'nullable|boolean',
            'reorder_point'     => 'nullable|integer|min:0',
            'safety_stock'      => 'nullable|integer|min:0',
            'expiry_alert_days1'=> 'nullable|integer|min:1',
            'expiry_alert_days2'=> 'nullable|integer|min:1',
            'expiry_alert_days3'=> 'nullable|integer|min:1',
            'drug_type_id'      => 'nullable|integer|exists:drug_types,id',
            'strength'          => 'nullable|numeric|min:0',
            'registration_no'   => 'nullable|string|max:50',
            'tmt_id'            => 'nullable|string|max:30',
            'is_original_drug'  => 'nullable|boolean',
            'is_antibiotic'     => 'nullable|boolean',
            'max_dispense_qty'  => 'nullable|numeric|min:0',
            'default_qty'       => 'nullable|integer|min:1',
            'indication_note'   => 'nullable|string',
            'side_effect_note'  => 'nullable|string',
            'is_fda_report'     => 'nullable|boolean',
            'is_fda11_report'   => 'nullable|boolean',
            'is_fda13_report'   => 'nullable|boolean',
            'is_sale_control'   => 'nullable|boolean',
            'sale_control_qty'  => 'nullable|numeric|min:0',
            'note'              => 'nullable|string',
        ]);

        $data['is_vat']          = $request->boolean('is_vat');
        $data['is_not_discount'] = $request->boolean('is_not_discount');
        $data['is_original_drug']= $request->boolean('is_original_drug');
        $data['is_antibiotic']   = $request->boolean('is_antibiotic');
        $data['is_fda_report']   = $request->boolean('is_fda_report');
        $data['is_fda11_report'] = $request->boolean('is_fda11_report');
        $data['is_fda13_report'] = $request->boolean('is_fda13_report');
        $data['is_sale_control'] = $request->boolean('is_sale_control');
        $data['default_qty']     = $data['default_qty'] ?? 1;
        $baseUnitName = $data['base_unit_name'];
        $data['unit_name'] = $baseUnitName;
        unset($data['base_unit_name']);

        $product->update($data);

        $product->productUnits()->updateOrCreate(
            ['is_base_unit' => true],
            [
                'unit_name' => $baseUnitName,
                'qty_per_base' => 1,
                'is_for_sale' => true,
                'is_for_purchase' => true,
                'is_disabled' => false,
                'price_retail' => $product->price_retail ?? 0,
                'price_wholesale1' => $product->price_wholesale1 ?? 0,
                'price_wholesale2' => $product->price_wholesale2 ?? 0,
            ]
        );

        return redirect()->route('products.edit', $product)
            ->with('success', 'บันทึกข้อมูลสินค้าเรียบร้อยแล้ว');
    }

    public function createProduct()
    {
        $drugTypes   = DrugType::where('is_disabled', false)->orderBy('name_th')->get();
        $dosageForms = DosageForm::where('is_disabled', false)->orderBy('name_th')->get();
        $categories  = \App\Models\ProductCategory::where('is_disabled', false)
                           ->orderBy('sort_order')
                           ->get();
        return view('pos.create_product', compact('drugTypes', 'dosageForms', 'categories'));
    }

    public function storeProduct(Request $request)
    {
        $data = $request->validate([
            'barcode'           => 'nullable|string|max:50|unique:products,barcode',
            'barcode2'          => 'nullable|string|max:50',
            'code'              => 'nullable|string|max:50|unique:products,code',
            'trade_name'        => 'required|string|max:255',
            'name_for_print'    => 'nullable|string|max:255',
            'category_id'       => 'nullable|integer|exists:product_categories,id',
            'dosage_form_id'    => 'nullable|integer|exists:dosage_forms,id',
            'base_unit_name'    => 'required|string|max:50',
            'price_retail'      => 'required|numeric|min:0',
            'price_wholesale1'  => 'nullable|numeric|min:0',
            'price_wholesale2'  => 'nullable|numeric|min:0',
            'is_vat'            => 'nullable|boolean',
            'is_not_discount'   => 'nullable|boolean',
            'reorder_point'     => 'nullable|integer|min:0',
            'safety_stock'      => 'nullable|integer|min:0',
            'expiry_alert_days1'=> 'nullable|integer|min:1',
            'expiry_alert_days2'=> 'nullable|integer|min:1',
            'expiry_alert_days3'=> 'nullable|integer|min:1',
            'drug_type_id'      => 'nullable|integer|exists:drug_types,id',
            'strength'          => 'nullable|numeric|min:0',
            'registration_no'   => 'nullable|string|max:50',
            'tmt_id'            => 'nullable|string|max:30',
            'is_original_drug'  => 'nullable|boolean',
            'is_antibiotic'     => 'nullable|boolean',
            'max_dispense_qty'  => 'nullable|numeric|min:0',
            'default_qty'       => 'nullable|integer|min:1',
            'indication_note'   => 'nullable|string',
            'side_effect_note'  => 'nullable|string',
            'is_fda_report'     => 'nullable|boolean',
            'is_fda11_report'   => 'nullable|boolean',
            'is_fda13_report'   => 'nullable|boolean',
            'is_sale_control'   => 'nullable|boolean',
            'sale_control_qty'  => 'nullable|numeric|min:0',
            'note'              => 'nullable|string',
        ]);

        $data['is_vat']          = $request->boolean('is_vat');
        $data['is_not_discount'] = $request->boolean('is_not_discount');
        $data['is_original_drug']= $request->boolean('is_original_drug');
        $data['is_antibiotic']   = $request->boolean('is_antibiotic');
        $data['is_fda_report']   = $request->boolean('is_fda_report');
        $data['is_fda11_report'] = $request->boolean('is_fda11_report');
        $data['is_fda13_report'] = $request->boolean('is_fda13_report');
        $data['is_sale_control'] = $request->boolean('is_sale_control');
        $data['default_qty']     = $data['default_qty'] ?? 1;
        $data['is_disabled']     = false;
        $data['is_hidden']       = false;
        $baseUnitName = $data['base_unit_name'];
        $data['unit_name'] = $baseUnitName;
        unset($data['base_unit_name']);
        if (empty($data['code'])) {
            $lastId = Product::max('id') ?? 0;
            $data['code'] = 'PRD-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
        }

        $product = Product::create($data);
        $product->productUnits()->create([
            'unit_name' => $baseUnitName,
            'qty_per_base' => 1,
            'is_base_unit' => true,
            'is_for_sale' => true,
            'is_for_purchase' => true,
            'is_disabled' => false,
            'price_retail' => $product->price_retail ?? 0,
            'price_wholesale1' => $product->price_wholesale1 ?? 0,
            'price_wholesale2' => $product->price_wholesale2 ?? 0,
        ]);

        return redirect()->route('pos.products.create')
            ->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว');
    }
    // เพิ่ม/ลบ product unit
    public function storeProductUnit(Request $request, Product $product)
    {
        $data = $request->validate([
            'unit_name' => 'required|string|max:50',
            'qty_per_base' => 'required|numeric|min:0.0001',
            'barcode' => 'nullable|string|max:50|unique:product_units,barcode',
            'is_for_sale' => 'nullable|boolean',
            'is_for_purchase' => 'nullable|boolean',
            'price_retail'     => 'nullable|numeric|min:0',
            'price_wholesale1' => 'nullable|numeric|min:0',
            'price_wholesale2' => 'nullable|numeric|min:0',
        ]);
        $data['is_for_sale'] = $request->boolean('is_for_sale');
        $data['is_for_purchase'] = $request->boolean('is_for_purchase');
        $data['is_disabled'] = false;
        $data['price_retail']     = $data['price_retail'] ?? 0;
        $data['price_wholesale1'] = $data['price_wholesale1'] ?? 0;
        $data['price_wholesale2'] = $data['price_wholesale2'] ?? 0;
        $product->productUnits()->create($data);
        return redirect()->back()->with('success', 'เพิ่มหน่วยสำเร็จ');
    }

    public function destroyProductUnit(Product $product, ProductUnit $productUnit)
    {
        $productUnit->delete();
        return redirect()->back()->with('success', 'ลบหน่วยสำเร็จ');
    }
}
