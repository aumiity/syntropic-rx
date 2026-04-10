@extends('layouts.app')

@section('content')
<div class="p-6 w-full mx-auto">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">หน้าจอจัดการสินค้า</h1>
            <p class="text-slate-600 text-sm">ทั้งหมด {{ number_format($totalCount ?? 0) }} รายการ</p>
            <p class="text-slate-600 text-sm">ค้นหาและดูรายการสินค้าในระบบ</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pos.products.create') }}" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600">สร้างสินค้าใหม่</a>
            <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 rounded-lg">กลับ POS</a>
        </div>
    </div>

    <form action="{{ route('products.index') }}" method="GET" class="mb-4 bg-white border border-slate-200 rounded-xl p-4">
        <input type="hidden" name="sort_by" value="{{ $sort_by ?? 'id' }}">
        <input type="hidden" name="sort_dir" value="{{ $sort_dir ?? 'desc' }}">

        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-6">
                <input
                    type="search"
                    name="q"
                    value="{{ old('q', $q) }}"
                    placeholder="รหัส, ชื่อ, บาร์โค้ด"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2"
                >
            </div>

            <div class="md:col-span-3">
                <select name="category_id" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                    <option value="">ประเภทสินค้า: ทั้งหมด</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $category_id === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3">
                <select name="status" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                    <option value="">สถานะ: ทั้งหมด</option>
                    <option value="active" @selected($status === 'active')>ใช้งาน</option>
                    <option value="disabled" @selected($status === 'disabled')>ระงับ</option>
                </select>
            </div>

            <div class="md:col-span-6">
                <input
                    type="text"
                    name="generic_name"
                    value="{{ old('generic_name', $generic_name) }}"
                    placeholder="ชื่อสามัญทางยา"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2"
                >
            </div>

            <div class="md:col-span-3">
                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-emerald-500 text-white hover:bg-emerald-600">ค้นหา</button>
            </div>

            <div class="md:col-span-3">
                <a href="{{ route('products.index') }}" class="w-full inline-flex justify-center px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700">เคลียร์</a>
            </div>
        </div>
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
                            'id', 'code', 'trade_name', 'category', 'cost_price', 'price_retail', 'margin', 'status', 'actions'
                        ];
                    @endphp
                    @foreach($columns as $col)
                        @if(isset($sortable[$col]))
                            @php
                                $isActive = $sort_by === $col;
                                $nextDir = ($isActive && $sort_dir === 'desc') ? 'asc' : 'desc';
                                $arrow = $isActive ? ($sort_dir === 'desc' ? '↑' : '↓') : '';
                                $query = array_filter([
                                    'q' => $q ?? null,
                                    'category_id' => $category_id ?? null,
                                    'generic_name' => $generic_name ?? null,
                                    'status' => $status ?? null,
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
                        @elseif($col === 'category')
                            <th class="px-4 py-3 text-left">ประเภท</th>
                        @elseif($col === 'margin')
                            <th class="px-4 py-3 text-right">อัตรากำไร</th>
                        @elseif($col === 'status')
                            <th class="px-4 py-3 text-center">สถานะ</th>
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
                        <td class="px-4 py-2">{{ $product->category->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($product->cost_price ?? 0, 2) }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($product->price_retail, 2) }}</td>
                        <td class="px-4 py-2 text-right whitespace-nowrap">
                            @php
                                $cost = $product->cost_price ?? 0;
                                $profit = $product->price_retail - $cost;
                                $profit_percent_cost = $cost > 0 ? ($profit / $cost) * 100 : 0;
                                $profit_percent_sale = $product->price_retail > 0 ? ($profit / $product->price_retail) * 100 : 0;
                            @endphp
                            ฿{{ number_format($profit, 2) }} | เทียบทุน:{{ number_format($profit_percent_cost, 0) }}%
                        </td>
                        <td class="px-4 py-2 text-center">
                            @if($product->is_disabled)
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium bg-red-100 text-red-600">ปิดใช้งาน ***</span>
                            @else
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium bg-emerald-100 text-emerald-700">ใช้งาน</span>
                            @endif
                        </td>
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
        {{ $products->appends([
            'q' => $q,
            'category_id' => $category_id,
            'generic_name' => $generic_name,
            'status' => $status,
            'sort_by' => $sort_by,
            'sort_dir' => $sort_dir,
        ])->withQueryString()->links() }}
    </div>
</div>
@endsection
