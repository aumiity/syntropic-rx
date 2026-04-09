<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleItemLot;
use App\Models\ProductLot;
use App\Models\Supplier;
use Illuminate\Support\Facades\Schema;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->toDateString());
        $dateTo   = $request->input('date_to',   now()->toDateString());
        $search   = trim($request->input('q', ''));

        $allowedSortBy = ['sold_at', 'invoice_no', 'subtotal', 'total_discount', 'total_amount'];
        $sortBy  = in_array($request->input('sort_by'), $allowedSortBy) ? $request->input('sort_by') : 'sold_at';
        $sortDir = $request->input('sort_dir') === 'asc' ? 'asc' : 'desc';

        $query = Sale::with(['saleItems.saleItemLots.lot', 'customer'])
            ->whereDate('sold_at', '>=', $dateFrom)
            ->whereDate('sold_at', '<=', $dateTo)
            ->where('status', '!=', 'voided');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($q2) => $q2->where('full_name', 'like', "%{$search}%"));
            });
        }

        $query->orderBy($sortBy, $sortDir);
        $sales = $query->get();

        // คำนวณ cost จาก sale_item_lots → product_lots.cost_price
        $sales->each(function ($sale) {
            $cost = 0;
            foreach ($sale->saleItems as $item) {
                foreach ($item->saleItemLots as $sil) {
                    $lotCost = $sil->lot ? (float)$sil->lot->cost_price : 0;
                    $cost += $lotCost * (float)$sil->qty;
                }
            }
            $sale->total_cost   = $cost;
            $sale->total_profit = (float)$sale->total_amount - $cost;
        });

        $summary = [
            'subtotal'       => $sales->sum('subtotal'),
            'total_discount' => $sales->sum('total_discount'),
            'total_amount'   => $sales->sum('total_amount'),
            'total_cost'     => $sales->sum('total_cost'),
            'total_profit'   => $sales->sum('total_profit'),
        ];

        return view('reports.sales', compact('sales', 'summary', 'dateFrom', 'dateTo', 'search', 'sortBy', 'sortDir'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['saleItems.saleItemLots.lot', 'customer']);

        $sale->saleItems->each(function ($item) {
            $cost = 0;
            foreach ($item->saleItemLots as $sil) {
                $lotCost = $sil->lot ? (float)$sil->lot->cost_price : 0;
                $cost += $lotCost * (float)$sil->qty;
            }
            $item->line_cost   = $cost;
            $item->line_profit = (float)$item->line_total - $cost;
        });

        return view('reports.sales_show', compact('sale'));
    }

    public function void(Request $request, Sale $sale)
    {
        if ($sale->status === 'voided') {
            return back()->with('error', 'บิลนี้ถูกยกเลิกแล้ว');
        }

        $reason = trim($request->input('void_reason', ''));
        if (!$reason) {
            return back()->with('error', 'กรุณาระบุเหตุผลในการยกเลิก');
        }

        DB::transaction(function () use ($sale, $reason) {
            // คืน stock กลับทุก lot
            foreach ($sale->saleItems()->with('saleItemLots')->get() as $item) {
                foreach ($item->saleItemLots as $sil) {
                    ProductLot::where('id', $sil->lot_id)
                        ->increment('qty_on_hand', $sil->qty);
                    $sil->update(['is_cancelled' => true]);
                }
                $item->update(['is_cancelled' => true]);
            }

            $sale->update([
                'status'      => 'voided',
                'void_reason' => $reason,
            ]);
        });

        return redirect()->route('reports.sales')->with('success', "ยกเลิกบิล {$sale->invoice_no} เรียบร้อยแล้ว");
    }

    public function purchaseHistory(Request $request)
    {
        $suppliers = Supplier::where('is_disabled', false)->orderBy('name')->get();

        $allowedSortBy = ['created_at', 'invoice_no', 'supplier_name', 'total_value'];
        $sortBy  = in_array($request->input('sort_by'), $allowedSortBy) ? $request->input('sort_by') : 'created_at';
        $sortDir = $request->input('sort_dir') === 'asc' ? 'asc' : 'desc';

        $hasSupplierInvoiceNo = Schema::hasColumn('product_lots', 'supplier_invoice_no');
        $hasPaymentType       = Schema::hasColumn('product_lots', 'payment_type');
        $hasIsPaid            = Schema::hasColumn('product_lots', 'is_paid');

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

        $historyQuery = DB::table('product_lots')
            ->leftJoin('suppliers', 'product_lots.supplier_id', '=', 'suppliers.id')
            ->select($selectFields)
            ->groupBy('product_lots.invoice_no', 'suppliers.name');

        if ($request->input('filter_date')) {
            $historyQuery->whereDate('product_lots.created_at', $request->input('filter_date'));
        }
        if ($request->input('filter_supplier')) {
            $historyQuery->where('product_lots.supplier_id', $request->input('filter_supplier'));
        }
        if ($request->input('filter_supplier_invoice') && $hasSupplierInvoiceNo) {
            $historyQuery->where('product_lots.supplier_invoice_no', 'like',
                '%' . $request->input('filter_supplier_invoice') . '%');
        }

        $orderCol = match ($sortBy) {
            'supplier_name' => DB::raw('MIN(suppliers.name)'),
            'total_value'   => DB::raw('SUM(product_lots.cost_price * product_lots.qty_received)'),
            'invoice_no'    => DB::raw('MIN(product_lots.invoice_no)'),
            default         => DB::raw('MIN(product_lots.created_at)'),
        };
        $historyQuery->orderBy($orderCol, $sortDir);

        $receiveHistory = $historyQuery->paginate(20)->withQueryString();

        return view('reports.purchases', compact(
            'receiveHistory', 'suppliers',
            'sortBy', 'sortDir',
            'hasSupplierInvoiceNo', 'hasPaymentType', 'hasIsPaid'
        ));
    }
}