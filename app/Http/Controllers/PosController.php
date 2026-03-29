<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ProductLot;
use App\Models\Supplier;

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
                    'created_by' => auth()->id() ?: null,
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
                  ->orWhere('barcode', $query)
                  ->orWhere('code', $query);
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

        $products = Product::when($q, function($query) use ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('trade_name', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
            });
        })->orderBy('id', 'desc')->paginate(20);

        return view('pos.products_index', compact('products', 'q'));
    }

    public function editProduct(Product $product)
    {
        return view('pos.edit_product', compact('product'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $data = $request->validate([
            'barcode' => 'required|string|max:50|unique:products,barcode,' . $product->id,
            'code' => 'nullable|string|max:50|unique:products,code,' . $product->id,
            'trade_name' => 'required|string|max:255',
            'name_for_print' => 'nullable|string|max:255',
            'price_retail' => 'required|numeric|min:0',
            'price_wholesale1' => 'nullable|numeric|min:0',
            'item_type' => 'nullable|string|in:drug,supply,equipment,service',
            'reorder_point' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'drug_type_id' => 'nullable|integer|exists:drug_types,id',
        ]);

        $data['item_type'] = $data['item_type'] ?? 'drug';
        $product->update($data);

        return redirect()->route('products.index')->with('success', 'อัพเดตข้อมูลสินค้าเรียบร้อยแล้ว');
    }

    public function createProduct()
    {
        return view('pos.create_product');
    }

    public function storeProduct(Request $request)
    {
        $data = $request->validate([
            'barcode' => 'required|string|max:50|unique:products,barcode',
            'code' => 'nullable|string|max:50|unique:products,code',
            'trade_name' => 'required|string|max:255',
            'name_for_print' => 'nullable|string|max:255',
            'price_retail' => 'required|numeric|min:0',
            'price_wholesale1' => 'nullable|numeric|min:0',
            'item_type' => 'nullable|string|in:drug,supply,equipment,service',
            'reorder_point' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'drug_type_id' => 'nullable|integer|exists:drug_types,id',
        ]);

        $data['item_type'] = $data['item_type'] ?? 'drug';

        Product::create(array_merge($data, [
            'is_disabled' => false,
            'is_hidden' => false,
        ]));

        return redirect()->route('pos.products.create')
            ->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว');
    }
}
