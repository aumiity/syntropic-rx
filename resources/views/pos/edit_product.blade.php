@extends('layouts.app')

@section('content')
<div class="p-6 max-w-4xl mx-auto">

    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">แก้ไขสินค้า #{{ $product->id }}</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ $product->trade_name }}</p>
        </div>
        <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-sm text-gray-600">กลับรายการ</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-5">

            {{-- Section: ข้อมูลพื้นฐาน --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="text-sm font-semibold text-gray-600 mb-4 pb-2 border-b border-gray-100">ข้อมูลพื้นฐาน</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ชื่อสินค้า (Trade Name) <span class="text-red-500">*</span></label>
                        <input type="text" name="trade_name" value="{{ old('trade_name',$product->trade_name) }}" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ชื่อพิมพ์ (ฉลากยา)</label>
                        <input type="text" name="name_for_print" value="{{ old('name_for_print',$product->name_for_print) }}" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Barcode หลัก</label>
                        <input type="text" name="barcode" value="{{ old('barcode',$product->barcode) }}" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Barcode สำรอง</label>
                        <input type="text" name="barcode2" value="{{ old('barcode2',$product->barcode2) }}" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">รหัสสินค้าภายใน</label>
                        <input type="text" name="code" value="{{ old('code',$product->code) }}" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ประเภทสินค้า</label>
                        <select name="item_type" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                            <option value="drug"      {{ old('item_type',$product->item_type)=='drug'      ? 'selected':'' }}>ยา (Drug)</option>
                            <option value="supply"    {{ old('item_type',$product->item_type)=='supply'    ? 'selected':'' }}>เวชภัณฑ์ (Supply)</option>
                            <option value="equipment" {{ old('item_type',$product->item_type)=='equipment' ? 'selected':'' }}>อุปกรณ์ (Equipment)</option>
                            <option value="service"   {{ old('item_type',$product->item_type)=='service'   ? 'selected':'' }}>บริการ (Service)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">รูปแบบยา</label>
                        <select name="dosage_form_id" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                            <option value="">-- เลือกรูปแบบ --</option>
                            @foreach($dosageForms as $form)
                                <option value="{{ $form->id }}" {{ old('dosage_form_id',$product->dosage_form_id)==$form->id ? 'selected':'' }}>{{ $form->name_th }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">หน่วยนับ</label>
                        <select name="unit_id" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                            <option value="">-- เลือกหน่วย --</option>
                            @foreach($itemUnits as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id',$product->unit_id)==$unit->id ? 'selected':'' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Section: ราคา --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="text-sm font-semibold text-gray-600 mb-4 pb-2 border-b border-gray-100">ราคา</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ราคาขายปลีก <span class="text-red-500">*</span></label>
                        <input type="number" name="price_retail" value="{{ old('price_retail',$product->price_retail) }}" step="0.01" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ราคาส่ง 1</label>
                        <input type="number" name="price_wholesale1" value="{{ old('price_wholesale1',$product->price_wholesale1) }}" step="0.01" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ราคาส่ง 2</label>
                        <input type="number" name="price_wholesale2" value="{{ old('price_wholesale2',$product->price_wholesale2) }}" step="0.01" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                </div>
                <div class="flex gap-6 mt-3">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="is_vat" value="1" {{ old('is_vat',$product->is_vat) ? 'checked':'' }} class="w-4 h-4 rounded">มี VAT</label>
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="is_not_discount" value="1" {{ old('is_not_discount',$product->is_not_discount) ? 'checked':'' }} class="w-4 h-4 rounded">ห้ามลดราคา</label>
                </div>
            </div>

            {{-- Section: ข้อมูลยา --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="text-sm font-semibold text-gray-600 mb-4 pb-2 border-b border-gray-100">ข้อมูลยาเฉพาะ</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ประเภทยา (ขย.)</label>
                        <select name="drug_type_id" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                            <option value="">-- เลือกประเภท --</option>
                            @foreach($drugTypes as $type)
                                <option value="{{ $type->id }}" {{ old('drug_type_id',$product->drug_type_id)==$type->id ? 'selected':'' }}>{{ $type->name_th }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ความแรง (Strength)</label>
                        <input type="number" name="strength" value="{{ old('strength',$product->strength) }}" step="0.0001" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">เลขทะเบียน อย.</label>
                        <input type="text" name="registration_no" value="{{ old('registration_no',$product->registration_no) }}" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">TMT ID</label>
                        <input type="text" name="tmt_id" value="{{ old('tmt_id',$product->tmt_id) }}" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">จำนวนจ่ายสูงสุด/ครั้ง</label>
                        <input type="number" name="max_dispense_qty" value="{{ old('max_dispense_qty',$product->max_dispense_qty) }}" step="0.01" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">จำนวนควบคุมการขาย</label>
                        <input type="number" name="sale_control_qty" value="{{ old('sale_control_qty',$product->sale_control_qty) }}" step="0.01" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                </div>
                <div class="flex flex-wrap gap-6 mt-3">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="is_original_drug" value="1" {{ old('is_original_drug',$product->is_original_drug) ? 'checked':'' }} class="w-4 h-4 rounded">ยาต้นแบบ</label>
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="is_antibiotic" value="1" {{ old('is_antibiotic',$product->is_antibiotic) ? 'checked':'' }} class="w-4 h-4 rounded">ยาปฏิชีวนะ</label>
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="is_fda_report" value="1" {{ old('is_fda_report',$product->is_fda_report) ? 'checked':'' }} class="w-4 h-4 rounded">ต้องรายงาน ขย.</label>
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="is_fda13_report" value="1" {{ old('is_fda13_report',$product->is_fda13_report) ? 'checked':'' }} class="w-4 h-4 rounded">รายงาน ขย.13</label>
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="is_sale_control" value="1" {{ old('is_sale_control',$product->is_sale_control) ? 'checked':'' }} class="w-4 h-4 rounded">ควบคุมการขาย</label>
                </div>
            </div>

            {{-- Section: Stock Alert --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="text-sm font-semibold text-gray-600 mb-4 pb-2 border-b border-gray-100">การแจ้งเตือน Stock</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Reorder Point</label>
                        <input type="number" name="reorder_point" value="{{ old('reorder_point',$product->reorder_point) }}" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Safety Stock</label>
                        <input type="number" name="safety_stock" value="{{ old('safety_stock',$product->safety_stock) }}" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">แจ้งเตือนหมดอายุ ระดับ 1 (วัน)</label>
                        <input type="number" name="expiry_alert_days1" value="{{ old('expiry_alert_days1',$product->expiry_alert_days1) }}" min="1" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">แจ้งเตือนหมดอายุ ระดับ 2 (วัน)</label>
                        <input type="number" name="expiry_alert_days2" value="{{ old('expiry_alert_days2',$product->expiry_alert_days2) }}" min="1" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">แจ้งเตือนหมดอายุ ด่วน (วัน)</label>
                        <input type="number" name="expiry_alert_days3" value="{{ old('expiry_alert_days3',$product->expiry_alert_days3) }}" min="1" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                </div>
            </div>

            {{-- Section: ข้อมูลเสริม --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="text-sm font-semibold text-gray-600 mb-4 pb-2 border-b border-gray-100">ข้อมูลเสริม</h2>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ข้อบ่งใช้ (Indication)</label>
                        <textarea name="indication_note" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-emerald-400">{{ old('indication_note',$product->indication_note) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ผลข้างเคียง (Side Effects)</label>
                        <textarea name="side_effect_note" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-emerald-400">{{ old('side_effect_note',$product->side_effect_note) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">หมายเหตุ</label>
                        <textarea name="note" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-emerald-400">{{ old('note',$product->note) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end gap-3 pb-6">
                <a href="{{ route('products.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50">ยกเลิก</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium">อัปเดตสินค้า</button>
            </div>

        </div>
    </form>
</div>
@endsection
