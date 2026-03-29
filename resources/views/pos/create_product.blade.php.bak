@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-bold">เพิ่มสินค้าใหม่</h1>
        <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 rounded-lg">กลับหน้า POS</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded-lg bg-emerald-100 border border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-800">
            <strong>พบข้อผิดพลาด:</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 max-w-2xl">
        <form action="{{ route('pos.products.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700">Barcode <span class="text-red-500">*</span></label>
                <input type="text" name="barcode" value="{{ old('barcode') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">รหัสสินค้า</label>
                <input type="text" name="code" value="{{ old('code') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">ชื่อสินค้า (trade name) <span class="text-red-500">*</span></label>
                <input type="text" name="trade_name" value="{{ old('trade_name') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">ชื่อพิมพ์</label>
                <input type="text" name="name_for_print" value="{{ old('name_for_print') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">ราคาขาย (Retail) <span class="text-red-500">*</span></label>
                    <input type="number" name="price_retail" value="{{ old('price_retail') }}" step="0.01" min="0" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">ราคาส่ง</label>
                    <input type="number" name="price_wholesale1" value="{{ old('price_wholesale1') }}" step="0.01" min="0" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">ประเภทสินค้า</label>
                    <select name="item_type" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" required>
                        <option value="drug" {{ old('item_type', 'drug') == 'drug' ? 'selected' : '' }}>drug</option>
                        <option value="supply" {{ old('item_type') == 'supply' ? 'selected' : '' }}>supply</option>
                        <option value="equipment" {{ old('item_type') == 'equipment' ? 'selected' : '' }}>equipment</option>
                        <option value="service" {{ old('item_type') == 'service' ? 'selected' : '' }}>service</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">ชนิดยา (ID)</label>
                    <input type="number" name="drug_type_id" value="{{ old('drug_type_id') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Reorder Point</label>
                    <input type="number" name="reorder_point" value="{{ old('reorder_point') }}" min="0" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Safety Stock</label>
                    <input type="number" name="safety_stock" value="{{ old('safety_stock') }}" min="0" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="px-6 py-3 bg-emerald-500 text-white rounded-lg font-bold hover:bg-emerald-600">บันทึกสินค้า</button>
            </div>
        </form>
    </div>
</div>
@endsection
