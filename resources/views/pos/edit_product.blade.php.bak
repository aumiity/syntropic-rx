@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">แก้ไขสินค้า #{{ $product->id }}</h1>
            <p class="text-slate-600 text-sm">{{ $product->trade_name }}</p>
        </div>
        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 rounded-lg">กลับรายการสินค้า</a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 max-w-2xl">
        <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-700">Barcode <span class="text-red-500">*</span></label>
                <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">รหัสสินค้า</label>
                <input type="text" name="code" value="{{ old('code', $product->code) }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">ชื่อสินค้า (trade name) <span class="text-red-500">*</span></label>
                <input type="text" name="trade_name" value="{{ old('trade_name', $product->trade_name) }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">ชื่อพิมพ์</label>
                <input type="text" name="name_for_print" value="{{ old('name_for_print', $product->name_for_print) }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">ราคาขาย (Retail) <span class="text-red-500">*</span></label>
                    <input type="number" name="price_retail" value="{{ old('price_retail', $product->price_retail) }}" step="0.01" min="0" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">ราคาส่ง</label>
                    <input type="number" name="price_wholesale1" value="{{ old('price_wholesale1', $product->price_wholesale1) }}" step="0.01" min="0" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">ประเภทสินค้า</label>
                    <select name="item_type" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" required>
                        <option value="drug" {{ old('item_type', $product->item_type) == 'drug' ? 'selected' : '' }}>drug</option>
                        <option value="supply" {{ old('item_type', $product->item_type) == 'supply' ? 'selected' : '' }}>supply</option>
                        <option value="equipment" {{ old('item_type', $product->item_type) == 'equipment' ? 'selected' : '' }}>equipment</option>
                        <option value="service" {{ old('item_type', $product->item_type) == 'service' ? 'selected' : '' }}>service</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">ชนิดยา (ID)</label>
                    <input type="number" name="drug_type_id" value="{{ old('drug_type_id', $product->drug_type_id) }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Reorder Point</label>
                    <input type="number" name="reorder_point" value="{{ old('reorder_point', $product->reorder_point) }}" min="0" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Safety Stock</label>
                    <input type="number" name="safety_stock" value="{{ old('safety_stock', $product->safety_stock) }}" min="0" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="px-6 py-3 bg-emerald-500 text-white rounded-lg font-bold hover:bg-emerald-600">อัพเดตสินค้า</button>
            </div>
        </form>
    </div>
</div>
@endsection