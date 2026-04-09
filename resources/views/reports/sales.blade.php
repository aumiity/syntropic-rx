@extends('reports.layout')
@section('report_content')

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('reports.sales') }}"
          class="bg-white rounded-xl border border-slate-200 p-4 mb-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-slate-500 mb-1">ตั้งแต่วันที่</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}"
                class="h-10 px-3 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-emerald-400">
        </div>
        <div>
            <label class="block text-xs text-slate-500 mb-1">ถึงวันที่</label>
            <input type="date" name="date_to" value="{{ $dateTo }}"
                class="h-10 px-3 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-emerald-400">
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-slate-500 mb-1">ค้นหา</label>
            <input type="text" name="q" value="{{ $search }}" placeholder="เลขที่บิล, ชื่อลูกค้า..."
                class="w-full h-10 px-3 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-emerald-400">
        </div>
        <button type="submit"
            class="h-10 px-5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium">
            ค้นหา
        </button>
        <a href="{{ route('reports.sales') }}"
            class="h-10 px-4 rounded-lg border border-slate-200 text-slate-600 text-sm flex items-center hover:bg-slate-50">
            รีเซ็ต
        </a>
    </form>

    {{-- Summary Box --}}
    <div class="grid grid-cols-5 gap-3 mb-4">
        @foreach([
            ['label'=>'ราคารวม',    'value'=>$summary['subtotal'],       'color'=>'text-slate-700'],
            ['label'=>'ส่วนลดรวม', 'value'=>$summary['total_discount'],  'color'=>'text-amber-600'],
            ['label'=>'มูลค่ารวม', 'value'=>$summary['total_amount'],    'color'=>'text-emerald-600'],
            ['label'=>'ต้นทุนรวม', 'value'=>$summary['total_cost'],      'color'=>'text-red-500'],
            ['label'=>'กำไรรวม',   'value'=>$summary['total_profit'],    'color'=>'text-blue-600'],
        ] as $box)
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="text-xs text-slate-400 mb-1">{{ $box['label'] }}</div>
            <div class="text-xl font-extrabold {{ $box['color'] }}">
                ฿{{ number_format($box['value'], 2) }}
            </div>
        </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm divide-y divide-slate-100">
            <thead class="bg-slate-50 text-slate-600 text-xs font-semibold">
                <tr>
                    <th class="px-4 py-3 text-left">วัน/เวลา</th>
                    <th class="px-4 py-3 text-left">เลขที่บิล</th>
                    <th class="px-4 py-3 text-left">ลูกค้า</th>
                    <th class="px-4 py-3 text-right">ราคา</th>
                    <th class="px-4 py-3 text-right">ส่วนลด</th>
                    <th class="px-4 py-3 text-right">มูลค่ารวม</th>
                    <th class="px-4 py-3 text-right">ต้นทุน</th>
                    <th class="px-4 py-3 text-right">กำไร</th>
                    <th class="px-4 py-3 text-center">ดูบิล</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($sales as $sale)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-500 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($sale->sold_at)->format('d/m/y H:i') }}
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $sale->invoice_no }}</td>
                    <td class="px-4 py-3 text-slate-600">
                        {{ $sale->customer?->full_name ?? 'ลูกค้าทั่วไป' }}
                    </td>
                    <td class="px-4 py-3 text-right text-slate-700">
                        ฿{{ number_format($sale->subtotal, 2) }}
                    </td>
                    <td class="px-4 py-3 text-right text-amber-600">
                        {{ $sale->total_discount > 0 ? '฿'.number_format($sale->total_discount,2) : '0.00' }}
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-emerald-600">
                        ฿{{ number_format($sale->total_amount, 2) }}
                    </td>
                    <td class="px-4 py-3 text-right text-red-500">
                        ฿{{ number_format($sale->total_cost, 2) }}
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-blue-600">
                        ฿{{ number_format($sale->total_profit, 2) }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('reports.sales.show', $sale) }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-medium transition-colors">
                            ดูบิล
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-12 text-center text-slate-300">
                        ไม่พบข้อมูลการขาย
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection