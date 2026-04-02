@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50 p-4">
    <div class="max-w-7xl mx-auto bg-white rounded-xl shadow-md p-5">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-2xl font-bold">ประวัติการรับสินค้า</h1>
                <p class="text-slate-500">รายการรับยาเข้าสต๊อคทั้งหมด</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pos.stock.receive') }}" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600">รับสินค้าใหม่</a>
                <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300">กลับ POS</a>
            </div>
        </div>

        {{-- Filter Form --}}
        <form method="GET" class="mb-4 p-4 bg-slate-50 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">วันที่จาก</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded border border-slate-300 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">ถึงวันที่</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded border border-slate-300 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">ผู้จำหน่าย</label>
                    <select name="supplier_id" class="w-full rounded border border-slate-300 px-3 py-2">
                        <option value="">ทั้งหมด</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">สินค้า</label>
                    <select name="product_id" class="w-full rounded border border-slate-300 px-3 py-2">
                        <option value="">ทั้งหมด</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->trade_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">ค้นหา</button>
                </div>
            </div>
        </form>

        {{-- Summary --}}
        @php
            $totalQty = $movements->sum('qty_change');
            $totalValue = $movements->sum(fn($m) => $m->qty_change * $m->unit_cost);
        @endphp
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg">
            <div class="flex gap-6 text-sm">
                <div><strong>จำนวนรวม:</strong> {{ number_format($totalQty) }}</div>
                <div><strong>มูลค่ารวม:</strong> {{ number_format($totalValue, 2) }} บาท</div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border border-slate-200">
                <thead class="bg-slate-100 text-slate-700 text-sm">
                    <tr>
                        <th class="px-3 py-2">วันที่</th>
                        <th class="px-3 py-2">สินค้า</th>
                        <th class="px-3 py-2">Lot</th>
                        <th class="px-3 py-2">ผู้จำหน่าย</th>
                        <th class="px-3 py-2">จำนวน</th>
                        <th class="px-3 py-2">ราคาทุน</th>
                        <th class="px-3 py-2">รวม</th>
                        <th class="px-3 py-2">ผู้บันทึก</th>
                        <th class="px-3 py-2">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movements as $movement)
                        <tr class="border-t border-slate-200">
                            <td class="px-3 py-2 text-sm">{{ \Carbon\Carbon::parse($movement->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="px-3 py-2 text-sm">
                                {{ $movement->trade_name }}<br>
                                <small class="text-slate-500">{{ $movement->barcode ?? $movement->code }}</small>
                            </td>
                            <td class="px-3 py-2 text-sm">
                                {{ $movement->lot_number }}<br>
                                <small class="text-slate-500">หมดอายุ: {{ $movement->expiry_date ? \Carbon\Carbon::parse($movement->expiry_date)->format('d/m/Y') : '-' }}</small>
                            </td>
                            <td class="px-3 py-2 text-sm">{{ $movement->supplier_name ?? '-' }}</td>
                            <td class="px-3 py-2 text-sm text-center">{{ number_format($movement->qty_change) }}</td>
                            <td class="px-3 py-2 text-sm text-right">{{ number_format($movement->unit_cost, 2) }}</td>
                            <td class="px-3 py-2 text-sm text-right">{{ number_format($movement->qty_change * $movement->unit_cost, 2) }}</td>
                            <td class="px-3 py-2 text-sm">{{ $movement->created_by_name ?? '-' }}</td>
                            <td class="px-3 py-2 text-sm">{{ $movement->note }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $movements->appends(request()->query())->links() }}
        </div>
    </div>
</div>

@endsection