<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\ProductLabel;
use App\Models\ProductUnit;
use App\Models\Customer;
use App\Models\ProductLot;
use App\Models\Supplier;
use App\Models\DrugType;
use App\Models\DosageForm;
use App\Models\ProductCategory;
use App\Models\DrugGenericName;

class PosController extends Controller
{
    private function checkBarcodeUnique(array $barcodes, $excludeProductId = null): ?array
    {
        $barcodeFields = ['barcode', 'barcode2', 'barcode3', 'barcode4'];

        $normalizedByField = [];
        foreach ($barcodeFields as $index => $field) {
            $normalizedByField[$field] = trim((string) ($barcodes[$index] ?? ''));
        }

        // เช็ค duplicate ภายใน array เดียวกัน
        $seen = [];
        $duplicates = [];
        foreach ($normalizedByField as $field => $value) {
            if ($value === '') {
                continue;
            }

            if (isset($seen[$value])) {
                $duplicates[] = $seen[$value];
                $duplicates[] = $field;
                continue;
            }

            $seen[$value] = $field;
        }

        if (!empty($duplicates)) {
            return [
                'message' => 'มีบาร์โค้ดซ้ำกันภายในสินค้าเดียวกัน',
                'duplicates' => array_values(array_unique($duplicates)),
            ];
        }

        $barcodes = array_values(array_unique(array_filter(array_values($normalizedByField))));
        if (empty($barcodes)) {
            return null;
        }

        // เช็คใน products table
        $query = DB::table('products');
        if ($excludeProductId) {
            $query->where('id', '!=', $excludeProductId);
        }
        $query->where(function ($q) use ($barcodes, $barcodeFields) {
            foreach ($barcodeFields as $field) {
                $q->orWhereIn($field, $barcodes);
            }
        });
        $found = $query->first();
        if ($found) {
            $dupBarcode = collect($barcodeFields)
                ->map(function ($field) use ($found) {
                    return $found->{$field} ?? null;
                })
                ->first(function ($value) use ($barcodes) {
                    return in_array($value, $barcodes, true);
                });

            return [
                'message' => "Barcode '{$dupBarcode}' ซ้ำกับสินค้า: {$found->trade_name}",
                'duplicates' => array_values(array_keys($normalizedByField, $dupBarcode, true)),
            ];
        }

        // เช็คใน product_units table
        $unitQuery = DB::table('product_units')
            ->whereIn('barcode', $barcodes);
        if ($excludeProductId) {
            $unitQuery->where('product_id', '!=', $excludeProductId);
        }
        $foundUnit = $unitQuery->first();
        if ($foundUnit) {
            $dupBarcode = (string) ($foundUnit->barcode ?? '');
            $productName = DB::table('products')
                ->where('id', $foundUnit->product_id)
                ->value('trade_name');

            return [
                'message' => "Barcode ซ้ำกับหน่วยสินค้าของ: {$productName}",
                'duplicates' => $dupBarcode !== ''
                    ? array_values(array_keys($normalizedByField, $dupBarcode, true))
                    : [],
            ];
        }

        return null;
    }

    private function logPriceChanges(int $productId, array $original, Product $product): void
    {
        $priceFields = [
            'retail' => 'price_retail',
            'wholesale1' => 'price_wholesale1',
            'wholesale2' => 'price_wholesale2',
        ];

        foreach ($priceFields as $type => $field) {
            $oldPrice = $original[$field] ?? 0;
            $newPrice = $product->{$field} ?? 0;

            if ((float) $oldPrice !== (float) $newPrice) {
                DB::table('price_logs')->insert([
                    'product_id' => $productId,
                    'price_type' => $type,
                    'old_price' => $oldPrice,
                    'new_price' => $newPrice,
                    'created_at' => now(),
                ]);
            }
        }
    }

    public function index()
    {
        $customers = Customer::where('id', '!=', 1)->get();
        return view('pos.index', compact('customers'));
    }

    public function receiveStockForm()
    {
        $products = Product::with('unit')
            ->where('is_disabled', false)
            ->orderBy('trade_name')
            ->get();

        $suppliers = Supplier::where('is_disabled', false)->orderBy('name')->get();

        $today = now()->format('Ymd');
        $count = Schema::hasColumn('product_lots', 'invoice_no')
            ? DB::table('product_lots')
                ->where('invoice_no', 'like', "PO-{$today}-%")
                ->count()
            : 0;

        $nextPoNumber = 'PO-' . $today . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        $hasSupplierInvoiceNo = Schema::hasColumn('product_lots', 'supplier_invoice_no');
        $hasPaymentType       = Schema::hasColumn('product_lots', 'payment_type');
        $hasIsPaid            = Schema::hasColumn('product_lots', 'is_paid');

        $allowedSortBy = ['created_at', 'invoice_no', 'supplier_name', 'total_value'];
        $sortBy  = in_array(request('sort_by'), $allowedSortBy) ? request('sort_by') : 'created_at';
        $sortDir = request('sort_dir') === 'asc' ? 'asc' : 'desc';

        $selectFields = [
            'product_lots.invoice_no',
            DB::raw('MIN(product_lots.created_at) as created_at'),
            DB::raw('suppliers.name as supplier_name'),
            DB::raw('COUNT(product_lots.id) as item_count'),
            DB::raw('SUM(product_lots.cost_price * product_lots.qty_received) as total_value'),
        ];
        if ($hasSupplierInvoiceNo) {
            $selectFields[] = DB::raw('MIN(product_lots.supplier_invoice_no) as supplier_invoice_no');
        }
        if ($hasPaymentType) {
            $selectFields[] = DB::raw('MIN(product_lots.payment_type) as payment_type');
        }
        if ($hasIsPaid) {
            $selectFields[] = DB::raw('MAX(product_lots.is_paid) as is_paid');
        }

        if (Schema::hasColumn('product_lots', 'invoice_no')) {
            $historyQuery = DB::table('product_lots')
                ->leftJoin('suppliers', 'product_lots.supplier_id', '=', 'suppliers.id')
                ->select($selectFields)
                ->groupBy('product_lots.invoice_no', 'suppliers.name');

            if (request('filter_date')) {
                $historyQuery->whereDate('product_lots.created_at', request('filter_date'));
            }
            if (request('filter_supplier_invoice')) {
                $historyQuery->where('product_lots.supplier_invoice_no', 'like', '%' . request('filter_supplier_invoice') . '%');
            }
            if (request('filter_supplier')) {
                $historyQuery->where('product_lots.supplier_id', request('filter_supplier'));
            }

            $orderByCol = match($sortBy) {
                'invoice_no'    => 'product_lots.invoice_no',
                'supplier_name' => DB::raw('suppliers.name'),
                'total_value'   => DB::raw('SUM(product_lots.cost_price * product_lots.qty_received)'),
                default         => DB::raw('MIN(product_lots.created_at)'),
            };

            $receiveHistory = $historyQuery->orderBy($orderByCol, $sortDir)->paginate(20)->withQueryString();
        } else {
            $receiveHistory = ProductLot::query()->whereRaw('1 = 0')->paginate(20);
        }

        return view('pos.receive_stock', compact('products', 'suppliers', 'receiveHistory', 'nextPoNumber'));
    }

