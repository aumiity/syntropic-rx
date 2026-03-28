@extends('layouts.app')

@section('content')
<div class="p-6">
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
                    <th class="px-4 py-3 text-left">ID</th>
                    <th class="px-4 py-3 text-left">Barcode</th>
                    <th class="px-4 py-3 text-left">รหัส</th>
                    <th class="px-4 py-3 text-left">ชื่อ</th>
                    <th class="px-4 py-3 text-right">ราคาขาย</th>
                    <th class="px-4 py-3 text-right">สถานะ</th>
                    <th class="px-4 py-3 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="px-4 py-2">{{ $product->id }}</td>
                        <td class="px-4 py-2">{{ $product->barcode }}</td>
                        <td class="px-4 py-2">{{ $product->code ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $product->trade_name }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($product->price_retail, 2) }}</td>
                        <td class="px-4 py-2 text-right">{{ $product->is_disabled ? 'ปิดใช้งาน' : 'ใช้งาน' }}</td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center gap-1 px-2 py-1 bg-blue-500 text-white rounded-md text-xs hover:bg-blue-600">แก้ไข</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-slate-500">ไม่พบข้อมูลสินค้า</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->withQueryString()->links() }}
    </div>
</div>
@endsection
