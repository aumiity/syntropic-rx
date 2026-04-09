@extends('reports.layout')
@section('report_content')

    {{-- Back + Header --}}
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('reports.sales') }}"
           class="text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">บิล {{ $sale->invoice_no }}</h2>
        <span class="px-3 py-1 rounded-full text-xs font-semibold
            {{ $sale->status === 'voided' ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-700' }}">
            {{ $sale->status === 'voided' ? 'ยกเลิกแล้ว' : 'สำเร็จ' }}
        </span>
    </div>

    {{-- Bill Info --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 mb-4 grid grid-cols-2 gap-4 text-sm">
        <div><span class="text-slate-400">วันที่/เวลา:</span> <span class="font-medium">{{ \Carbon\Carbon::parse($sale->sold_at)->format('d/m/Y H:i:s') }}</span></div>
        <div><span class="text-slate-400">ลูกค้า:</span> <span class="font-medium">{{ $sale->customer?->full_name ?? 'ลูกค้าทั่วไป' }}</span></div>
        <div><span class="text-slate-400">ประเภทการขาย:</span> <span class="font-medium">{{ $sale->sale_type }}</span></div>
        <div><span class="text-slate-400">วิธีชำระ:</span>
            <span class="font-medium">
                @if($sale->cash_amount > 0) เงินสด ฿{{ number_format($sale->cash_amount,2) }} @endif
                @if($sale->transfer_amount > 0) โอนเงิน ฿{{ number_format($sale->transfer_amount,2) }} @endif
                @if($sale->card_amount > 0) บัตร ฿{{ number_format($sale->card_amount,2) }} @endif
            </span>
        </div>
        @if($sale->change_amount > 0)
        <div><span class="text-slate-400">เงินทอน:</span> <span class="font-medium">฿{{ number_format($sale->change_amount,2) }}</span></div>
        @endif
        @if($sale->note)
        <div class="col-span-2"><span class="text-slate-400">หมายเหตุ:</span> <span>{{ $sale->note }}</span></div>
        @endif
        @if($sale->void_reason)
        <div class="col-span-2"><span class="text-red-400">เหตุผลยกเลิก:</span> <span class="text-red-600 font-medium">{{ $sale->void_reason }}</span></div>
        @endif
    </div>

    {{-- Items Table --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-4">
        <table class="min-w-full text-sm divide-y divide-slate-100">
            <thead class="bg-slate-50 text-slate-600 text-xs font-semibold">
                <tr>
                    <th class="px-4 py-3 text-left">รายการ</th>
                    <th class="px-4 py-3 text-center">หน่วย</th>
                    <th class="px-4 py-3 text-right">จำนวน</th>
                    <th class="px-4 py-3 text-right">ราคา/หน่วย</th>
                    <th class="px-4 py-3 text-right">ส่วนลด</th>
                    <th class="px-4 py-3 text-right">รวม</th>
                    <th class="px-4 py-3 text-right">ต้นทุน</th>
                    <th class="px-4 py-3 text-right">กำไร</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($sale->saleItems as $item)
                <tr class="{{ $item->is_cancelled ? 'opacity-40 line-through' : '' }}">
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $item->item_name }}</td>
                    <td class="px-4 py-3 text-center text-slate-500">{{ $item->unit_name ?? '-' }}</td>
                    <td class="px-4 py-3 text-right">{{ $item->qty }}</td>
                    <td class="px-4 py-3 text-right">฿{{ number_format($item->unit_price,2) }}</td>
                    <td class="px-4 py-3 text-right text-amber-600">{{ $item->discount > 0 ? '฿'.number_format($item->discount,2) : '-' }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-emerald-600">฿{{ number_format($item->line_total,2) }}</td>
                    <td class="px-4 py-3 text-right text-red-500">฿{{ number_format($item->line_cost,2) }}</td>
                    <td class="px-4 py-3 text-right text-blue-600">฿{{ number_format($item->line_profit,2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Totals --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 mb-4 flex justify-end">
        <div class="w-64 space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-slate-500">ราคารวม</span><span>฿{{ number_format($sale->subtotal,2) }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">ส่วนลด</span><span class="text-amber-600">฿{{ number_format($sale->total_discount,2) }}</span></div>
            <div class="flex justify-between font-bold text-base border-t border-slate-100 pt-2">
                <span>มูลค่ารวม</span><span class="text-emerald-600">฿{{ number_format($sale->total_amount,2) }}</span>
            </div>
        </div>
    </div>

    {{-- Void --}}
    @if($sale->status !== 'voided')
    <div class="bg-white rounded-xl border border-red-100 p-5">
        <h3 class="text-sm font-semibold text-red-600 mb-3">ยกเลิกบิล</h3>
        <form method="POST" action="{{ route('reports.sales.void', $sale) }}"
              onsubmit="return confirm('ยืนยันการยกเลิกบิล {{ $sale->invoice_no }} ?\nstock จะถูกคืนทั้งหมด')">
            @csrf
            <div class="flex gap-3">
                <input type="text" name="void_reason" required placeholder="ระบุเหตุผลการยกเลิก..."
                    class="flex-1 h-10 px-3 rounded-lg border border-red-200 text-sm focus:outline-none focus:border-red-400">
                <button type="submit"
                    class="px-5 h-10 rounded-lg bg-red-500 hover:bg-red-600 text-white text-sm font-medium">
                    ยกเลิกบิล
                </button>
            </div>
        </form>
    </div>
    @endif

@endsection