    public function receiveStock(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'receive_date' => 'required|date',
            'invoice_no' => 'required|string|max:100',
            'supplier_invoice_no' => 'nullable|string|max:100',
            'payment_type' => 'required|in:cash,credit',
            'due_date' => 'nullable|date|required_if:payment_type,credit',
            'is_paid' => 'nullable|boolean',
            'paid_date' => 'nullable|date',

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
            'discount' => 'nullable|array',
            'discount.*' => 'nullable|numeric|min:0',
            'note' => 'nullable|array',
            'note.*' => 'nullable|string|max:255',
        ], [
            'due_date.required_if' => 'กรุณาระบุวันครบกำหนดชำระเมื่อเลือกการชำระเงินแบบเครดิต',
        ]);

        $rows = count($data['product_id']);
        $receiveAt = \Carbon\Carbon::parse($data['receive_date']);
        $paymentType = $data['payment_type'] ?? 'cash';
        $dueDate = $paymentType === 'credit' ? ($data['due_date'] ?? null) : null;
        $isPaid = $request->boolean('is_paid');
        $paidDate = $isPaid ? ($data['paid_date'] ?? $receiveAt->toDateString()) : null;

        $hasInvoiceNo = Schema::hasColumn('product_lots', 'invoice_no');
        $hasSupplierInvoiceNo = Schema::hasColumn('product_lots', 'supplier_invoice_no');
        $hasPaymentType = Schema::hasColumn('product_lots', 'payment_type');
        $hasDueDate = Schema::hasColumn('product_lots', 'due_date');
        $hasIsPaid = Schema::hasColumn('product_lots', 'is_paid');
        $hasPaidDate = Schema::hasColumn('product_lots', 'paid_date');

        DB::transaction(function () use (
            $data,
            $rows,
            $receiveAt,
            $paymentType,
            $dueDate,
            $isPaid,
            $paidDate,
            $hasInvoiceNo,
            $hasSupplierInvoiceNo,
            $hasPaymentType,
            $hasDueDate,
            $hasIsPaid,
            $hasPaidDate
        ) {
            for ($i = 0; $i < $rows; $i++) {
                $productId = $data['product_id'][$i];
                $lotNumber = $data['lot_number'][$i];
                $manufacturedDate = $data['manufactured_date'][$i] ?? null;
                $expiryDate = $data['expiry_date'][$i];
                $costPrice = (float) $data['cost_price'][$i];
                $sellPrice = (float) $data['sell_price'][$i];
                $qtyReceived = (int) $data['qty_received'][$i];
                $discount = (float) ($data['discount'][$i] ?? 0);
                $itemNote = $data['note'][$i] ?? null;

                if (!$productId || !$lotNumber || !$expiryDate || !$qtyReceived) {
                    continue;
                }

                $lot = ProductLot::firstOrNew([
                    'product_id' => $productId,
                    'lot_number' => $lotNumber,
                ]);

                $prevOnHand = (int) ($lot->exists ? $lot->qty_on_hand : 0);

                if (!$lot->exists) {
                    $lot->created_at = $receiveAt->copy();
                }

                $lot->supplier_id = $data['supplier_id'];
                if ($hasInvoiceNo) {
                    $lot->invoice_no = $data['invoice_no'];
                }
                if ($hasSupplierInvoiceNo) {
                    $lot->supplier_invoice_no = $data['supplier_invoice_no'] ?? null;
                }
                if ($hasPaymentType) {
                    $lot->payment_type = $paymentType;
                }
                if ($hasDueDate) {
                    $lot->due_date = $dueDate;
                }
                if ($hasIsPaid) {
                    $lot->is_paid = $isPaid;
                }
                if ($hasPaidDate) {
                    $lot->paid_date = $paidDate;
                }
                $lot->manufactured_date = $manufacturedDate;
                $lot->expiry_date = $expiryDate;
                $lot->cost_price = $costPrice;
                $lot->sell_price = $sellPrice;
                $lot->qty_received = ($lot->qty_received ?: 0) + $qtyReceived;
                $lot->qty_on_hand = ($lot->qty_on_hand ?: 0) + $qtyReceived;
                $lot->note = $itemNote;
                $lot->is_closed = false;
                $lot->updated_at = $receiveAt->copy();
                $lot->save();

                Product::whereKey($productId)->update([
                    'price_retail' => $sellPrice,
                    'updated_at' => now(),
                ]);

                $movementNote = collect([
                    'รับยาเข้าสต๊อค',
                    'เลขที่เอกสาร: ' . $data['invoice_no'],
                    !empty($data['supplier_invoice_no']) ? 'บิลผู้จำหน่าย: ' . $data['supplier_invoice_no'] : null,
                    $discount > 0 ? 'ส่วนลด: ' . number_format($discount, 2) : null,
                    $itemNote ? 'หมายเหตุ: ' . $itemNote : null,
                ])->filter()->implode(' | ');

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
                    'note' => $movementNote,
                    'created_by' => optional(auth()->user())->id,
                    'created_at' => $receiveAt->copy(),
                ]);
            }
        });

        return redirect()->route('pos.stock.receive')->with('success', 'บันทึกการรับสินค้าเรียบร้อยแล้ว');
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
                'product_lots.invoice_no',
                'product_lots.supplier_invoice_no',
                'product_lots.lot_number',
                'product_lots.expiry_date',
                'suppliers.name as supplier_name',
                'users.name as created_by_name',
            ]);

        // Filters
        if ($request->filled('invoice_no')) {
            $query->where('product_lots.invoice_no', $request->invoice_no);
        } elseif ($request->filled('received_at')) {
            $query->whereDate('stock_movements.created_at', \Carbon\Carbon::parse($request->received_at)->toDateString());
        }

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

        // Fetch bill header when filtering by invoice_no or received_at
        $billHeader = null;
        $hasPaymentType     = Schema::hasColumn('product_lots', 'payment_type');
        $hasDueDate         = Schema::hasColumn('product_lots', 'due_date');
        $hasIsPaid          = Schema::hasColumn('product_lots', 'is_paid');
        $hasPaidDate        = Schema::hasColumn('product_lots', 'paid_date');
        $hasSupplierInvoice = Schema::hasColumn('product_lots', 'supplier_invoice_no');
        $hasIsCancelled     = Schema::hasColumn('product_lots', 'is_cancelled');

        if ($request->filled('invoice_no') || $request->filled('received_at')) {
            $selectFields = [
                'product_lots.invoice_no',
                'product_lots.created_at',
                DB::raw('suppliers.name as supplier_name'),
                DB::raw('SUM(product_lots.cost_price * product_lots.qty_received) as total_value'),
                DB::raw('COUNT(product_lots.id) as item_count'),
            ];
            $groupByFields = [
                'product_lots.invoice_no',
                'product_lots.created_at',
                'suppliers.name',
            ];

            if ($hasSupplierInvoice) {
                $selectFields[]  = 'product_lots.supplier_invoice_no';
                $groupByFields[] = 'product_lots.supplier_invoice_no';
            }
            if ($hasPaymentType) {
                $selectFields[]  = 'product_lots.payment_type';
                $groupByFields[] = 'product_lots.payment_type';
            }
            if ($hasDueDate) {
                $selectFields[]  = 'product_lots.due_date';
                $groupByFields[] = 'product_lots.due_date';
            }
            if ($hasIsPaid) {
                $selectFields[]  = 'product_lots.is_paid';
                $groupByFields[] = 'product_lots.is_paid';
            }
            if ($hasPaidDate) {
                $selectFields[]  = 'product_lots.paid_date';
                $groupByFields[] = 'product_lots.paid_date';
            }
            if ($hasIsCancelled) {
                $selectFields[]  = 'product_lots.is_cancelled';
                $groupByFields[] = 'product_lots.is_cancelled';
            } else {
                $selectFields[] = DB::raw('0 as is_cancelled');
            }

            $billQuery = DB::table('product_lots')
                ->leftJoin('suppliers', 'product_lots.supplier_id', '=', 'suppliers.id')
                ->select($selectFields)
                ->groupBy($groupByFields);

            if ($request->filled('invoice_no')) {
                $billQuery->where('product_lots.invoice_no', $request->invoice_no);
            } else {
                $billQuery->whereDate('product_lots.created_at', \Carbon\Carbon::parse($request->received_at)->toDateString());
            }

            $billHeader = $billQuery->first();
        }


        return view('pos.receive_stock_history', compact('movements', 'suppliers', 'products', 'billHeader'));
    }

    public function updateBillMeta(Request $request)
    {
        $data = $request->validate([
            'invoice_no'          => 'nullable|string',
            'supplier_invoice_no' => 'nullable|string|max:100',
            'supplier_id'         => 'required|exists:suppliers,id',
            'receive_date'        => 'required|date',
            'payment_type'        => 'required|in:cash,credit',
            'due_date'            => 'nullable|date|required_if:payment_type,credit',
            'is_paid'             => 'nullable|boolean',
            'paid_date'           => 'nullable|date',
        ]);

        $isPaid   = $request->boolean('is_paid');
        $paidDate = $isPaid ? ($data['paid_date'] ?? null) : null;
        $dueDate  = $data['payment_type'] === 'credit' ? ($data['due_date'] ?? null) : null;

        $update = [
            'supplier_id' => $data['supplier_id'],
            'created_at'  => \Carbon\Carbon::parse($data['receive_date'])->startOfDay(),
            'updated_at'  => now(),
        ];

        if (Schema::hasColumn('product_lots', 'supplier_invoice_no')) {
            $update['supplier_invoice_no'] = $data['supplier_invoice_no'] ?? null;
        }
        if (Schema::hasColumn('product_lots', 'payment_type')) {
            $update['payment_type'] = $data['payment_type'];
        }
        if (Schema::hasColumn('product_lots', 'due_date')) {
            $update['due_date'] = $dueDate;
        }
        if (Schema::hasColumn('product_lots', 'is_paid')) {
            $update['is_paid'] = $isPaid;
        }
        if (Schema::hasColumn('product_lots', 'paid_date')) {
            $update['paid_date'] = $paidDate;
        }

        $invoiceNo = $data['invoice_no'] ?: null;

        $query = DB::table('product_lots');
        if ($invoiceNo === null) {
            $query->whereNull('invoice_no');
        } else {
            $query->where('invoice_no', $invoiceNo);
        }
        $query->update($update);

        return redirect()
            ->back()
            ->with('success', 'แก้ไขรายละเอียดบิลเรียบร้อยแล้ว');
    }


    public function cancelBill(Request $request)
    {
        $data = $request->validate([
            'invoice_no'  => 'required|string',
            'cancel_note' => 'nullable|string|max:500',
        ]);

        $invoiceNo = $data['invoice_no'];
        $cancelNote = $data['cancel_note'] ?? null;

        $lots = DB::table('product_lots')
            ->where('invoice_no', $invoiceNo)
            ->get();

        if ($lots->isEmpty()) {
            return back()->with('error', 'ไม่พบบิลที่ต้องการยกเลิก');
        }

        $hasIsCancelled = Schema::hasColumn('product_lots', 'is_cancelled');
        if ($hasIsCancelled && $lots->first()->is_cancelled) {
            return back()->with('error', 'บิลนี้ถูกยกเลิกไปแล้ว');
        }

        $warnings = [];

        DB::transaction(function () use ($lots, $invoiceNo, $cancelNote, $hasIsCancelled, &$warnings) {
            foreach ($lots as $lot) {
                $qtyReceived = (int) $lot->qty_received;
                $qtyOnHand   = (int) $lot->qty_on_hand;

                $qtyToReverse = max(0, min($qtyOnHand, $qtyReceived));

                if ($qtyToReverse < $qtyReceived) {
                    $soldQty = $qtyReceived - $qtyToReverse;
                    $warnings[] = "Lot {$lot->lot_number}: จำหน่ายไปแล้ว {$soldQty} หน่วย - หักคืนได้ {$qtyToReverse} หน่วย";
                }

                $newQtyOnHand = $qtyOnHand - $qtyToReverse;

                $lotUpdate = [
                    'qty_on_hand' => $newQtyOnHand,
                    'is_closed'   => true,
                    'updated_at'  => now(),
                ];
                if ($hasIsCancelled) {
                    $lotUpdate['is_cancelled'] = true;
                    $lotUpdate['cancelled_at'] = now();
                    $lotUpdate['cancel_note']  = $cancelNote;
                }

                DB::table('product_lots')->where('id', $lot->id)->update($lotUpdate);

                if ($qtyToReverse > 0) {
                    $movementNote = collect([
                        'ยกเลิกบิลรับสินค้า',
                        'เลขที่เอกสาร: ' . $invoiceNo,
                        $cancelNote ? 'หมายเหตุ: ' . $cancelNote : null,
                    ])->filter()->implode(' | ');

                    DB::table('stock_movements')->insert([
                        'product_id'    => $lot->product_id,
                        'lot_id'        => $lot->id,
                        'movement_type' => 'purchase_return',
                        'ref_type'      => 'bill_cancel',
                        'ref_id'        => null,
                        'qty_change'    => $qtyToReverse,
                        'qty_before'    => $qtyOnHand,
                        'qty_after'     => $newQtyOnHand,
                        'unit_cost'     => $lot->cost_price,
                        'note'          => $movementNote,
                        'created_by'    => optional(auth()->user())->id,
                        'created_at'    => now(),
                    ]);
                }
            }
        });

        $message = 'ยกเลิกบิล ' . $invoiceNo . ' เรียบร้อยแล้ว';
        if (!empty($warnings)) {
            $message .= ' (มีสินค้าบางส่วนที่จำหน่ายไปแล้ว: ' . implode(', ', $warnings) . ')';
        }

        return redirect()
            ->route('pos.stock.receive')
            ->with('success', $message);
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

    public function searchGenericName(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $rows = DrugGenericName::query()
            ->where('name', 'like', "%{$q}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($rows);
    }

    public function productIndex(Request $request)
    {
        $q = trim($request->get('q', ''));
        $category_id = $request->filled('category_id') ? (int) $request->get('category_id') : null;
        $generic_name = trim($request->get('generic_name', ''));
        $status = $request->get('status');

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

        if (!in_array($status, ['active', 'disabled'], true)) {
            $status = null;
        }

        $genericNameColumn = Schema::hasColumn('products', 'generic_name')
            ? 'generic_name'
            : 'search_keywords';

        $query = Product::with(['lots', 'category'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('trade_name', 'like', "%{$q}%")
                        ->orWhere('barcode', 'like', "%{$q}%")
                        ->orWhere('code', 'like', "%{$q}%");
                });
            })
            ->when($category_id, function ($query) use ($category_id) {
                $query->where('category_id', $category_id);
            })
            ->when($generic_name !== '', function ($query) use ($generic_name, $genericNameColumn) {
                $query->where($genericNameColumn, 'like', "%{$generic_name}%");
            })
            ->when($status === 'active', function ($query) {
                $query->where('is_disabled', false);
            })
            ->when($status === 'disabled', function ($query) {
                $query->where('is_disabled', true);
            });

        $totalCount = (clone $query)->count();

        $products = $query->orderBy($sort_by, $sort_dir)->paginate(20);

        $categories = ProductCategory::active()->orderBy('name')->get();

        return view('pos.products_index', compact(
            'products',
            'q',
            'sort_by',
            'sort_dir',
            'categories',
            'totalCount',
            'category_id',
            'generic_name',
            'status'
        ));
    }

    public function editProduct(Product $product)
    {
        $product->load(['productUnits', 'genericName']);
        $drugTypes   = DrugType::where('is_disabled', false)->orderBy('name_th')->get();
        $dosageForms = DosageForm::where('is_disabled', false)->orderBy('name_th')->get();
        $categories = ProductCategory::active()->get();
        $salesHistory = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sale_items.product_id', $product->id)
            ->orderByDesc('sales.sold_at')
            ->limit(20)
            ->select(
                'sales.sold_at',
                'sales.invoice_no',
                'sale_items.qty',
                'sale_items.unit_name',
                'sale_items.unit_price',
                'sale_items.line_total',
                'sale_items.is_cancelled'
            )
            ->get();

        $purchaseHistory = DB::table('product_lots')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'product_lots.supplier_id')
            ->where('product_lots.product_id', $product->id)
            ->orderBy(DB::raw('MAX(product_lots.created_at)'), 'desc')
            ->limit(20)
            ->select(
                'product_lots.id',
                'product_lots.lot_number',
                'product_lots.created_at',
                'product_lots.qty_received',
                'product_lots.cost_price',
                'product_lots.note',
                'suppliers.name as supplier_name'
            )
            ->get();

        $adjustmentHistory = DB::table('stock_movements')
            ->where('product_id', $product->id)
            ->whereIn('movement_type', ['adjustment_in', 'adjustment_out'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $insufficientHistory = DB::table('stock_movements')
            ->where('product_id', $product->id)
            ->where('movement_type', 'sale')
            ->where(function ($q) {
                $q->where('qty_before', '<=', 0)
                    ->orWhere('qty_after', '<', 0);
            })
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $priceHistory = DB::table('price_logs')
            ->where('product_id', $product->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $returnHistory = DB::table('stock_returns')
            ->where('stock_returns.product_id', $product->id)
            ->leftJoin('product_lots', 'product_lots.id', '=', 'stock_returns.lot_id')
            ->orderByDesc('stock_returns.created_at')
            ->select(
                'stock_returns.*',
                'product_lots.lot_number',
                'product_lots.expiry_date'
            )
            ->limit(20)
            ->get();

        $baseUnitName = optional($product->productUnits->firstWhere('is_base_unit', true))->unit_name
            ?? optional($product->productUnits->firstWhere('qty_per_base', '1.0000'))->unit_name
            ?? optional($product->productUnits->first())->unit_name
            ?? $product->unit_name
            ?? '';
            
        return view('pos.edit_product', compact(
            'product',
            'drugTypes',
            'dosageForms',
            'categories',
            'baseUnitName',
            'salesHistory',
            'purchaseHistory',
            'adjustmentHistory',
            'insufficientHistory',
            'priceHistory',
            'returnHistory'
        ));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $result = $this->saveProductData($request, $product);
        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'errors' => $result['errors'],
                'duplicate_fields' => $result['duplicate_fields'] ?? [],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'บันทึกสำเร็จ',
            'errors' => [],
        ]);
    }

    public function autoSaveProduct(Request $request, Product $product)
    {
        $result = $this->saveProductData($request, $product);
        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'errors' => $result['errors'],
                'duplicate_fields' => $result['duplicate_fields'] ?? [],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'บันทึกสำเร็จ',
            'errors' => [],
        ]);
    }

    private function saveProductData(Request $request, Product $product): array
    {
        $product->refresh();
        $original = $product->getOriginal();

        $validator = Validator::make($request->all(), [
            'barcode'           => 'nullable|string|max:50',
            'barcode2'          => 'nullable|string|max:50',
            'barcode3'          => 'nullable|string|max:50',
            'barcode4'          => 'nullable|string|max:50',
            'code'              => 'nullable|string|max:50|unique:products,code,' . $product->id,
            'trade_name'        => 'required|string|max:255',
            'name_for_print'    => 'nullable|string|max:255',
            'category_id'       => 'nullable|integer|exists:product_categories,id',
            'dosage_form_id'    => 'nullable|integer|exists:dosage_forms,id',
            'base_unit_name'    => 'required|string|max:50',
            'price_retail'      => 'required|numeric|min:0',
            'price_wholesale1'  => 'nullable|numeric|min:0',
            'price_wholesale2'  => 'nullable|numeric|min:0',
            'has_wholesale1'    => 'nullable|boolean',
            'has_wholesale2'    => 'nullable|boolean',
            'is_vat'            => 'nullable|boolean',
            'is_not_discount'   => 'nullable|boolean',
            'reorder_point'     => 'nullable|integer|min:0',
            'safety_stock'      => 'nullable|integer|min:0',
            'expiry_alert_days1'=> 'nullable|integer|min:1',
            'expiry_alert_days2'=> 'nullable|integer|min:1',
            'expiry_alert_days3'=> 'nullable|integer|min:1',
            'drug_type_id'      => 'nullable|integer|exists:drug_types,id',
            'drug_generic_name_id' => 'nullable|integer|exists:drug_generic_names,id',
            'strength'          => 'nullable|numeric|min:0',
            'registration_no'   => 'nullable|string|max:50',
            'tmt_id'            => 'nullable|string|max:30',
            'is_original_drug'  => 'nullable|boolean',
            'is_antibiotic'     => 'nullable|boolean',
            'max_dispense_qty'  => 'nullable|numeric|min:0',
            'default_qty'       => 'nullable|integer|min:1',
            'indication_note'   => 'nullable|string',
            'side_effect_note'  => 'nullable|string',
            'search_keywords'   => 'nullable|string',
            'is_fda_report'     => 'nullable|boolean',
            'is_fda11_report'   => 'nullable|boolean',
            'is_fda13_report'   => 'nullable|boolean',
            'is_sale_control'   => 'nullable|boolean',
            'sale_control_qty'  => 'nullable|numeric|min:0',
            'note'              => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'ข้อมูลไม่ถูกต้อง',
                'errors' => $validator->errors()->toArray(),
            ];
        }

        $barcodeError = $this->checkBarcodeUnique([
            $request->barcode,
            $request->barcode2,
            $request->barcode3,
            $request->barcode4,
        ], $product->id);

        if ($barcodeError) {
            return [
                'success' => false,
                'message' => $barcodeError['message'],
                'errors' => ['barcode' => [$barcodeError['message']]],
                'duplicate_fields' => $barcodeError['duplicates'] ?? [],
            ];
        }

        $fillableFields = [
            'barcode',
            'barcode2',
            'barcode3',
            'barcode4',
            'code',
            'trade_name',
            'name_for_print',
            'category_id',
            'dosage_form_id',
            'price_retail',
            'price_wholesale1',
            'price_wholesale2',
            'has_wholesale1',
            'has_wholesale2',
            'reorder_point',
            'safety_stock',
            'expiry_alert_days1',
            'expiry_alert_days2',
            'expiry_alert_days3',
            'drug_type_id',
            'drug_generic_name_id',
            'strength',
            'registration_no',
            'tmt_id',
            'max_dispense_qty',
            'default_qty',
            'indication_note',
            'side_effect_note',
            'sale_control_qty',
            'search_keywords',
            'note',
        ];

        $data = collect($validator->validated())
            ->only($fillableFields)
            ->toArray();

        $data['unit_name'] = $request->input('base_unit_name');

        $booleanFields = [
            'is_vat',
            'is_not_discount',
            'is_original_drug',
            'is_antibiotic',
            'is_fda_report',
            'is_fda11_report',
            'is_fda13_report',
            'is_sale_control',
        ];

        foreach ($booleanFields as $field) {
            if ($request->has($field)) {
                $data[$field] = $request->boolean($field);
            }
        }

        $hasWholesale1 = $request->boolean('has_wholesale1');
        $hasWholesale2 = $request->boolean('has_wholesale2');
        $data['price_wholesale1'] = !$hasWholesale1 ? 0 : (float) ($request->input('price_wholesale1') ?? 0);
        $data['price_wholesale2'] = !$hasWholesale2 ? 0 : (float) ($request->input('price_wholesale2') ?? 0);
        $data['default_qty'] = $data['default_qty'] ?? 1;
        $data['is_disabled'] = $request->boolean('is_disabled');

        $product->fill($data);
        $product->save();

        $wholesaleFlags = [];
        if (Schema::hasColumn('products', 'has_wholesale1')) {
            $wholesaleFlags['has_wholesale1'] = $hasWholesale1;
        }
        if (Schema::hasColumn('products', 'has_wholesale2')) {
            $wholesaleFlags['has_wholesale2'] = $hasWholesale2;
        }
        if (!empty($wholesaleFlags)) {
            $product->forceFill($wholesaleFlags)->save();
        }

        $baseUnitName = $request->input('base_unit_name');
        $product->productUnits()->updateOrCreate(
            ['is_base_unit' => true],
            [
                'unit_name'       => $baseUnitName,
                'qty_per_base'    => 1,
                'is_for_sale'     => true,
                'is_for_purchase' => true,
                'is_disabled'     => false,
                'price_retail'    => $product->price_retail ?? 0,
                'price_wholesale1'=> $product->price_wholesale1 ?? 0,
                'price_wholesale2'=> $product->price_wholesale2 ?? 0,
            ]
        );

        $product->refresh();
        $this->logPriceChanges($product->id, $original, $product);

        return [
            'success' => true,
            'message' => 'บันทึกสำเร็จ',
            'errors' => [],
        ];
    }

    public function stockReturn(Request $request, Product $product)
    {
        $validated = $request->validate([
            'lot_id' => 'required|integer|exists:product_lots,id',
            'qty' => 'required|integer|min:1',
            'reason' => 'required|string|in:ลูกค้าเปลี่ยนใจ,ยาผิด,ยาเสียหาย,หมดอายุ,อื่นๆ',
            'note' => 'nullable|string',
        ]);

        $lot = $product->lots()
            ->where('id', $validated['lot_id'])
            ->where('qty_on_hand', '>', 0)
            ->first();

        if (!$lot) {
            return back()->with('error', 'ไม่พบ lot ที่เลือก หรือ lot นี้ไม่สามารถรับคืนได้');
        }

        DB::transaction(function () use ($product, $lot, $validated) {
            $qtyChange = (int) $validated['qty'];
            $qtyBefore = (int) $lot->qty_on_hand;
            $lot->qty_on_hand = $qtyBefore + $qtyChange;
            $lot->save();

            $stockReturnId = DB::table('stock_returns')->insertGetId([
                'product_id' => $product->id,
                'lot_id' => $lot->id,
                'qty' => $qtyChange,
                'reason' => $validated['reason'],
                'note' => $validated['note'] ?? null,
                'created_at' => now(),
            ]);

            $note = trim((string) ($validated['note'] ?? ''));
            $movementNote = 'รับคืนสินค้า: ' . $validated['reason'];
            if ($note !== '') {
                $movementNote .= ' - ' . $note;
            }

            DB::table('stock_movements')->insert([
                'product_id' => $product->id,
                'lot_id' => $lot->id,
                'movement_type' => 'return',
                'ref_type' => 'stock_return',
                'ref_id' => $stockReturnId,
                'qty_change' => $qtyChange,
                'qty_before' => $qtyBefore,
                'qty_after' => (int) $lot->qty_on_hand,
                'unit_cost' => $lot->cost_price,
                'note' => $movementNote,
                'created_by' => optional(auth()->user())->id,
                'created_at' => now(),
            ]);
        });

        return redirect()->route('products.edit', $product)
            ->with('success', 'บันทึกรับคืนสินค้าเรียบร้อยแล้ว');
    }

    public function adjustStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'target_qty' => 'required|integer|min:0',
            'note' => 'nullable|string',
        ]);

        $currentQty = (int) $product->lots()->sum('qty_on_hand');
        $targetQty = (int) $validated['target_qty'];
        $diff = $targetQty - $currentQty;

        if ($diff === 0) {
            return response()->json([
                'success' => false,
                'message' => 'ยอดเท่าเดิม',
            ], 422);
        }

        $note = trim((string) ($validated['note'] ?? '')) ?: 'ปรับยอด';
        $reference = 'ADJ-' . now()->format('YmdHis');
        $fullNote = $note . ' [' . $reference . ']';

        if ($diff > 0) {
            $lot = $product->lots()
                ->whereDate('expiry_date', '>', now()->toDateString())
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->first();

            if (!$lot) {
                return response()->json([
                    'success' => false,
                    'message' => 'lot ล่าสุดหมดอายุแล้ว กรุณารับสินค้าเข้าใหม่แทน',
                ], 422);
            }

            DB::transaction(function () use ($product, $lot, $diff, $fullNote) {
                $qtyBefore = (int) $lot->qty_on_hand;
                $lot->qty_on_hand = $qtyBefore + $diff;
                $lot->save();

                DB::table('stock_movements')->insert([
                    'product_id' => $product->id,
                    'lot_id' => $lot->id,
                    'movement_type' => 'adjust_in',
                    'ref_type' => 'adjust_stock',
                    'ref_id' => null,
                    'qty_change' => $diff,
                    'qty_before' => $qtyBefore,
                    'qty_after' => (int) $lot->qty_on_hand,
                    'unit_cost' => $lot->cost_price,
                    'note' => $fullNote,
                    'created_by' => optional(auth()->user())->id,
                    'created_at' => now(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'ปรับสต็อคเรียบร้อยแล้ว',
                'current_qty' => $targetQty,
                'diff' => $diff,
            ]);
        }

        $remaining = abs($diff);
        $lots = $product->lots()
            ->where('qty_on_hand', '>', 0)
            ->orderBy('expiry_date')
            ->orderBy('id')
            ->get();

        try {
            DB::transaction(function () use ($product, $lots, &$remaining, $fullNote) {
                foreach ($lots as $lot) {
                    if ($remaining <= 0) {
                        break;
                    }

                    $qtyBefore = (int) $lot->qty_on_hand;
                    $qtyToDeduct = min($qtyBefore, $remaining);

                    if ($qtyToDeduct <= 0) {
                        continue;
                    }

                    $lot->qty_on_hand = $qtyBefore - $qtyToDeduct;
                    $lot->save();

                    DB::table('stock_movements')->insert([
                        'product_id' => $product->id,
                        'lot_id' => $lot->id,
                        'movement_type' => 'adjust_out',
                        'ref_type' => 'adjust_stock',
                        'ref_id' => null,
                        'qty_change' => $qtyToDeduct,
                        'qty_before' => $qtyBefore,
                        'qty_after' => (int) $lot->qty_on_hand,
                        'unit_cost' => $lot->cost_price,
                        'note' => $fullNote,
                        'created_by' => optional(auth()->user())->id,
                        'created_at' => now(),
                    ]);

                    $remaining -= $qtyToDeduct;
                }

                if ($remaining > 0) {
                    throw new \RuntimeException('สต็อคไม่พอ');
                }
            });
        } catch (\RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'ปรับสต็อคเรียบร้อยแล้ว',
            'current_qty' => $targetQty,
            'diff' => $diff,
        ]);
    }

    public function labels(Product $product)
    {
        $rows = DB::table('product_labels as pl')
            ->leftJoin('label_dosages as ld', 'ld.id', '=', 'pl.dosage_id')
            ->leftJoin('label_meal_relations as lmr', 'lmr.id', '=', 'pl.meal_relation_id')
            ->leftJoin('label_frequencies as lf', 'lf.id', '=', 'pl.frequency_id')
            ->leftJoin('label_times as lt', 'lt.id', '=', 'pl.label_time_id')
            ->leftJoin('label_advices as la', 'la.id', '=', 'pl.advice_id')
            ->leftJoin('products as p', 'p.id', '=', 'pl.product_id')
            ->where('pl.product_id', $product->id)
            ->orderBy('pl.sort_order')
            ->orderBy('pl.id')
            ->select([
                'pl.*',
                'ld.name_th as dosage_name',
                'ld.name_mm as dosage_name_mm',
                'ld.name_zh as dosage_name_zh',
                'lmr.name_th as meal_relation_name',
                'lmr.name_mm as meal_relation_name_mm',
                'lmr.name_zh as meal_relation_name_zh',
                'lf.name_th as frequency_name',
                'lf.name_mm as frequency_name_mm',
                'lf.name_zh as frequency_name_zh',
                'lt.name_th as label_time_name',
                'lt.name_mm as label_time_name_mm',
                'lt.name_zh as label_time_name_zh',
                'la.name_th as advice_name',
                'la.name_mm as advice_name_mm',
                'la.name_zh as advice_name_zh',
                'p.barcode as product_barcode',
            ])
            ->get();

        return response()->json($rows);
    }

    public function saveLabel(Request $request, Product $product)
    {
        $payload = $request->validate([
            'label_id' => 'nullable|integer|exists:product_labels,id',
            'label_name' => 'nullable|string|max:200',
            'dosage_id' => 'nullable|integer|exists:label_dosages,id',
            'frequency_id' => 'nullable|integer|exists:label_frequencies,id',
            'meal_relation_id' => 'nullable|integer|exists:label_meal_relations,id',
            'label_time_id' => 'nullable|integer|exists:label_times,id',
            'advice_id' => 'nullable|integer|exists:label_advices,id',
            'indication_th' => 'nullable|string',
            'indication_mm' => 'nullable|string',
            'indication_zh' => 'nullable|string',
            'show_barcode' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $labelId = (int) ($payload['label_id'] ?? 0);
        $label = null;

        if ($labelId > 0) {
            $label = ProductLabel::query()
                ->where('product_id', $product->id)
                ->where('id', $labelId)
                ->first();

            if (!$label) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบฉลากที่ต้องการแก้ไข',
                ], 404);
            }
        }

        $saveData = [
            'label_name' => $payload['label_name'] ?? null,
            'dosage_id' => $payload['dosage_id'] ?? null,
            'frequency_id' => $payload['frequency_id'] ?? null,
            'meal_relation_id' => $payload['meal_relation_id'] ?? null,
            'label_time_id' => $payload['label_time_id'] ?? null,
            'advice_id' => $payload['advice_id'] ?? null,
            'indication_th' => $payload['indication_th'] ?? null,
            'indication_mm' => $payload['indication_mm'] ?? null,
            'indication_zh' => $payload['indication_zh'] ?? null,
            'show_barcode' => $request->boolean('show_barcode'),
            'is_default' => $request->boolean('is_default'),
            'is_active' => $request->boolean('is_active', true),
        ];

        // Unset is_default for all other labels if this one is set as default
        if ($request->input('is_default') === '1') {
            \App\Models\ProductLabel::where('product_id', $product->id)
                ->update(['is_default' => 0]);
        }

        if ($label) {
            $label->fill($saveData);
            $label->save();
        } else {
            $maxSortOrder = (int) ProductLabel::where('product_id', $product->id)->max('sort_order');
            $saveData['product_id'] = $product->id;
            $saveData['sort_order'] = $maxSortOrder + 1;
            $label = ProductLabel::create($saveData);
        }

        return response()->json([
            'success' => true,
            'message' => 'บันทึกฉลากเรียบร้อยแล้ว',
            'data' => $label,
        ]);
    }

    public function updateLabel(Request $request, $productId, $labelId)
    {
        $label = \App\Models\ProductLabel::where('product_id', $productId)
            ->where('id', $labelId)
            ->firstOrFail();

        // Unset is_default for all other labels if this one is set as default
        if ($request->input('is_default') === '1') {
            \App\Models\ProductLabel::where('product_id', $label->product_id)
                ->where('id', '!=', $label->id)
                ->update(['is_default' => 0]);
        }

        $label->update([
            'label_name'      => $request->input('label_name'),
            'dosage_id'       => $request->input('dosage_id') ?: null,
            'frequency_id'    => $request->input('frequency_id') ?: null,
            'meal_relation_id'=> $request->input('meal_relation_id') ?: null,
            'label_time_id'   => $request->input('label_time_id') ?: null,
            'advice_id'       => $request->input('advice_id') ?: null,
            'indication_th'   => $request->input('indication_th'),
            'indication_mm'   => $request->input('indication_mm'),
            'indication_zh'   => $request->input('indication_zh'),
            'show_barcode'    => $request->input('show_barcode') === '1' ? 1 : 0,
            'is_default'      => $request->input('is_default') === '1' ? 1 : 0,
            'is_active'       => $request->input('is_active') === '1' ? 1 : 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'อัปเดตฉลากเรียบร้อยแล้ว',
            'label'   => $label->fresh(),
        ]);
    }

    public function deleteLabel(ProductLabel $label)
    {
        $label->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบฉลากเรียบร้อยแล้ว',
        ]);
    }

    public function toggleLabelActive(ProductLabel $label)
    {
        $label->is_active = !$label->is_active;
        $label->save();

        return response()->json([
            'success' => true,
            'is_active' => $label->is_active,
        ]);
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
            'barcode'           => 'nullable|string|max:50',
            'barcode2'          => 'nullable|string|max:50',
            'barcode3'          => 'nullable|string|max:50',
            'barcode4'          => 'nullable|string|max:50',
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

        $barcodeError = $this->checkBarcodeUnique([
            $data['barcode'] ?? null,
            $data['barcode2'] ?? null,
            $data['barcode3'] ?? null,
            $data['barcode4'] ?? null,
        ]);

        if ($barcodeError) {
            return back()->withErrors(['barcode' => $barcodeError['message']])->withInput();
        }

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
