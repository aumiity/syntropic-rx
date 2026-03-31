@extends('layouts.app')

@section('content')
<div class="p-6 w-[1600px] mx-auto">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">หน้าจอจัดการสินค้า</h1>
            <p class="text-slate-600 text-sm">ค้นหาและดูรายการสินค้าในระบบ</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pos.products.create') }}" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600">สร้างสินค้าใหม่</a>
            <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 rounded-lg">กลับ POS</a>
        </div>
    </div>

    <form action="{{ route('products.index') }}" method="GET" class="mb-4 flex gap-2 items-center">
        <input type="search" name="q" value="{{ old('q', $q) }}" placeholder="ค้นหา ชื่อ, บาร์โค้ด, รหัส" class="border border-slate-300 rounded-lg px-3 py-2 w-full">
        <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-500 text-white">ค้นหา</button>
    </form>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-slate-600">
                <tr>
                    @php
                        $sortable = [
                            'id' => 'ID',
                            'code' => 'รหัส',
                            'trade_name' => 'ชื่อ',
                        ];
                        $columns = [
                            'id', 'code', 'trade_name', 'cost_price', 'price_retail', 'profit', 'profit_percent', 'status', 'actions'
                        ];
                    @endphp
                    @foreach($columns as $col)
                        @if(isset($sortable[$col]))
                            @php
                                $isActive = $sort_by === $col;
                                $nextDir = ($isActive && $sort_dir === 'asc') ? 'desc' : 'asc';
                                $arrow = $isActive ? ($sort_dir === 'asc' ? '↑' : '↓') : '';
                                $query = array_filter([
                                    'q' => $q ?? null,
                                    'sort_by' => $col,
                                    'sort_dir' => $nextDir
                                ]);
                            @endphp
                            <th class="px-4 py-3 text-left cursor-pointer">
                                <a href="?{{ http_build_query($query) }}" class="hover:underline">
                                    {{ $sortable[$col] }} {!! $arrow !!}
                                </a>
                            </th>
                        @elseif($col === 'cost_price')
                            <th class="px-4 py-3 text-right">ราคาทุน</th>
                        @elseif($col === 'price_retail')
                            <th class="px-4 py-3 text-right">ราคาขาย</th>
                        @elseif($col === 'profit')
                            <th class="px-4 py-3 text-right">กำไร(บาท)</th>
                        @elseif($col === 'profit_percent')
                            <th class="px-4 py-3 text-right">กำไรเทียบทุน(%)</th>
                        @elseif($col === 'status')
                            <th class="px-4 py-3 text-right">สถานะ</th>
                        @elseif($col === 'actions')
                            <th class="px-4 py-3 text-center">จัดการ</th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="px-4 py-2">{{ $product->id }}</td>
                        <td class="px-4 py-2">{{ $product->code ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $product->trade_name }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($product->lots?->avg('cost_price') ?? 0, 2) }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($product->price_retail, 2) }}</td>
                        <td class="px-4 py-2 text-right">
                            @php
                                $cost = $product->lots?->avg('cost_price') ?? 0;
                                $profit = $product->price_retail - $cost;
                            @endphp
                            {{ number_format($profit, 2) }}
                        </td>
                        <td class="px-4 py-2 text-right">
                            @php
                                $profit_percent = $cost > 0 ? ($profit / $cost) * 100 : 0;
                            @endphp
                            {{ number_format($profit_percent, 2) }}%
                        </td>
                        <td class="px-4 py-2 text-right">{{ $product->is_disabled ? 'ปิดใช้งาน' : 'ใช้งาน' }}</td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center gap-1 px-2 py-1 bg-blue-500 text-white rounded-md text-xs hover:bg-blue-600">แก้ไข</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-6 text-center text-slate-500">ไม่พบข้อมูลสินค้า</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Removed client-side sortTable() script, now using server-side sorting --}}

    <div class="mt-4">
        {{ $products->withQueryString()->links() }}
    </div>
</div>
@endsection
