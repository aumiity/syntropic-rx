@extends('reports.layout')
@section('report_content')

@php
    $fp = array_filter([
        'filter_date'             => request('filter_date'),
        'filter_supplier_invoice' => request('filter_supplier_invoice'),
        'filter_supplier'         => request('filter_supplier'),
    ]);
    function purchaseThSort($col, $label, $sortBy, $sortDir, $fp) {
        $dir  = ($sortBy === $col && $sortDir === 'asc') ? 'desc' : 'asc';
        $icon = $sortBy === $col ? ($sortDir === 'asc' ? ' ↑' : ' ↓') : '';
        return '<a href="' . route('reports.purchases', array_merge($fp, ['sort_by' => $col, 'sort_dir' => $dir])) . '" class="hover:text-emerald-600">' . $label . $icon . '</a>';
    }
@endphp

{{-- Filter --}}
<form method="GET" action="{{ route('reports.purchases') }}"
      class="bg-white rounded-xl border border-slate-200 p-4 mb-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs text-slate-500 mb-1">วันที่รับ</label>
        <input type="date" name="filter_date" value="{{ request('filter_date') }}"
            class="h-10 px-3 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-emerald-400">
    </div>
    <div>
        <label class="block text-xs text-slate-500 mb-1">เลขที่บิลผู้จำหน่าย</label>
        <input type="text" name="filter_supplier_invoice" value="{{ request('filter_supplier_invoice') }}"
            placeholder="เลขที่บิลผู้จำหน่าย..."
            class="h-10 px-3 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-emerald-400">
    </div>
    <div>
        <label class="block text-xs text-slate-500 mb-1">ผู้จำหน่าย</label>
        <select name="filter_supplier"
            class="h-10 px-3 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-emerald-400">
            <option value="">ทั้งหมด</option>
            @foreach($suppliers as $s)
                <option value="{{ $s->id }}" {{ request('filter_supplier') == $s->id ? 'selected' : '' }}>
                    {{ $s->name }}
                </option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="h-10 px-5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium">ค้นหา</button>
    <a href="{{ route('reports.purchases') }}" class="h-10 px-4 rounded-lg border border-slate-200 text-slate-600 text-sm flex items-center hover:bg-slate-50">รีเซ็ต</a>
    <a href="{{ route('pos.stock.receive') }}" class="h-10 px-5 rounded-lg bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium flex items-center gap-1 ml-auto">
        + รับสินค้าใหม่
    </a>
</form>

{{-- Table --}}
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="min-w-full text-sm divide-y divide-slate-100">
        <thead class="bg-slate-50 text-slate-600 text-xs font-semibold">
            <tr>
                <th class="px-4 py-3 text-left">{!! purchaseThSort('created_at','วันที่รับ',$sortBy,$sortDir,$fp) !!}</th>
                <th class="px-4 py-3 text-left">{!! purchaseThSort('invoice_no','เลขที่เอกสาร',$sortBy,$sortDir,$fp) !!}</th>
                @if($hasSupplierInvoiceNo)
                <th class="px-4 py-3 text-left">เลขที่บิลผู้จำหน่าย</th>
                @endif
                <th class="px-4 py-3 text-left">{!! purchaseThSort('supplier_name','ผู้จำหน่าย',$sortBy,$sortDir,$fp) !!}</th>
                <th class="px-4 py-3 text-center">รายการ</th>
                @if($hasPaymentType)
                <th class="px-4 py-3 text-left">การชำระเงิน</th>
                @endif
                @if($hasIsPaid)
                <th class="px-4 py-3 text-left">สถานะ</th>
                @endif
                <th class="px-4 py-3 text-right">{!! purchaseThSort('total_value','มูลค่ารวม',$sortBy,$sortDir,$fp) !!}</th>
                <th class="px-4 py-3 text-center">จัดการ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($receiveHistory as $history)
            @php
                $detailParams = !empty($history->invoice_no)
                    ? ['invoice_no' => $history->invoice_no]
                    : ['received_at' => $history->created_at];
            @endphp
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-500">
                    {{ $history->created_at ? \Carbon\Carbon::parse($history->created_at)->format('d/m/Y') : '-' }}
                </td>
                <td class="px-4 py-3 font-medium text-slate-800">{{ $history->invoice_no ?: '-' }}</td>
                @if($hasSupplierInvoiceNo)
                <td class="px-4 py-3 text-slate-600">{{ $history->supplier_invoice_no ?? '-' }}</td>
                @endif
                <td class="px-4 py-3 text-slate-700">{{ $history->supplier_name ?: '-' }}</td>
                <td class="px-4 py-3 text-center">{{ number_format($history->item_count ?? 0) }}</td>
                @if($hasPaymentType)
                <td class="px-4 py-3 text-slate-700">
                    {{ isset($history->payment_type) ? ($history->payment_type === 'credit' ? 'เครดิต' : 'เงินสด') : '-' }}
                </td>
                @endif
                @if($hasIsPaid)
                <td class="px-4 py-3">
                    @if(isset($history->is_paid))
                        @if($history->is_paid)
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs text-emerald-700">ชำระแล้ว</span>
                        @else
                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs text-amber-700">ค้างชำระ</span>
                        @endif
                    @else
                        <span class="text-xs text-slate-400">-</span>
                    @endif
                </td>
                @endif
                <td class="px-4 py-3 text-right font-medium text-slate-800">
                    ฿{{ number_format((float)($history->total_value ?? 0), 2) }}
                </td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('pos.stock.receive.history', $detailParams) }}"
                       class="inline-flex px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 text-xs font-medium">
                        ดูรายละเอียด
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-4 py-12 text-center text-slate-300">ยังไม่มีประวัติการรับสินค้า</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $receiveHistory->links() }}</div>

@endsection