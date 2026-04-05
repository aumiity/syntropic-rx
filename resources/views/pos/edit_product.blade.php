@extends('layouts.app')

@section('content')
<div class="p-6 w-full mx-auto">

    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">แก้ไขสินค้า #{{ $product->id }}</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ $product->trade_name }}</p>
        </div>
        <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-sm text-gray-600">กลับรายการ</a>
    </div>

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

        <!-- Tab Bar -->
        <div class="mb-6 flex flex-wrap gap-2 border-b border-gray-200">
            <button type="button" data-tab="tab-1" class="tab-button active min-h-11 px-4 py-2.5 text-sm font-medium text-emerald-600 border-b-2 border-emerald-600 rounded-t-lg">ข้อมูลหลัก</button>
            <button type="button" data-tab="tab-price" class="tab-button min-h-11 px-4 py-2.5 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800 rounded-t-lg">ราคา</button>
            <button type="button" data-tab="tab-2" class="tab-button min-h-11 px-4 py-2.5 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800 rounded-t-lg">หน่วยสินค้า</button>
            <button type="button" data-tab="tab-3" class="tab-button min-h-11 px-4 py-2.5 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800 rounded-t-lg">ยาสามัญและประเภทยา</button>
            <button type="button" data-tab="tab-labels" class="tab-button min-h-11 px-4 py-2.5 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800 rounded-t-lg">ฉลาก</button>
            <button type="button" data-tab="tab-5" class="tab-button min-h-11 px-4 py-2.5 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800 rounded-t-lg">สต๊อค</button>
            <button type="button" data-tab="tab-6" class="tab-button min-h-11 px-4 py-2.5 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800 rounded-t-lg">ข้อมูลอื่นๆ</button>
            <button type="button" data-tab="tab-7" class="tab-button min-h-11 px-4 py-2.5 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800 rounded-t-lg">ประวัติ</button>
        </div>

        <!-- Tab Panels -->
        <div class="space-y-5">

            <!-- Tab 1: ข้อมูลหลัก -->
            <div id="tab-1" class="tab-panel active bg-white border border-gray-200 rounded-xl p-5">
                @php
                    $latestCost = $product->lots?->sortByDesc('created_at')->first()?->cost_price ?? 0;
                    $avgCost = $product->lots?->avg('cost_price') ?? 0;
                @endphp

                {{-- Apple-style toggle: is_disabled --}}
                <div class="flex items-center justify-end mb-4">
                    <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                        <input type="hidden" name="is_disabled" value="0">
                        <input type="checkbox" name="is_disabled" value="1" class="sr-only peer"
                            {{ old('is_disabled', $product->is_disabled) ? 'checked' : '' }}>
                        {{-- Track: ฟ้า=เปิด (unchecked), เทา=ปิด (checked) --}}
                        {{-- Thumb: เริ่มที่ขวา (translate-x-5 = 20px), checked กลับซ้าย (translate-x-0) --}}
                        <span class="text-sm font-medium text-blue-600 peer-checked:hidden">เปิดใช้งาน</span>
                        <span class="hidden text-sm font-medium text-gray-400 peer-checked:inline">ปิดใช้งาน</span>
                        <div class="relative w-14 h-7 rounded-full
                                    bg-blue-500 peer-checked:bg-gray-300
                                    transition-colors duration-300 ease-in-out
                                    after:content-[''] after:absolute after:top-1 after:left-1
                                    after:h-5 after:w-7 after:rounded-full after:bg-white after:shadow-sm
                                    after:translate-x-5
                                    peer-checked:after:translate-x-0
                                    after:transition-transform after:duration-300 after:ease-in-out">
                        </div>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">รหัสสินค้า</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">รหัสสินค้า (auto)</label>
                                    <input type="text" readonly value="{{ $product->code }}" class="w-full h-10 rounded-lg bg-gray-100 border border-gray-200 px-3 text-sm text-gray-600">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">ID</label>
                                    <input type="text" readonly value="{{ $product->id }}" class="w-full h-10 rounded-lg bg-gray-100 border border-gray-200 px-3 text-sm text-gray-600">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">ชื่อสินค้า <span class="text-red-500">*</span></label>
                            <input type="text" name="trade_name" value="{{ old('trade_name', $product->trade_name) }}" required class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400" data-required="true" data-error-msg="กรุณากรอกชื่อสินค้า">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">ชื่อพิมพ์</label>
                            <input type="text" name="name_for_print" value="{{ old('name_for_print', $product->name_for_print) }}" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">ประเภทสินค้า</label>
                            <select name="category_id" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                                <option value="">-- เลือกประเภท --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">หน่วยขาย (เล็กที่สุด)</label>
                            <div class="flex items-center gap-3">
                                <div id="base-unit-display" class="flex-1 h-10 rounded-lg bg-gray-100 border border-gray-200 px-3 text-sm text-gray-700 flex items-center">
                                    {{ $baseUnitName }}
                                </div>
                                <button
                                    type="button"
                                    class="h-10 px-4 rounded-lg bg-blue-500 hover:bg-blue-600 text-white text-sm"
                                    onclick="
                                        const m=document.getElementById('modal-edit-unit');
                                        document.getElementById('modal-unit-input').value = document.getElementById('base-unit-hidden').value;
                                        m.classList.remove('hidden');
                                        m.classList.add('flex');
                                    "
                                >
                                    แก้ไขหน่วยขาย
                                </button>
                            </div>
                            <input id="base-unit-hidden" type="hidden" name="base_unit_name" value="{{ $baseUnitName }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">บาร์โค้ด</label>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-400 w-4">1</span>
                                    <input type="text" name="barcode" value="{{ $product->barcode }}" 
                                        placeholder="บาร์โค้ด 1"
                                        class="flex-1 h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-400 w-4">2</span>
                                    <input type="text" name="barcode2" value="{{ $product->barcode2 }}" 
                                        placeholder="บาร์โค้ด 2"
                                        class="flex-1 h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-400 w-4">3</span>
                                    <input type="text" name="barcode3" value="{{ old('barcode3', $product->barcode3) }}" 
                                        placeholder="บาร์โค้ด 3"
                                        class="flex-1 h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-400 w-4">4</span>
                                    <input type="text" name="barcode4" value="{{ old('barcode4', $product->barcode4) }}" 
                                        placeholder="บาร์โค้ด 4"
                                        class="flex-1 h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">ชื่อที่ตั้งเอง (คำค้น)</label>
                            <input type="text" name="search_keywords" value="{{ old('search_keywords', $product->search_keywords) }}" 
                                placeholder="เช่น พารา, para, tylenol (คั่นด้วยจุลภาค)"
                                class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">ให้ลงขายเริ่มต้น</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="default_qty" value="{{ old('default_qty', $product->default_qty ?? 1) }}" min="1" class="flex-1 h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                                <span class="text-sm text-gray-500">{{ $baseUnitName }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">จุดสั่งซื้อ (Reorder Point)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="reorder_point" value="{{ old('reorder_point', $product->reorder_point) }}" min="0" class="flex-1 h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                                <span class="text-sm text-gray-500">{{ $baseUnitName }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Safety Stock</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="safety_stock" value="{{ old('safety_stock', $product->safety_stock) }}" min="0" class="flex-1 h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                                <span class="text-sm text-gray-500">{{ $baseUnitName }}</span>
                            </div>
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-700">
                                จำนวนคงเหลือ: {{ $product->lots?->sum('qty_on_hand') ?? 0 }} {{ $baseUnitName }}
                            </p>
                            <button 
                                type="button"
                                id="open-adjust-stock-modal-tab1"
                                onclick="document.getElementById('open-adjust-stock-modal').click()"
                                class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-medium">
                                ปรับสต็อค
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Price: ราคา -->
            <div id="tab-price" class="tab-panel hidden bg-white border border-gray-200 rounded-xl p-5">
                @php
                    $hasWholesale1 = (int) old('has_wholesale1', data_get($product, 'has_wholesale1', (($product->price_wholesale1 ?? 0) > 0 ? 1 : 0)));
                    $hasWholesale2 = (int) old('has_wholesale2', data_get($product, 'has_wholesale2', (($product->price_wholesale2 ?? 0) > 0 ? 1 : 0)));
                @endphp

                <input type="hidden" name="has_wholesale1" id="has_wholesale1" value="{{ $hasWholesale1 ? 1 : 0 }}">
                <input type="hidden" name="has_wholesale2" id="has_wholesale2" value="{{ $hasWholesale2 ? 1 : 0 }}">

                <div class="overflow-hidden rounded-xl border border-gray-200 divide-y divide-gray-200" data-latest-cost="{{ $latestCost }}">
                    <div class="px-4 py-3 bg-gray-50 space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">ราคาทุนล่าสุด</span>
                            <span class="font-semibold text-gray-800">฿{{ number_format($latestCost, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">ทุนเฉลี่ย</span>
                            <span class="font-semibold text-gray-800">฿{{ number_format($avgCost, 2) }}</span>
                        </div>
                    </div>

                    <div class="px-4 py-4 space-y-2">
                        <div class="flex items-center justify-between gap-4">
                            <label for="price_retail" class="text-sm font-medium text-gray-700">ราคาขายปลีก</label>
                            <div class="w-full max-w-xs">
                                <input type="number" name="price_retail" id="price_retail" value="{{ old('price_retail', $product->price_retail) }}" step="0.01" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm text-right focus:outline-none focus:border-emerald-400" data-required="true" data-error-msg="กรุณากรอกราคาขายปลีก">
                                <p id="retail-unit-text" class="mt-1 text-xs text-gray-500 text-right">ต่อ 1 ({{ $baseUnitName }})</p>
                            </div>
                        </div>
                        <div class="text-sm flex flex-wrap items-center gap-x-5 gap-y-1">
                            <span>กำไร/หน่วย <span id="profit-retail-per-unit" class="font-semibold text-gray-700">฿0.00</span></span>
                            <span>เทียบทุน <span id="profit-retail-vs-cost" class="font-semibold text-gray-700">0.00%</span></span>
                            <span>เทียบขาย <span id="profit-retail-vs-sale" class="font-semibold text-gray-700">0.00%</span></span>
                        </div>
                        <p id="warning-retail" class="hidden text-sm text-red-600 font-medium"></p>
                    </div>

                    <div class="px-4 py-4 space-y-3">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-sm font-medium text-gray-700">ราคาส่ง ระดับ 1</span>
                            <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                                <input type="checkbox" id="toggle-has-wholesale1" class="sr-only peer" {{ $hasWholesale1 ? 'checked' : '' }}>
                                <div class="relative w-14 h-7 rounded-full bg-gray-300 peer-checked:bg-blue-500 transition-colors duration-300 ease-in-out after:content-[''] after:absolute after:top-1 after:left-1 after:h-5 after:w-7 after:rounded-full after:bg-white after:shadow-sm after:translate-x-0 peer-checked:after:translate-x-5 after:transition-transform after:duration-300 after:ease-in-out"></div>
                            </label>
                        </div>

                        <div id="wholesale1-section" class="overflow-hidden transition-all duration-300 ease-in-out {{ $hasWholesale1 ? 'max-h-40 opacity-100' : 'max-h-0 opacity-0' }}">
                            <div class="pt-1 space-y-2">
                                <div class="flex items-center justify-between gap-4">
                                    <label for="price_wholesale1_main" class="text-sm text-gray-600">ราคา</label>
                                    <input type="number" name="price_wholesale1" id="price_wholesale1_main" value="{{ old('price_wholesale1', $product->price_wholesale1) }}" step="0.01" min="0" placeholder="0" class="w-full max-w-xs h-10 rounded-lg border border-gray-300 px-3 text-sm text-right focus:outline-none focus:border-emerald-400">
                                </div>
                                <div class="text-sm flex flex-wrap items-center gap-x-5 gap-y-1">
                                    <span>กำไร/หน่วย <span id="profit-wh1-per-unit" class="font-semibold text-gray-700">฿0.00</span></span>
                                    <span>เทียบทุน <span id="profit-wh1-vs-cost" class="font-semibold text-gray-700">0.00%</span></span>
                                    <span>เทียบขาย <span id="profit-wh1-vs-sale" class="font-semibold text-gray-700">0.00%</span></span>
                                </div>
                                <p id="warning-wh1" class="hidden text-sm text-red-600 font-medium">⚠ ราคาส่งไม่ควรแพงกว่าปลีก</p>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-4 space-y-3">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-sm font-medium text-gray-700">ราคาส่ง ระดับ 2</span>
                            <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                                <input type="checkbox" id="toggle-has-wholesale2" class="sr-only peer" {{ $hasWholesale2 ? 'checked' : '' }}>
                                <div class="relative w-14 h-7 rounded-full bg-gray-300 peer-checked:bg-blue-500 transition-colors duration-300 ease-in-out after:content-[''] after:absolute after:top-1 after:left-1 after:h-5 after:w-7 after:rounded-full after:bg-white after:shadow-sm after:translate-x-0 peer-checked:after:translate-x-5 after:transition-transform after:duration-300 after:ease-in-out"></div>
                            </label>
                        </div>

                        <div id="wholesale2-section" class="overflow-hidden transition-all duration-300 ease-in-out {{ $hasWholesale2 ? 'max-h-40 opacity-100' : 'max-h-0 opacity-0' }}">
                            <div class="pt-1 space-y-2">
                                <div class="flex items-center justify-between gap-4">
                                    <label for="price_wholesale2_main" class="text-sm text-gray-600">ราคา</label>
                                    <input type="number" name="price_wholesale2" id="price_wholesale2_main" value="{{ old('price_wholesale2', $product->price_wholesale2) }}" step="0.01" min="0" placeholder="0" class="w-full max-w-xs h-10 rounded-lg border border-gray-300 px-3 text-sm text-right focus:outline-none focus:border-emerald-400">
                                </div>
                                <div class="text-sm flex flex-wrap items-center gap-x-5 gap-y-1">
                                    <span>กำไร/หน่วย <span id="profit-wh2-per-unit" class="font-semibold text-gray-700">฿0.00</span></span>
                                    <span>เทียบทุน <span id="profit-wh2-vs-cost" class="font-semibold text-gray-700">0.00%</span></span>
                                    <span>เทียบขาย <span id="profit-wh2-vs-sale" class="font-semibold text-gray-700">0.00%</span></span>
                                </div>
                                <p id="warning-wh2" class="hidden text-sm text-red-600 font-medium">⚠ ราคาส่งไม่ควรแพงกว่าปลีก</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: หน่วยสินค้า -->
            <div id="tab-2" class="tab-panel hidden bg-white border border-gray-200 rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-700">หน่วยสินค้า</h3>
                    <button type="button" id="btn-add-unit" class="h-10 px-4 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium">
                        + เพิ่มหน่วย
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium">หน่วย</th>
                                <th class="px-3 py-2 text-right font-medium">ขนาดบรรจุ</th>
                                <th class="px-3 py-2 text-left font-medium">ขายได้ / รับเข้าได้</th>
                                <th class="px-3 py-2 text-center font-medium">สถานะ</th>
                                <th class="px-3 py-2 text-right font-medium">ราคาปลีก</th>
                                <th class="px-3 py-2 text-center font-medium w-32">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($product->productUnits->where('is_base_unit', false) as $unit)
                                <tr class="border-t border-gray-100">
                                    <td class="px-3 py-2">
                                        <div class="font-medium text-gray-800">{{ $unit->unit_name }}</div>
                                        <div class="text-xs text-gray-400">{{ $unit->barcode ?: '-' }}</div>
                                    </td>
                                    <td class="px-3 py-2 text-right text-gray-700">{{ rtrim(rtrim(number_format($unit->qty_per_base, 4, '.', ''), '0'), '.') }}</td>
                                    <td class="px-3 py-2">
                                        <div class="flex flex-wrap gap-1">
                                            @if($unit->is_for_sale)
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs">ขายได้</span>
                                            @endif
                                            @if($unit->is_for_purchase)
                                                <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-700 text-xs">รับเข้าได้</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button
                                            type="button"
                                            class="unit-disabled-toggle relative inline-flex h-7 w-12 items-center rounded-full transition-colors duration-200 {{ $unit->is_disabled ? 'bg-gray-300' : 'bg-blue-500' }}"
                                            data-unit-id="{{ $unit->id }}"
                                            data-is-disabled="{{ $unit->is_disabled ? '1' : '0' }}"
                                            aria-label="สลับสถานะหน่วยสินค้า"
                                            title="{{ $unit->is_disabled ? 'ปิดใช้งาน' : 'เปิดใช้งาน' }}"
                                            onclick="toggleUnitDisabled({{ $unit->id }}, this)">
                                            <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-sm transition-transform duration-200 {{ $unit->is_disabled ? 'translate-x-1' : 'translate-x-6' }}"></span>
                                        </button>
                                    </td>
                                    <td class="px-3 py-2 text-right text-gray-800">{{ number_format($unit->price_retail ?? 0, 2) }}</td>
                                    <td class="px-3 py-2">
                                        <div class="flex items-center justify-center gap-2">
                                            <button
                                                type="button"
                                                class="w-9 h-9 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50"
                                                title="แก้ไข"
                                                onclick='openUnitModal({
                                                    mode: "edit",
                                                    id: {{ $unit->id }},
                                                    unit_name: @json($unit->unit_name),
                                                    qty_per_base: {{ (float) $unit->qty_per_base }},
                                                    barcode: @json($unit->barcode),
                                                    price_retail: {{ (float) ($unit->price_retail ?? 0) }},
                                                    price_wholesale1: {{ (float) ($unit->price_wholesale1 ?? 0) }},
                                                    is_for_sale: {{ $unit->is_for_sale ? "true" : "false" }},
                                                    is_for_purchase: {{ $unit->is_for_purchase ? "true" : "false" }}
                                                })'>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-4 h-4 mx-auto">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 3.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 15.07a4.5 4.5 0 0 1-1.897 1.13L6 17l.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.862 3.487Z" />
                                                </svg>
                                            </button>

                                            <button
                                                type="button"
                                                class="w-9 h-9 rounded-lg border border-red-200 text-red-600 hover:bg-red-50"
                                                title="ลบ"
                                                onclick="deleteUnit({{ $unit->id }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-4 h-4 mx-auto">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0 1 15.916 21.75H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0C9.16 2.313 8.25 3.297 8.25 4.477v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-gray-400 py-6">ยังไม่มีหน่วยสินค้า</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 3: ประเภทยาตามกฎหมาย -->
            <div id="tab-3" class="tab-panel hidden bg-white border border-gray-200 rounded-xl p-5">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">สินค้านี้เป็นยาตามกฎหมาย (รายงาน ขย.9)</span>
                    <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="is_fda_report" id="is_fda_report" value="1" class="sr-only peer"
                            {{ old('is_fda_report', $product->is_fda_report) ? 'checked' : '' }}>
                        <div class="relative w-14 h-7 rounded-full bg-gray-300 peer-checked:bg-blue-500 transition-colors duration-300 ease-in-out after:content-[''] after:absolute after:top-1 after:left-1 after:h-5 after:w-7 after:rounded-full after:bg-white after:shadow-sm after:translate-x-0 peer-checked:after:translate-x-5 after:transition-transform after:duration-300 after:ease-in-out"></div>
                    </label>
                </div>

                <div id="drug_law_section" class="overflow-hidden transition-all duration-300 ease-in-out {{ old('is_fda_report', $product->is_fda_report) ? 'max-h-130 opacity-100' : 'max-h-0 opacity-0' }}">
                    <div class="rounded-xl border border-gray-200 divide-y divide-gray-200">
                        <div class="px-4 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <label for="generic-name-search" class="pt-2 text-sm font-medium text-gray-700">ชื่อยาสามัญ</label>
                                <div class="relative w-full max-w-xl">
                                    <input type="hidden" name="drug_generic_name_id" id="drug_generic_name_id" value="{{ old('drug_generic_name_id', $product->drug_generic_name_id) }}">
                                    <div class="relative">
                                        <input type="text" id="generic-name-search" autocomplete="off"
                                            value="{{ old('drug_generic_name_name', $product->genericName->name ?? '') }}"
                                            placeholder="พิมพ์ค้นหาชื่อยาสามัญ"
                                            class="w-full h-10 rounded-lg border border-gray-300 px-3 pr-10 text-sm focus:outline-none focus:border-emerald-400">
                                        <button type="button" id="clear-generic-name" class="absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 rounded hover:bg-gray-100 text-gray-400 hidden">×</button>
                                    </div>
                                    <div id="generic-name-dropdown" class="hidden absolute z-30 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-56 overflow-auto"></div>
                                </div>
                            </div>
                        </div>

                        <div class="px-4 py-4">
                            <div class="flex items-center justify-between gap-4">
                                <label for="drug_type_id" class="text-sm font-medium text-gray-700">ประเภทยาตามกฎหมาย</label>
                                <select name="drug_type_id" id="drug_type_id" class="w-full max-w-xl h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                                    <option value="">-- เลือกประเภท --</option>
                                    @foreach($drugTypes->where('is_disabled', false)->sortBy('name_th') as $type)
                                        <option value="{{ $type->id }}" {{ old('drug_type_id', $product->drug_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name_th }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="px-4 py-4">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm font-medium text-gray-700">รายงาน ขย.11</span>
                                <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="is_fda11_report" id="is_fda11_report" value="1" class="sr-only peer"
                                        {{ old('is_fda11_report', $product->is_fda11_report) ? 'checked' : '' }}>
                                    <div class="relative w-14 h-7 rounded-full bg-gray-300 peer-checked:bg-blue-500 transition-colors duration-300 ease-in-out after:content-[''] after:absolute after:top-1 after:left-1 after:h-5 after:w-7 after:rounded-full after:bg-white after:shadow-sm after:translate-x-0 peer-checked:after:translate-x-5 after:transition-transform after:duration-300 after:ease-in-out"></div>
                                </label>
                            </div>
                        </div>

                        <div class="px-4 py-4">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm font-medium text-gray-700">รายงาน ขย.13</span>
                                <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="is_fda13_report" id="is_fda13_report" value="1" class="sr-only peer"
                                        {{ old('is_fda13_report', $product->is_fda13_report) ? 'checked' : '' }}>
                                    <div class="relative w-14 h-7 rounded-full bg-gray-300 peer-checked:bg-blue-500 transition-colors duration-300 ease-in-out after:content-[''] after:absolute after:top-1 after:left-1 after:h-5 after:w-7 after:rounded-full after:bg-white after:shadow-sm after:translate-x-0 peer-checked:after:translate-x-5 after:transition-transform after:duration-300 after:ease-in-out"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Tab Labels: ฉลาก (Label Management) -->
            <div id="tab-labels" class="tab-panel hidden bg-white border border-gray-200 rounded-xl p-5 pb-24">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-base font-semibold ">ฉลากยา (Labels)</h3>
                    <button type="button" id="btn-add-label" class="h-10 px-4 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium">
                        + เพิ่มฉลาก
                    </button>
                </div>
                <div id="labels-list-container">
                    <!-- Labels table will be loaded here via AJAX -->
                    <div class="flex items-center justify-center h-32">
                        <span class="text-gray-400">กำลังโหลดข้อมูล...</span>
                    </div>
                </div>
            </div>

            <!-- Tab 5: สต๊อค -->
            <div id="tab-5" class="tab-panel hidden bg-white border border-gray-200 rounded-xl p-5">
                @php
                    $sortedLots = $product->lots->sortBy('expiry_date')->values();
                    $currentStockTotal = (int) ($sortedLots->sum('qty_on_hand') ?? 0);
                    $totalCostValue = (float) $sortedLots->sum(function ($lot) {
                        return ((float) $lot->qty_on_hand) * ((float) $lot->cost_price);
                    });
                    $avgCostPerUnit = $currentStockTotal > 0 ? ($totalCostValue / $currentStockTotal) : 0;
                    $totalRetailValue = ((float) $currentStockTotal) * ((float) ($product->price_retail ?? 0));
                    $now = now();
                @endphp

                <div class="mb-6">
                    <h3 class="mb-3 text-sm font-semibold text-gray-700">สต็อคสินค้า</h3>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <p class="text-xs text-gray-500">จำนวน</p>
                            <p class="mt-1 text-lg font-semibold text-gray-800">{{ number_format($currentStockTotal, 0) }} {{ $baseUnitName }}</p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <p class="text-xs text-gray-500">มูลค่ารวม (ต้นทุน)</p>
                            <p class="mt-1 text-lg font-semibold text-gray-800">฿{{ number_format($totalCostValue, 2) }}</p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <p class="text-xs text-gray-500">ต้นทุน/หน่วย</p>
                            <p class="mt-1 text-lg font-semibold text-gray-800">฿{{ number_format($avgCostPerUnit, 2) }}</p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <p class="text-xs text-gray-500">มูลค่ารวม (ราคาขาย)</p>
                            <p class="mt-1 text-lg font-semibold text-gray-800">฿{{ number_format($totalRetailValue, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="mb-4 flex items-center justify-between gap-4 border-b border-gray-100 pb-2">
                        <h3 class="text-sm font-semibold text-gray-700">ล็อตสินค้า</h3>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                id="open-stock-return-modal"
                                class="inline-flex h-10 items-center rounded-lg bg-sky-500 px-4 text-sm font-medium text-white hover:bg-sky-600"
                            >
                                รับคืนสินค้า
                            </button>
                            <button
                                type="button"
                                id="open-adjust-stock-modal"
                                class="inline-flex h-10 items-center rounded-lg bg-amber-500 px-4 text-sm font-medium text-white hover:bg-amber-600"
                            >
                                ปรับสต็อค
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="px-3 py-2 text-left font-medium text-gray-600 text-xs">ล็อตที่</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600 text-xs">Lot Number</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600 text-xs">วันหมดอายุ</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-600 text-xs">ราคาทุน</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-600 text-xs">คงเหลือ</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600 text-xs">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sortedLots as $idx => $lot)
                                    @php
                                        $daysToExpiry = $now->diffInDays($lot->expiry_date, false);
                                    @endphp
                                    <tr class="border-b border-gray-100">
                                        <td class="px-3 py-2 text-gray-800">{{ $idx + 1 }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $lot->lot_number }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $lot->expiry_date->format('d/m/Y') }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700">฿{{ number_format($lot->cost_price, 2) }}</td>
                                        <td class="px-3 py-2 text-right font-medium text-gray-800">{{ $lot->qty_on_hand }}</td>
                                        <td class="px-3 py-2 text-xs">
                                            @if($daysToExpiry < 0)
                                                <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 font-medium text-red-700">หมดอายุแล้ว</span>
                                            @elseif($daysToExpiry < 30)
                                                <span class="inline-flex rounded-full bg-red-50 px-2 py-0.5 font-medium text-red-600">ใกล้หมดอายุ</span>
                                            @elseif($daysToExpiry < 90)
                                                <span class="inline-flex rounded-full bg-yellow-100 px-2 py-0.5 font-medium text-yellow-700">เตือน</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 font-medium text-emerald-700">ปกติ</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="border-b border-gray-100">
                                        <td colspan="6" class="px-3 py-4 text-center text-gray-400 text-sm">ไม่มีข้อมูลล็อต</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Expiry Alert Settings -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-600 mb-4 pb-2 border-b border-gray-100">ตั้งค่าแจ้งเตือนหมดอายุ</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">แจ้งเตือนล่วงหน้า ระดับ 1 (วัน)</label>
                            <input type="number" name="expiry_alert_days1" value="{{ old('expiry_alert_days1', $product->expiry_alert_days1) }}" min="1" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">ระดับ 2 (วัน)</label>
                            <input type="number" name="expiry_alert_days2" value="{{ old('expiry_alert_days2', $product->expiry_alert_days2) }}" min="1" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">ด่วน (วัน)</label>
                            <input type="number" name="expiry_alert_days3" value="{{ old('expiry_alert_days3', $product->expiry_alert_days3) }}" min="1" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 6: ข้อมูลอื่นๆ -->
            <div id="tab-6" class="tab-panel hidden bg-white border border-gray-200 rounded-xl p-5">
                <div class="grid grid-cols-1 gap-4">
                    <!-- ข้อบ่งใช้ (Indication) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ข้อบ่งใช้ (Indication)</label>
                        <textarea name="indication_note" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-emerald-400">{{ old('indication_note', $product->indication_note) }}</textarea>
                    </div>
                    <!-- ผลข้างเคียง (Side Effects) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ผลข้างเคียง (Side Effects)</label>
                        <textarea name="side_effect_note" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-emerald-400">{{ old('side_effect_note', $product->side_effect_note) }}</textarea>
                    </div>
                    <!-- หมายเหตุ -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">หมายเหตุ</label>
                        <textarea name="note" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-emerald-400">{{ old('note', $product->note) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Tab 7: ประวัติ -->
            <div id="tab-7" class="tab-panel hidden bg-white border border-gray-200 rounded-xl p-5">
                <div class="mb-4 flex flex-wrap gap-2 border-b border-gray-100 pb-3">
                    <button type="button" data-history-tab="history-sales" class="history-subtab-button active rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700">
                        ประวัติการขาย
                    </button>
                    <button type="button" data-history-tab="history-purchase" class="history-subtab-button rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50">
                        ประวัติการซื้อ
                    </button>
                    <button type="button" data-history-tab="history-adjustment" class="history-subtab-button rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50">
                        ปรับสต็อค
                    </button>
                    <button type="button" data-history-tab="history-insufficient" class="history-subtab-button rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50">
                        ไม่พอจ่าย
                    </button>
                    <button type="button" data-history-tab="history-price-change" class="history-subtab-button rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50">
                        การเปลี่ยนราคา
                    </button>
                    <button type="button" data-history-tab="history-stock-return" class="history-subtab-button rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50">
                        รับคืนสินค้า
                    </button>
                </div>

                <div id="history-sales" class="history-subtab-panel">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium">วันที่</th>
                                    <th class="px-3 py-2 text-left font-medium">เลขที่บิล</th>
                                    <th class="px-3 py-2 text-right font-medium">จำนวน</th>
                                    <th class="px-3 py-2 text-left font-medium">หน่วย</th>
                                    <th class="px-3 py-2 text-right font-medium">ราคา/หน่วย</th>
                                    <th class="px-3 py-2 text-right font-medium">รวม</th>
                                    <th class="px-3 py-2 text-center font-medium">ดูเพิ่มเติม</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesHistory as $row)
                                    <tr class="border-t border-gray-100">
                                        <td class="px-3 py-2 text-gray-700">{{ $row->sold_at ? \Illuminate\Support\Carbon::parse($row->sold_at)->format('d/m/Y H:i') : '-' }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $row->invoice_no ?: '-' }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700">{{ number_format((float) $row->qty, 2) }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $row->unit_name ?: '-' }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700">{{ number_format((float) $row->unit_price, 2) }}</td>
                                        <td class="px-3 py-2 text-right text-gray-800 font-medium">{{ number_format((float) $row->line_total, 2) }}</td>
                                        <td class="px-3 py-2 text-center">
                                            <div class="flex flex-col items-center gap-1">
                                                <button disabled class="px-3 py-1 rounded-lg border border-gray-200 text-xs text-gray-400 cursor-not-allowed">ดูบิล</button>
                                                @if($row->is_cancelled)
                                                    <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">ยกเลิก</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-3 py-8 text-center text-gray-400">ไม่มีข้อมูล</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="history-purchase" class="history-subtab-panel hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium">ผู้จำหน่าย</th>
                                    <th class="px-3 py-2 text-left font-medium">เลขที่เอกสาร</th>
                                    <th class="px-3 py-2 text-left font-medium">วันที่รับสินค้า</th>
                                    <th class="px-3 py-2 text-right font-medium">จำนวน</th>
                                    <th class="px-3 py-2 text-right font-medium">ต้นทุน/หน่วย</th>
                                    <th class="px-3 py-2 text-center font-medium"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseHistory as $row)
                                    <tr class="border-t border-gray-100">
                                        <td class="px-3 py-2 text-gray-700">{{ $row->supplier_name ?? '-' }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $row->lot_number ?: '-' }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $row->created_at ? \Illuminate\Support\Carbon::parse($row->created_at)->format('d/m/Y') : '-' }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700">{{ number_format((float) $row->qty_received, 2) }}</td>
                                        <td class="px-3 py-2 text-right text-gray-800 font-medium">{{ number_format((float) $row->cost_price, 2) }} ฿</td>
                                        <td class="px-3 py-2 text-center">
                                            <button disabled class="px-3 py-1 rounded-lg border border-gray-200 text-xs text-gray-400 cursor-not-allowed">ดูใบรับ</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-8 text-center text-gray-400">ไม่มีข้อมูล</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="history-adjustment" class="history-subtab-panel hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium">วันที่</th>
                                    <th class="px-3 py-2 text-center font-medium">ประเภท</th>
                                    <th class="px-3 py-2 text-right font-medium">จำนวน</th>
                                    <th class="px-3 py-2 text-right font-medium">ก่อน</th>
                                    <th class="px-3 py-2 text-right font-medium">หลัง</th>
                                    <th class="px-3 py-2 text-left font-medium">หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($adjustmentHistory as $row)
                                    <tr class="border-t border-gray-100">
                                        <td class="px-3 py-2 text-gray-700">{{ $row->created_at ? \Illuminate\Support\Carbon::parse($row->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                        <td class="px-3 py-2 text-center">
                                            @if($row->movement_type === 'adjustment_in')
                                                <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">เพิ่ม</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">ลด</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-right text-gray-700">{{ number_format((float) $row->qty_change, 2) }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700">{{ number_format((float) $row->qty_before, 2) }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700">{{ number_format((float) $row->qty_after, 2) }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $row->note ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-8 text-center text-gray-400">ไม่มีข้อมูล</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="history-insufficient" class="history-subtab-panel hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium">วันที่</th>
                                    <th class="px-3 py-2 text-right font-medium">จำนวนที่ขาด</th>
                                    <th class="px-3 py-2 text-right font-medium">ยอดก่อน</th>
                                    <th class="px-3 py-2 text-left font-medium">หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($insufficientHistory as $row)
                                    <tr class="border-t border-gray-100">
                                        <td class="px-3 py-2 text-gray-700">{{ $row->created_at ? \Illuminate\Support\Carbon::parse($row->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                        <td class="px-3 py-2 text-right text-red-600 font-medium">{{ number_format(abs((float) $row->qty_change), 2) }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700">{{ number_format((float) $row->qty_before, 2) }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $row->note ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-8 text-center text-gray-400">ไม่มีข้อมูล</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="history-price-change" class="history-subtab-panel hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium">วันที่</th>
                                    <th class="px-3 py-2 text-left font-medium">ประเภทราคา</th>
                                    <th class="px-3 py-2 text-right font-medium">ราคาเก่า</th>
                                    <th class="px-3 py-2 text-right font-medium">ราคาใหม่</th>
                                    <th class="px-3 py-2 text-right font-medium">ผลต่าง</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($priceHistory as $row)
                                    @php
                                        $delta = (float) $row->new_price - (float) $row->old_price;
                                        $priceTypeLabel = match ($row->price_type) {
                                            'retail' => 'ปลีก',
                                            'wholesale1' => 'ส่ง 1',
                                            'wholesale2' => 'ส่ง 2',
                                            default => $row->price_type,
                                        };
                                    @endphp
                                    <tr class="border-t border-gray-100">
                                        <td class="px-3 py-2 text-gray-700">{{ $row->created_at ? \Illuminate\Support\Carbon::parse($row->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $priceTypeLabel }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700">{{ number_format((float) $row->old_price, 2) }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700">{{ number_format((float) $row->new_price, 2) }}</td>
                                        <td class="px-3 py-2 text-right font-medium {{ $delta > 0 ? 'text-green-600' : ($delta < 0 ? 'text-red-600' : 'text-gray-700') }}">{{ $delta > 0 ? '+' : '' }}{{ number_format($delta, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-8 text-center text-gray-400">ไม่มีข้อมูล</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="history-stock-return" class="history-subtab-panel hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium">วันที่</th>
                                    <th class="px-3 py-2 text-left font-medium">Lot</th>
                                    <th class="px-3 py-2 text-left font-medium">วันหมดอายุ</th>
                                    <th class="px-3 py-2 text-right font-medium">จำนวน</th>
                                    <th class="px-3 py-2 text-left font-medium">หน่วย</th>
                                    <th class="px-3 py-2 text-left font-medium">เหตุผล</th>
                                    <th class="px-3 py-2 text-left font-medium">หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returnHistory as $row)
                                    <tr class="border-t border-gray-100">
                                        <td class="px-3 py-2 text-gray-700">{{ $row->created_at ? \Illuminate\Support\Carbon::parse($row->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $row->lot_number ?: '-' }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $row->expiry_date ? \Illuminate\Support\Carbon::parse($row->expiry_date)->format('d/m/Y') : '-' }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700">{{ number_format((float) ($row->qty ?? 0), 0) }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $baseUnitName }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $row->reason ?: '-' }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $row->note ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-3 py-8 text-center text-gray-400">ไม่มีข้อมูล</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Submit Button Row (always visible) --}}
            <div class="flex justify-end gap-3 pb-6 pt-4">
                <button type="button" onclick="window.location.reload()" class="px-6 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50">ยกเลิก</button>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium">อัปเดตสินค้า</button>
            </div>

        </div>
    </form>

    <div id="modal-edit-unit" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4">
        <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">แก้ไขหน่วยขาย</h3>
            <input
                type="text"
                id="modal-unit-input"
                placeholder="เช่น เม็ด, ขวด, หลอด"
                class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400"
            >
            <div class="mt-4 flex justify-end gap-2">
                <button
                    type="button"
                    class="px-4 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50"
                    onclick="window.closeBaseUnitModal()"
                >
                    ยกเลิก
                </button>
                <button
                    type="button"
                    class="px-4 py-2 rounded-lg bg-emerald-500 text-white text-sm hover:bg-emerald-600"
                    onclick="window.confirmBaseUnitEdit()"
                >
                    ยืนยัน
                </button>
            </div>
        </div>
    </div>

    <div id="unit-modal" class="fixed inset-0 bg-black/40 hidden z-50 items-center justify-center p-4">
        <div class="bg-white rounded-xl p-6 w-full max-w-xl shadow-xl">
            <div class="flex items-center justify-between mb-5">
                <h2 id="unit-modal-title" class="text-sm font-semibold text-gray-800">เพิ่มหน่วยสินค้า</h2>
                <button type="button" id="unit-modal-close" class="w-8 h-8 rounded-lg hover:bg-gray-100 text-gray-500">✕</button>
            </div>

            <form id="unit-modal-form" class="space-y-4" novalidate>
                <input type="hidden" id="unit-id" name="unit_id">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">ชื่อหน่วย <span class="text-red-500">*</span></label>
                        <input type="text" id="unit_name" name="unit_name" required class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">จำนวนต่อหน่วยฐาน <span class="text-red-500">*</span></label>
                        <input type="number" id="qty_per_base" name="qty_per_base" step="1" min="2" required class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                        <p id="qty-per-base-error" class="hidden mt-1 text-xs text-red-600">ต้องเป็นจำนวนเต็ม 2 ขึ้นไป</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Barcode</label>
                        <input type="text" id="barcode" name="barcode" class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">ราคา</label>
                        <input type="number" id="price_retail_unit" name="price_retail" step="0.01" min="0" class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                        <p id="price-retail-unit-warning" class="hidden mt-1 text-xs text-orange-500">กรุณากรอกราคา</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">ราคาทุนเบื้องต้น (ทุนล่าสุด × qty_per_base)</label>
                            <input type="text" id="unit-est-cost" readonly class="w-full h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">ประมาณการกำไร (ราคาปลีก - ทุน)</label>
                            <input type="text" id="unit-est-profit" readonly class="w-full h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700 font-medium">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">% เทียบราคาขาย</label>
                            <input type="text" id="unit-profit-vs-sale" readonly class="w-full h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700 font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">% เทียบราคาทุน</label>
                            <input type="text" id="unit-profit-vs-cost" readonly class="w-full h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700 font-medium">
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <p class="text-sm font-semibold text-gray-800">ช่วยคำนวณเทียบราคา</p>
                        <div class="rounded-lg border border-gray-200 bg-gray-50/70 divide-y divide-gray-200">
                            <div class="px-3 py-2 text-sm flex items-center justify-between gap-4">
                                <span id="unit-price-per-base-label" class="text-gray-600">เทียบเป็นราคาขาย ต่อ 1 ({{ $baseUnitName }})</span>
                                <span id="unit-price-per-base" class="font-medium text-gray-700">0.00</span>
                            </div>
                            <div class="px-3 py-2 text-sm flex items-center justify-between gap-4">
                                <span id="unit-base-price-label" class="text-gray-600">จากราคาขายปลีก ต่อ 1 ({{ $baseUnitName }})</span>
                                <span class="font-medium text-gray-700">{{ number_format((float) $product->price_retail, 2) }}</span>
                            </div>
                            <div class="px-3 py-2 text-sm flex items-center justify-between gap-4">
                                <span class="text-gray-600">ลูกค้าประหยัด</span>
                                <span id="unit-saving" class="font-medium text-gray-700">0.00</span>
                            </div>
                            <div class="px-3 py-2 text-sm flex items-center justify-between gap-4">
                                <span class="text-gray-600">คิดเป็น (%)</span>
                                <span id="unit-saving-pct" class="font-medium text-gray-700">0.00%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-5 pt-1">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" id="is_for_sale" name="is_for_sale" class="w-4 h-4 rounded accent-emerald-500" checked>
                        ขายได้
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" id="is_for_purchase" name="is_for_purchase" class="w-4 h-4 rounded accent-emerald-500" checked>
                        รับเข้าได้
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" id="unit-cancel" class="px-4 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit" id="unit-save" class="px-5 py-2.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div id="adjust-stock-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-800">ปรับสต็อค</h2>
                    <p class="mt-1 text-sm text-gray-500">กำหนดยอดคงเหลือใหม่ของสินค้านี้</p>
                </div>
                <button type="button" id="close-adjust-stock-modal" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100">✕</button>
            </div>

            <form id="adjust-stock-form" class="space-y-4">
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                    <p class="text-sm text-gray-500">ยอดคงเหลือปัจจุบัน (รวมทุก lot)</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-800">
                        <span id="adjust-stock-current-qty">{{ $currentStockTotal }}</span>
                        <span class="text-sm font-medium text-gray-500">{{ $baseUnitName }}</span>
                    </p>
                </div>

                <div>
                    <label for="adjust-stock-target-qty" class="mb-1 block text-sm font-medium text-gray-700">ยอดที่ต้องการให้เป็น</label>
                    <input
                        type="number"
                        id="adjust-stock-target-qty"
                        name="target_qty"
                        min="0"
                        step="1"
                        value="{{ $currentStockTotal }}"
                        class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none"
                    >
                </div>

                <div class="rounded-xl border border-dashed border-gray-200 px-4 py-3">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Preview</p>
                    <p id="adjust-stock-preview" class="mt-2 text-sm font-semibold text-gray-500">ยอดเท่าเดิม</p>
                </div>

                <div>
                    <label for="adjust-stock-note" class="mb-1 block text-sm font-medium text-gray-700">หมายเหตุ</label>
                    <textarea
                        id="adjust-stock-note"
                        name="note"
                        rows="3"
                        placeholder="ระบุเหตุผลการปรับสต็อค (ถ้ามี)"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-400 focus:outline-none"
                    ></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" id="cancel-adjust-stock" class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50">ยกเลิก</button>
                    <button type="submit" id="submit-adjust-stock" class="rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-600">ยืนยัน</button>
                </div>
            </form>
        </div>
    </div>

    <div id="stock-return-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-800">รับคืนสินค้า</h2>
                    <p class="mt-1 text-sm text-gray-500">บันทึกการรับคืนและเพิ่มจำนวนกลับเข้าสต๊อค</p>
                </div>
                <button type="button" id="close-stock-return-modal" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100">✕</button>
            </div>

            @php
                $returnableLots = $product->lots->where('qty_on_hand', '>', 0)->sortBy('expiry_date')->values();
                $returnReasons = ['ลูกค้าเปลี่ยนใจ', 'ยาผิด', 'ยาเสียหาย', 'หมดอายุ', 'อื่นๆ'];
            @endphp

            <form action="{{ route('products.stock_return', $product) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="stock-return-lot" class="mb-1 block text-sm font-medium text-gray-700">Lot</label>
                    <select id="stock-return-lot" name="lot_id" required class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none">
                        <option value="">-- เลือก lot --</option>
                        @foreach($returnableLots as $lot)
                            <option value="{{ $lot->id }}">
                                {{ $lot->lot_number }} | หมดอายุ {{ $lot->expiry_date ? $lot->expiry_date->format('d/m/Y') : '-' }} | คงเหลือ {{ $lot->qty_on_hand }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="stock-return-qty" class="mb-1 block text-sm font-medium text-gray-700">จำนวนที่รับคืน</label>
                    <input
                        type="number"
                        id="stock-return-qty"
                        name="qty"
                        min="1"
                        step="1"
                        required
                        class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none"
                    >
                </div>

                <div>
                    <label for="stock-return-reason" class="mb-1 block text-sm font-medium text-gray-700">เหตุผล</label>
                    <select id="stock-return-reason" name="reason" required class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none">
                        <option value="">-- เลือกเหตุผล --</option>
                        @foreach($returnReasons as $reason)
                            <option value="{{ $reason }}">{{ $reason }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="stock-return-note" class="mb-1 block text-sm font-medium text-gray-700">หมายเหตุ</label>
                    <textarea
                        id="stock-return-note"
                        name="note"
                        rows="3"
                        placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-400 focus:outline-none"
                    ></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" id="cancel-stock-return" class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50">ยกเลิก</button>
                    <button type="submit" class="rounded-lg bg-sky-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-sky-600">ยืนยันรับคืน</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Label Modal (Add/Edit) -->
</div>

<!-- Tab Switching & Profit Calculation JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('input[name="_token"]')?.value;
        const mainForm = document.querySelector('form[action="{{ route('products.update', $product) }}"]');
        const unitModal = document.getElementById('unit-modal');
        const unitModalTitle = document.getElementById('unit-modal-title');
        const unitModalForm = document.getElementById('unit-modal-form');
        const addUnitBtn = document.getElementById('btn-add-unit');
        const closeUnitBtn = document.getElementById('unit-modal-close');
        const cancelUnitBtn = document.getElementById('unit-cancel');
        const saveUnitBtn = document.getElementById('unit-save');
        const qtyPerBaseInput = document.getElementById('qty_per_base');
        const priceRetailUnitInput = document.getElementById('price_retail_unit');
        const unitEstCost = document.getElementById('unit-est-cost');
        const unitEstProfit = document.getElementById('unit-est-profit');
        const unitProfitVsSale = document.getElementById('unit-profit-vs-sale');
        const unitProfitVsCost = document.getElementById('unit-profit-vs-cost');
        const unitPricePerBase = document.getElementById('unit-price-per-base');
        const unitPricePerBaseLabel = document.getElementById('unit-price-per-base-label');
        const unitBasePriceLabel = document.getElementById('unit-base-price-label');
        const unitSaving = document.getElementById('unit-saving');
        const unitSavingPct = document.getElementById('unit-saving-pct');

        const storeUnitUrl = "{{ route('product_units.store', $product) }}";
        const autoSaveUrl = "{{ route('products.autosave', $product) }}";
        const adjustStockUrl = "{{ route('products.adjust_stock', $product) }}";
        const unitBaseUrl = "{{ url('/products/units') }}";
        const toggleDisabledBaseUrl = "{{ url('/products/' . $product->id . '/units') }}";
        const modalLatestCost = parseFloat(document.querySelector('[data-latest-cost]')?.getAttribute('data-latest-cost')) || 0;
        const modalBaseUnitPrice = {{ (float) $product->price_retail }};
        const modalBaseUnitName = "{{ $baseUnitName }}";
        const baseUnitModal = document.getElementById('modal-edit-unit');
        const baseUnitInput = document.getElementById('modal-unit-input');
        const baseUnitHidden = document.getElementById('base-unit-hidden');
        const baseUnitDisplay = document.getElementById('base-unit-display');
        const retailUnitText = document.getElementById('retail-unit-text');
        const adjustStockModal = document.getElementById('adjust-stock-modal');
        const openAdjustStockModalBtn = document.getElementById('open-adjust-stock-modal');
        const closeAdjustStockModalBtn = document.getElementById('close-adjust-stock-modal');
        const cancelAdjustStockBtn = document.getElementById('cancel-adjust-stock');
        const adjustStockForm = document.getElementById('adjust-stock-form');
        const adjustStockCurrentQtyEl = document.getElementById('adjust-stock-current-qty');
        const adjustStockTargetQtyInput = document.getElementById('adjust-stock-target-qty');
        const adjustStockNoteInput = document.getElementById('adjust-stock-note');
        const adjustStockPreviewEl = document.getElementById('adjust-stock-preview');
        const submitAdjustStockBtn = document.getElementById('submit-adjust-stock');
        const stockReturnModal = document.getElementById('stock-return-modal');
        const openStockReturnModalBtn = document.getElementById('open-stock-return-modal');
        const closeStockReturnModalBtn = document.getElementById('close-stock-return-modal');
        const cancelStockReturnBtn = document.getElementById('cancel-stock-return');
        const initialCurrentStockQty = Number(@json($currentStockTotal));
        let modalMode = 'create';
        let isDirty = false;

        window.closeBaseUnitModal = function() {
            baseUnitModal?.classList.add('hidden');
            baseUnitModal?.classList.remove('flex');
        };

        async function triggerAutoSave(successMessage = 'บันทึกแล้ว') {
            console.log('triggerAutoSave called, isDirty:', isDirty, new Error().stack);
            if (!mainForm) return false;

            const formData = new FormData(mainForm);
            // sync base_unit_name จาก hidden input เสมอ
            const baseUnit = document.getElementById('base-unit-hidden');
            if (baseUnit) formData.set('base_unit_name', baseUnit.value);
            formData.append('_method', 'PUT');

            formData.set('_method', 'PUT');
            const response = await fetch(autoSaveUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(data.message || 'autosave failed');
            }

            showAutosaveToast(successMessage);
            isDirty = false;
            return true;
        }

        window.confirmBaseUnitEdit = async function() {
            const unitName = (baseUnitInput?.value || '').trim();
            if (!unitName) {
                notify('กรุณาระบุหน่วยขาย', 'error');
                return;
            }

            if (baseUnitHidden) baseUnitHidden.value = unitName;
            if (baseUnitDisplay) baseUnitDisplay.textContent = unitName;
            if (retailUnitText) retailUnitText.textContent = `ต่อ 1 (${unitName})`;

            window.closeBaseUnitModal();
            mainForm.submit(); // submit form ตรงๆ → refresh หน้าเหมือนกดปุ่มอัพเดต
        };

        function notify(message, type = 'info') {
            if (typeof showToast === 'function') {
                showToast(message, type);
            } else {
                console.log(`[${type}] ${message}`);
            }
        }

        function showAutosaveToast(message = 'บันทึกแล้ว') {
            if (typeof showToast === 'function') {
                showToast(message, 'success');
            }
        }

        function openAdjustStockModal() {
            if (!adjustStockModal) return;
            adjustStockCurrentQtyEl.textContent = String(initialCurrentStockQty);
            adjustStockTargetQtyInput.value = String(initialCurrentStockQty);
            adjustStockNoteInput.value = '';
            updateAdjustStockPreview();
            adjustStockModal.classList.remove('hidden');
            adjustStockModal.classList.add('flex');
            adjustStockTargetQtyInput.focus();
            adjustStockTargetQtyInput.select();
        }

        function closeAdjustStockModal() {
            if (!adjustStockModal) return;
            adjustStockModal.classList.add('hidden');
            adjustStockModal.classList.remove('flex');
        }

        function openStockReturnModal() {
            if (!stockReturnModal) return;
            stockReturnModal.classList.remove('hidden');
            stockReturnModal.classList.add('flex');
        }

        function closeStockReturnModal() {
            if (!stockReturnModal) return;
            stockReturnModal.classList.add('hidden');
            stockReturnModal.classList.remove('flex');
        }

        function updateAdjustStockPreview() {
            if (!adjustStockPreviewEl || !adjustStockTargetQtyInput) return;

            const targetQty = parseInt(adjustStockTargetQtyInput.value || '0', 10);
            const safeTargetQty = Number.isNaN(targetQty) ? 0 : Math.max(0, targetQty);
            const diff = safeTargetQty - initialCurrentStockQty;

            adjustStockPreviewEl.classList.remove('text-green-600', 'text-red-600', 'text-gray-500');

            if (diff > 0) {
                adjustStockPreviewEl.classList.add('text-green-600');
                adjustStockPreviewEl.textContent = `จะเพิ่ม +${diff}`;
                return;
            }

            if (diff < 0) {
                adjustStockPreviewEl.classList.add('text-red-600');
                adjustStockPreviewEl.textContent = `จะลด ${Math.abs(diff)}`;
                return;
            }

            adjustStockPreviewEl.classList.add('text-gray-500');
            adjustStockPreviewEl.textContent = 'ยอดเท่าเดิม';
        }

        async function submitAdjustStock(e) {
            e.preventDefault();
            if (!adjustStockTargetQtyInput) return;

            const targetQty = parseInt(adjustStockTargetQtyInput.value || '0', 10);
            if (Number.isNaN(targetQty) || targetQty < 0) {
                showToast?.('กรุณากรอกจำนวนเต็มตั้งแต่ 0 ขึ้นไป', 'error');
                adjustStockTargetQtyInput.focus();
                return;
            }

            const formData = new FormData();
            formData.append('target_qty', String(targetQty));
            formData.append('note', adjustStockNoteInput?.value || '');

            submitAdjustStockBtn.disabled = true;

            try {
                const response = await fetch(adjustStockUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.message || 'ไม่สามารถปรับสต็อคได้');
                }

                if (typeof showToast === 'function') {
                    showToast(data.message || 'ปรับสต็อคเรียบร้อยแล้ว', 'success');
                }

                window.location.hash = 'tab-5';
                window.location.reload();
            } catch (error) {
                if (typeof showToast === 'function') {
                    showToast(error.message || 'ไม่สามารถปรับสต็อคได้', 'error');
                } else {
                    alert(error.message || 'ไม่สามารถปรับสต็อคได้');
                }
            } finally {
                submitAdjustStockBtn.disabled = false;
            }
        }

        function activateTab(button, targetTab) {
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'text-emerald-600', 'border-emerald-600');
                btn.classList.add('text-gray-600', 'border-transparent');
            });
            tabPanels.forEach(panel => {
                panel.classList.add('hidden');
                panel.classList.remove('active');
            });

            button.classList.add('active', 'text-emerald-600', 'border-emerald-600');
            button.classList.remove('text-gray-600', 'border-transparent');
            document.getElementById(targetTab).classList.remove('hidden');
            document.getElementById(targetTab).classList.add('active');
        }

        function openModal() {
            unitModal.classList.add('flex');
            unitModal.classList.remove('hidden');
        }

        function closeModal() {
            unitModal.classList.remove('flex');
            unitModal.classList.add('hidden');
        }

        function resetUnitForm() {
            unitModalForm.reset();
            document.getElementById('unit-id').value = '';
            qtyPerBaseInput.value = '';
            document.getElementById('is_for_sale').checked = true;
            document.getElementById('is_for_purchase').checked = true;
        }

        function collectUnitPayload() {
            return {
                unit_name: document.getElementById('unit_name').value,
                qty_per_base: document.getElementById('qty_per_base').value,
                barcode: document.getElementById('barcode').value,
                price_retail: document.getElementById('price_retail_unit').value,
                is_for_sale: document.getElementById('is_for_sale').checked ? '1' : '0',
                is_for_purchase: document.getElementById('is_for_purchase').checked ? '1' : '0',
            };
        }

        function parseErrorMessage(data) {
            if (!data) return 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
            if (data.message && !data.errors) return data.message;
            if (data.errors) {
                const messages = Object.values(data.errors).flat();
                return messages.join('\n');
            }
            return 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
        }

        function formatMoney(value) {
            return Number(value || 0).toFixed(2);
        }

        function setSignedClass(el, value) {
            if (!el) return;
            el.classList.remove('text-red-600', 'text-green-600', 'text-gray-700');
            if (value > 0) {
                el.classList.add('text-green-600');
            } else if (value < 0) {
                el.classList.add('text-red-600');
            } else {
                el.classList.add('text-gray-700');
            }
        }

        function updateUnitCompareCalculation() {
            const priceRetailUnit = parseFloat(priceRetailUnitInput?.value) || 0;
            const qtyPerBase = parseFloat(qtyPerBaseInput?.value) || 0;

            if (unitPricePerBaseLabel) unitPricePerBaseLabel.textContent = `เทียบเป็นราคาขาย ต่อ 1 (${modalBaseUnitName})`;
            if (unitBasePriceLabel) unitBasePriceLabel.textContent = `จากราคาขายปลีก ต่อ 1 (${modalBaseUnitName})`;

            const costThisUnit = modalLatestCost * qtyPerBase;
            const profitPerUnit = priceRetailUnit - costThisUnit;
            const profitVsSale = priceRetailUnit > 0 ? (profitPerUnit / priceRetailUnit) * 100 : 0;
            const profitVsCost = costThisUnit > 0 ? (profitPerUnit / costThisUnit) * 100 : 0;
            const pricePerBase = qtyPerBase > 0 ? priceRetailUnit / qtyPerBase : 0;
            const saving = modalBaseUnitPrice - pricePerBase;
            const savingPct = modalBaseUnitPrice > 0 ? (saving / modalBaseUnitPrice) * 100 : 0;

            if (unitEstCost) unitEstCost.value = formatMoney(costThisUnit);
            if (unitEstProfit) unitEstProfit.value = formatMoney(profitPerUnit);
            if (unitProfitVsSale) unitProfitVsSale.value = `${formatMoney(profitVsSale)}%`;
            if (unitProfitVsCost) unitProfitVsCost.value = `${formatMoney(profitVsCost)}%`;
            if (unitPricePerBase) unitPricePerBase.textContent = formatMoney(pricePerBase);
            if (unitSaving) unitSaving.textContent = formatMoney(saving);
            if (unitSavingPct) unitSavingPct.textContent = `${formatMoney(savingPct)}%`;

            setSignedClass(unitEstProfit, profitPerUnit);
            setSignedClass(unitProfitVsSale, profitVsSale);
            setSignedClass(unitProfitVsCost, profitVsCost);
            setSignedClass(unitSaving, saving);
            setSignedClass(unitSavingPct, savingPct);
            setSignedClass(unitPricePerBase, modalBaseUnitPrice - pricePerBase);
        }

        const qtyPerBaseError = document.getElementById('qty-per-base-error');
        const priceRetailUnitWarning = document.getElementById('price-retail-unit-warning');

        qtyPerBaseInput?.addEventListener('input', function() {
            const val = parseFloat(this.value);
            const invalid = isNaN(val) || val < 2 || !Number.isInteger(val);
            qtyPerBaseError?.classList.toggle('hidden', !invalid);
        });

        priceRetailUnitInput?.addEventListener('blur', function() {
            const val = parseFloat(this.value);
            if (isNaN(val) || this.value.trim() === '') this.value = '0';
            priceRetailUnitWarning?.classList.toggle('hidden', parseFloat(this.value) > 0);
        });

        async function submitUnitForm(e) {
            e.preventDefault();
            if (!unitModalForm.reportValidity()) return;

            const qtyVal = parseFloat(qtyPerBaseInput?.value);
            if (isNaN(qtyVal) || qtyVal < 2 || !Number.isInteger(qtyVal)) {
                qtyPerBaseError?.classList.remove('hidden');
                qtyPerBaseInput?.focus();
                return;
            }

            const priceVal = parseFloat(priceRetailUnitInput?.value) || 0;
            priceRetailUnitWarning?.classList.toggle('hidden', priceVal > 0);

            const unitId = document.getElementById('unit-id').value;
            const payload = collectUnitPayload();
            const isEdit = modalMode === 'edit' && unitId;
            const targetUrl = isEdit ? `${unitBaseUrl}/${unitId}` : storeUnitUrl;

            const formData = new FormData();
            Object.entries(payload).forEach(([key, value]) => formData.append(key, value));
            if (isEdit) formData.append('_method', 'PUT');

            saveUnitBtn.disabled = true;

            try {
                const response = await fetch(targetUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(parseErrorMessage(data));
                }

                if (typeof showToast === 'function') {
                    showToast(data.message || 'บันทึกสำเร็จ', 'success');
                }

                window.location.reload();
            } catch (error) {
                if (typeof showToast === 'function') {
                    showToast(error.message, 'error');
                } else {
                    alert(error.message);
                }
            } finally {
                saveUnitBtn.disabled = false;
            }
        }

        window.openUnitModal = function(unit = null) {
            resetUnitForm();

            if (unit && unit.mode === 'edit') {
                modalMode = 'edit';
                unitModalTitle.textContent = 'แก้ไขหน่วยสินค้า';
                document.getElementById('unit-id').value = unit.id;
                document.getElementById('unit_name').value = unit.unit_name ?? '';
                document.getElementById('qty_per_base').value = unit.qty_per_base ?? '';
                document.getElementById('barcode').value = unit.barcode ?? '';
                document.getElementById('price_retail_unit').value = unit.price_retail ?? '';
                document.getElementById('is_for_sale').checked = !!unit.is_for_sale;
                document.getElementById('is_for_purchase').checked = !!unit.is_for_purchase;
            } else {
                modalMode = 'create';
                unitModalTitle.textContent = 'เพิ่มหน่วยสินค้า';
            }

            updateUnitCompareCalculation();
            openModal();
        };

        window.deleteUnit = async function(unitId) {
            if (!confirm('ยืนยันการลบหน่วยนี้?')) return;

            const formData = new FormData();
            formData.append('_method', 'DELETE');

            try {
                const response = await fetch(`${unitBaseUrl}/${unitId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(parseErrorMessage(data));
                }

                if (typeof showToast === 'function') {
                    showToast(data.message || 'ลบสำเร็จ', 'success');
                }

                window.location.reload();
            } catch (error) {
                if (typeof showToast === 'function') {
                    showToast(error.message, 'error');
                } else {
                    alert(error.message);
                }
            }
        };

        window.toggleUnitDisabled = async function(unitId, buttonEl) {
            if (!unitId || !buttonEl) return;

            buttonEl.disabled = true;

            try {
                const response = await fetch(`${toggleDisabledBaseUrl}/${unitId}/toggle-disabled`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'เปลี่ยนสถานะไม่สำเร็จ');
                }

                const isDisabled = !!data.is_disabled;
                buttonEl.dataset.isDisabled = isDisabled ? '1' : '0';
                buttonEl.title = isDisabled ? 'ปิดใช้งาน' : 'เปิดใช้งาน';
                buttonEl.classList.toggle('bg-blue-500', !isDisabled);
                buttonEl.classList.toggle('bg-gray-300', isDisabled);

                const knob = buttonEl.querySelector('span');
                knob?.classList.toggle('translate-x-6', !isDisabled);
                knob?.classList.toggle('translate-x-1', isDisabled);

                if (typeof showToast === 'function') {
                    showToast('อัปเดตสถานะหน่วยสินค้าเรียบร้อยแล้ว', 'success');
                }
            } catch (error) {
                if (typeof showToast === 'function') {
                    showToast(error.message || 'เปลี่ยนสถานะไม่สำเร็จ', 'error');
                }
            } finally {
                buttonEl.disabled = false;
            }
        };

        addUnitBtn?.addEventListener('click', () => window.openUnitModal());
        closeUnitBtn?.addEventListener('click', closeModal);
        cancelUnitBtn?.addEventListener('click', closeModal);
        openAdjustStockModalBtn?.addEventListener('click', openAdjustStockModal);
        closeAdjustStockModalBtn?.addEventListener('click', closeAdjustStockModal);
        cancelAdjustStockBtn?.addEventListener('click', closeAdjustStockModal);
        openStockReturnModalBtn?.addEventListener('click', openStockReturnModal);
        closeStockReturnModalBtn?.addEventListener('click', closeStockReturnModal);
        cancelStockReturnBtn?.addEventListener('click', closeStockReturnModal);
        adjustStockForm?.addEventListener('submit', submitAdjustStock);
        adjustStockTargetQtyInput?.addEventListener('input', updateAdjustStockPreview);
        unitModalForm?.addEventListener('submit', submitUnitForm);
        priceRetailUnitInput?.addEventListener('input', updateUnitCompareCalculation);
        qtyPerBaseInput?.addEventListener('input', updateUnitCompareCalculation);

        // --- Label Tab ---
        const productId = {{ $product->id }};
        const productName = @json($product->name_for_print ?: $product->trade_name);
        window.productName = productName; // Make it globally accessible for label preview
        const labelApiBase = `/products/${productId}/labels`;
        const labelLookupUrls = {
            frequencies: `/api/label-frequencies`,
            mealRelations: `/api/label-meal-relations`,
            dosages: `/api/label-dosages`,
            times: `/api/label-times`,
            advices: `/api/label-advices`,
        };
        const labelLookups = {
            frequencies: [],
            mealRelations: [],
            dosages: [],
            times: [],
            advices: [],
        };
        const labelSearchConfigs = [
            { key: 'dosages', field: 'dosage_id' },
            { key: 'frequencies', field: 'frequency_id' },
            { key: 'mealRelations', field: 'meal_relation_id' },
            { key: 'times', field: 'label_time_id' },
            { key: 'advices', field: 'advice_id' },
        ];


        // btn-add-label (append new inline form)
        document.getElementById('btn-add-label')?.addEventListener('click', function () {
            // Don't add duplicate "new" card
            if (document.querySelector('.label-inline-form[data-label-id="new"]')) {
                document.querySelector('.label-inline-form[data-label-id="new"]')
                    .closest('.label-card')?.scrollIntoView({ behavior: 'smooth' });
                return;
            }

            const container = document.getElementById('labels-list-container');
            if (!container) return;

            const newCard = document.createElement('div');
            newCard.className = 'label-card bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4 flex gap-4';
            newCard.innerHTML = `
                <!-- Left: Empty Preview -->
                <div class="flex-shrink-0 w-[400px]">
                    <div class="border-2 border-dashed border-gray-300 rounded bg-gray-50 flex items-center justify-center" style="height:150px;">
                        <span class="text-gray-400 text-sm">preview จะแสดงหลังบันทึก</span>
                    </div>
                </div>
                <!-- Right: Inline form for new label -->
                <div class="flex-1">
                    <form class="label-inline-form space-y-3" data-label-id="new">
                        <input type="hidden" id="label-id-new" name="label_id" value="">
                        <div>
                            <label for="label_name-new" class="block text-xs font-medium text-gray-600 mb-1">ชื่อฉลาก</label>
                            <input type="text" id="label_name-new" name="label_name" required placeholder="ชื่อเรียกฉลาก"
                                class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 text-sm focus:outline-none focus:border-emerald-400">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="dosage_id_search-new" class="block text-xs font-medium text-gray-600 mb-1">ปริมาณที่ใช้</label>
                                <input type="hidden" name="dosage_id" id="dosage_id_hidden-new">
                                <div class="relative">
                                    <input type="text" id="dosage_id_search-new" autocomplete="off" placeholder="ค้นหาปริมาณที่ใช้"
                                        class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 pr-10 text-sm focus:outline-none focus:border-emerald-400">
                                    <button type="button" id="clear-dosage_id-new" class="absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 rounded hover:bg-gray-100 text-gray-600 hidden">×</button>
                                    <div id="dosage_id_dropdown-new" class="hidden absolute z-30 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-56 overflow-auto"></div>
                                </div>
                            </div>
                            <div>
                                <label for="frequency_id_search-new" class="block text-xs font-medium text-gray-600 mb-1">ความถี่</label>
                                <input type="hidden" name="frequency_id" id="frequency_id_hidden-new">
                                <div class="relative">
                                    <input type="text" id="frequency_id_search-new" autocomplete="off" placeholder="ค้นหาความถี่"
                                        class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 pr-10 text-sm focus:outline-none focus:border-emerald-400">
                                    <button type="button" id="clear-frequency_id-new" class="absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 rounded hover:bg-gray-100 text-gray-600 hidden">×</button>
                                    <div id="frequency_id_dropdown-new" class="hidden absolute z-40 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-56 overflow-auto"></div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="meal_relation_id_search-new" class="block text-xs font-medium text-gray-600 mb-1">รูปแบบการทาน</label>
                                <input type="hidden" name="meal_relation_id" id="meal_relation_id_hidden-new">
                                <div class="relative">
                                    <input type="text" id="meal_relation_id_search-new" autocomplete="off" placeholder="ค้นหารูปแบบการทาน"
                                        class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 pr-10 text-sm focus:outline-none focus:border-emerald-400">
                                    <button type="button" id="clear-meal_relation_id-new" class="absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 rounded hover:bg-gray-100 text-gray-600 hidden">×</button>
                                    <div id="meal_relation_id_dropdown-new" class="hidden absolute z-40 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-56 overflow-auto"></div>
                                </div>
                            </div>
                            <div>
                                <label for="label_time_id_search-new" class="block text-xs font-medium text-gray-600 mb-1">เวลาทานยา</label>
                                <input type="hidden" name="label_time_id" id="label_time_id_hidden-new">
                                <div class="relative">
                                    <input type="text" id="label_time_id_search-new" autocomplete="off" placeholder="ค้นหาเวลาทานยา"
                                        class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 pr-10 text-sm focus:outline-none focus:border-emerald-400">
                                    <button type="button" id="clear-label_time_id-new" class="absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 rounded hover:bg-gray-100 text-gray-600 hidden">×</button>
                                    <div id="label_time_id_dropdown-new" class="hidden absolute z-40 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-56 overflow-auto"></div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label for="indication_th-new" class="block text-xs font-medium text-gray-600 mb-1">ข้อบ่งใช้ไทย</label>
                                <textarea id="indication_th-new" name="indication_th" rows="2"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:border-emerald-400"></textarea>
                            </div>
                            <div>
                                <label for="indication_mm-new" class="block text-xs font-medium text-gray-600 mb-1">ข้อบ่งใช้พม่า</label>
                                <textarea id="indication_mm-new" name="indication_mm" rows="2"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:border-emerald-400"></textarea>
                            </div>
                            <div>
                                <label for="indication_zh-new" class="block text-xs font-medium text-gray-600 mb-1">ข้อบ่งใช้จีน</label>
                                <textarea id="indication_zh-new" name="indication_zh" rows="2"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:border-emerald-400"></textarea>
                            </div>
                        </div>
                        <div>
                            <label for="advice_id_search-new" class="block text-xs font-medium text-gray-600 mb-1">คำแนะนำ</label>
                            <input type="hidden" name="advice_id" id="advice_id_hidden-new">
                            <div class="relative">
                                <input type="text" id="advice_id_search-new" autocomplete="off" placeholder="ค้นหาคำแนะนำ"
                                    class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 pr-10 text-sm focus:outline-none focus:border-emerald-400">
                                <button type="button" id="clear-advice_id-new" class="absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 rounded hover:bg-gray-100 text-gray-600 hidden">×</button>
                                <div id="advice_id_dropdown-new" class="hidden absolute z-40 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-56 overflow-auto"></div>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-5 mt-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" id="show_barcode-new" name="show_barcode" value="1" class="sr-only peer">
                                <span class="text-sm font-medium text-gray-600">แสดงบาร์โค้ด</span>
                                <div class="relative w-14 h-7 rounded-full bg-gray-100 peer-checked:bg-emerald-500 transition-colors duration-300 ease-in-out after:content-[''] after:absolute after:top-1 after:left-1 after:h-5 after:w-7 after:rounded-full after:bg-white after:shadow-sm after:translate-x-0 peer-checked:after:translate-x-5 after:transition-transform after:duration-300 after:ease-in-out"></div>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" id="is_default-new" name="is_default" value="1" class="sr-only peer">
                                <span class="text-sm font-medium text-gray-600">ตั้งเป็นฉลากเริ่มต้น</span>
                                <div class="relative w-14 h-7 rounded-full bg-gray-100 peer-checked:bg-amber-500 transition-colors duration-300 ease-in-out after:content-[''] after:absolute after:top-1 after:left-1 after:h-5 after:w-7 after:rounded-full after:bg-white after:shadow-sm after:translate-x-0 peer-checked:after:translate-x-5 after:transition-transform after:duration-300 after:ease-in-out"></div>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" id="is_active-new" name="is_active" value="1" class="sr-only peer" checked>
                                <span class="text-sm font-medium text-gray-600">เปิดใช้งาน</span>
                                <div class="relative w-14 h-7 rounded-full bg-gray-100 peer-checked:bg-emerald-500 transition-colors duration-300 ease-in-out after:content-[''] after:absolute after:top-1 after:left-1 after:h-5 after:w-7 after:rounded-full after:bg-white after:shadow-sm after:translate-x-0 peer-checked:after:translate-x-5 after:transition-transform after:duration-300 after:ease-in-out"></div>
                            </label>
                        </div>
                        <div class="flex gap-2 pt-3 border-t border-gray-100 mt-2">
                            <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium">💾 บันทึก</button>
                            <button type="button" class="cancel-new-label px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium">ยกเลิก</button>
                        </div>
                    </form>
                </div>
            `;

            container.appendChild(newCard);
            newCard.scrollIntoView({ behavior: 'smooth' });

            // Bind cancel button
            newCard.querySelector('.cancel-new-label')?.addEventListener('click', () => newCard.remove());

            // Bind autocomplete for new form
            const newForm = newCard.querySelector('.label-inline-form');
            if (newForm) {
                ['dosage', 'frequency', 'meal_relation', 'label_time', 'advice'].forEach(type => {
                    const fieldMap = {
                        dosage: 'dosage_id',
                        frequency: 'frequency_id',
                        meal_relation: 'meal_relation_id',
                        label_time: 'label_time_id',
                        advice: 'advice_id',
                    };
                    const field = fieldMap[type];
                    initializeLabelAutocomplete({
                        searchInput: newForm.querySelector(`#${field}_search-new`),
                        hiddenInput: newForm.querySelector(`#${field}_hidden-new`),
                        dropdown: newForm.querySelector(`#${field}_dropdown-new`),
                        clearBtn: newForm.querySelector(`#clear-${field}-new`),
                        type: type,
                    });
                });
            }

            // Bind form submit for new label
            newForm?.addEventListener('submit', async function (e) {
                e.preventDefault();
                const labelName = newForm.querySelector('#label_name-new')?.value.trim();
                if (!labelName) {
                    newForm.querySelector('#label_name-new')?.focus();
                    return;
                }
                const formData = new FormData(newForm);
                formData.set('show_barcode', newForm.querySelector('#show_barcode-new')?.checked ? '1' : '0');
                formData.set('is_default', newForm.querySelector('#is_default-new')?.checked ? '1' : '0');
                formData.set('is_active', newForm.querySelector('#is_active-new')?.checked ? '1' : '0');

                try {
                    const response = await fetch(labelApiBase, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: formData,
                    });
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(data.message || 'บันทึกไม่สำเร็จ');
                    showToast(data.message || 'เพิ่มฉลากสำเร็จ', 'success');
                    loadLabels();
                } catch (error) {
                    showToast(error.message, 'error');
                }
            });
        });

        // modal close/cancel
        document.getElementById('label-modal-close')?.addEventListener('click', closeLabelModal);
        document.getElementById('label-modal-cancel')?.addEventListener('click', closeLabelModal);

        // tab-labels click → loadLabels
        document.querySelector('[data-tab="tab-labels"]')?.addEventListener('click', function() {
            loadLabels();
        });

        // label form submit
        document.getElementById('label-modal-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const formData = new FormData(e.target);
            const labelName = document.getElementById('label_name').value.trim();
            if (!labelName) {
                showToast('กรุณากรอกชื่อฉลาก', 'error');
                document.getElementById('label_name').focus();
                return;
            }
            formData.set('show_barcode', document.getElementById('show_barcode')?.checked ? '1' : '0');
            formData.set('is_default', document.getElementById('is_default')?.checked ? '1' : '0');
            formData.set('is_active', document.getElementById('is_active')?.checked ? '1' : '0');
            fetch(labelApiBase, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData,
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok || data.success === false) {
                    throw new Error(data.message || 'บันทึกฉลากล้มเหลว');
                }
                return data;
            })
            .then(() => {
                closeLabelModal();
                loadLabels();
                showToast?.('บันทึกฉลากเรียบร้อยแล้ว', 'success');
            })
            .catch((error) => showToast(error.message || 'บันทึกฉลากล้มเหลว', 'error'));
        });

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, (char) => {
                const entities = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
                return entities[char] || char;
            });
        }

        function getLabelLookupItem(key, id) {
            if (!id) return null;
            return (labelLookups[key] || []).find(item => Number(item.id) === Number(id)) || null;
        }

        function setAutocompleteValue(field, id, text = '') {
            const hiddenInput = document.getElementById(`${field}_hidden`);
            const searchInput = document.getElementById(`${field}_search`);
            const clearButton = document.getElementById(`clear-${field}`);
            if (!hiddenInput || !searchInput) return;

            hiddenInput.value = id ? String(id) : '';
            searchInput.value = text || '';
            clearButton?.classList.toggle('hidden', !searchInput.value);
        }

        function setupAutocomplete(field, key) {
            const hiddenInput = document.getElementById(`${field}_hidden`);
            const searchInput = document.getElementById(`${field}_search`);
            const dropdown = document.getElementById(`${field}_dropdown`);
            const clearButton = document.getElementById(`clear-${field}`);
            if (!hiddenInput || !searchInput || !dropdown) return;

            const renderOptions = (query = '') => {
                const q = query.trim().toLowerCase();
                const options = (labelLookups[key] || []).filter(item => String(item.name_th || '').toLowerCase().includes(q));

                if (!options.length) {
                    dropdown.innerHTML = '<div class="px-3 py-2 text-sm text-gray-400">ไม่พบข้อมูล</div>';
                    dropdown.classList.remove('hidden');
                    return;
                }

                dropdown.innerHTML = options
                    .map(item => `<button type="button" class="label-search-option w-full text-left px-3 py-2 text-sm  hover:bg-gray-100" data-id="${item.id}" data-name="${escapeHtml(item.name_th || '')}">${escapeHtml(item.name_th || '')}</button>`)
                    .join('');

                dropdown.classList.remove('hidden');

                dropdown.querySelectorAll('.label-search-option').forEach(btn => {
                    btn.addEventListener('click', function() {
                        setAutocompleteValue(field, this.dataset.id || '', this.dataset.name || '');
                        dropdown.classList.add('hidden');
                    });
                });
            };

            searchInput.addEventListener('focus', () => renderOptions(searchInput.value || ''));
            searchInput.addEventListener('input', () => {
                hiddenInput.value = '';
                clearButton?.classList.toggle('hidden', (searchInput.value || '').length === 0);
                renderOptions(searchInput.value || '');
            });

            clearButton?.addEventListener('click', () => {
                setAutocompleteValue(field, '', '');
                dropdown.classList.add('hidden');
                searchInput.focus();
            });
        }

        function initializeLabelAutocomplete(config = null) {
            if (!config) {
                labelSearchConfigs.forEach(item => setupAutocomplete(item.field, item.key));
                return;
            }

            const { searchInput, hiddenInput, dropdown, clearBtn, type } = config;
            if (!searchInput || !hiddenInput || !dropdown) return;

            const lookupMap = {
                dosage: 'dosages',
                frequency: 'frequencies',
                meal_relation: 'mealRelations',
                label_time: 'times',
                advice: 'advices',
            };

            const lookupKey = lookupMap[type];
            if (!lookupKey) return;

            const renderOptions = (query = '') => {
                const q = String(query || '').trim().toLowerCase();
                const options = (labelLookups[lookupKey] || []).filter(item => String(item.name_th || '').toLowerCase().includes(q));

                if (!options.length) {
                    dropdown.innerHTML = '<div class="px-3 py-2 text-sm text-gray-400">ไม่พบข้อมูล</div>';
                    dropdown.classList.remove('hidden');
                    return;
                }

                dropdown.innerHTML = options
                    .map(item => `<button type="button" class="label-search-option w-full text-left px-3 py-2 text-sm hover:bg-gray-100" data-id="${item.id}" data-name="${escapeHtml(item.name_th || '')}">${escapeHtml(item.name_th || '')}</button>`)
                    .join('');

                dropdown.classList.remove('hidden');
                dropdown.querySelectorAll('.label-search-option').forEach(btn => {
                    btn.addEventListener('click', function() {
                        hiddenInput.value = this.dataset.id || '';
                        searchInput.value = this.dataset.name || '';
                        clearBtn?.classList.toggle('hidden', !searchInput.value);
                        dropdown.classList.add('hidden');
                    });
                });
            };

            searchInput.addEventListener('focus', () => renderOptions(searchInput.value || ''));
            searchInput.addEventListener('input', () => {
                hiddenInput.value = '';
                clearBtn?.classList.toggle('hidden', (searchInput.value || '').length === 0);
                renderOptions(searchInput.value || '');
            });

            clearBtn?.addEventListener('click', () => {
                hiddenInput.value = '';
                searchInput.value = '';
                clearBtn.classList.add('hidden');
                dropdown.classList.add('hidden');
                searchInput.focus();
            });
        }

        // Autocomplete initialization for all inline forms
        function initializeAllLabelAutocompletes() {
            document.querySelectorAll('.label-inline-form').forEach(form => {
                const labelId = form.dataset.labelId;
                // Dosage
                initializeLabelAutocomplete({
                    searchInput: form.querySelector(`#dosage_id_search-${labelId}`),
                    hiddenInput: form.querySelector(`#dosage_id_hidden-${labelId}`),
                    dropdown: form.querySelector(`#dosage_id_dropdown-${labelId}`),
                    clearBtn: form.querySelector(`#clear-dosage_id-${labelId}`),
                    type: 'dosage',
                });
                // Frequency
                initializeLabelAutocomplete({
                    searchInput: form.querySelector(`#frequency_id_search-${labelId}`),
                    hiddenInput: form.querySelector(`#frequency_id_hidden-${labelId}`),
                    dropdown: form.querySelector(`#frequency_id_dropdown-${labelId}`),
                    clearBtn: form.querySelector(`#clear-frequency_id-${labelId}`),
                    type: 'frequency',
                });
                // Meal Relation
                initializeLabelAutocomplete({
                    searchInput: form.querySelector(`#meal_relation_id_search-${labelId}`),
                    hiddenInput: form.querySelector(`#meal_relation_id_hidden-${labelId}`),
                    dropdown: form.querySelector(`#meal_relation_id_dropdown-${labelId}`),
                    clearBtn: form.querySelector(`#clear-meal_relation_id-${labelId}`),
                    type: 'meal_relation',
                });
                // Label Time
                initializeLabelAutocomplete({
                    searchInput: form.querySelector(`#label_time_id_search-${labelId}`),
                    hiddenInput: form.querySelector(`#label_time_id_hidden-${labelId}`),
                    dropdown: form.querySelector(`#label_time_id_dropdown-${labelId}`),
                    clearBtn: form.querySelector(`#clear-label_time_id-${labelId}`),
                    type: 'label_time',
                });
                // Advice
                initializeLabelAutocomplete({
                    searchInput: form.querySelector(`#advice_id_search-${labelId}`),
                    hiddenInput: form.querySelector(`#advice_id_hidden-${labelId}`),
                    dropdown: form.querySelector(`#advice_id_dropdown-${labelId}`),
                    clearBtn: form.querySelector(`#clear-advice_id-${labelId}`),
                    type: 'advice',
                });
            });
        }

        function openLabelModal(label = null) {
            const modal = document.getElementById('label-modal');
            if (!modal) return;
            document.getElementById('label-modal-form').reset();

            document.getElementById('label-id').value = label ? label.id : '';
            document.getElementById('label_name').value = label ? (label.label_name || '') : '';

            const dosage = label ? getLabelLookupItem('dosages', label.dosage_id) : null;
            const frequency = label ? getLabelLookupItem('frequencies', label.frequency_id) : null;
            const mealRelation = label ? getLabelLookupItem('mealRelations', label.meal_relation_id) : null;
            const labelTime = label ? getLabelLookupItem('times', label.label_time_id) : null;
            const advice = label ? getLabelLookupItem('advices', label.advice_id) : null;

            setAutocompleteValue('dosage_id', label ? (label.dosage_id || '') : '', label ? (label.dosage_name || dosage?.name_th || '') : '');
            setAutocompleteValue('frequency_id', label ? (label.frequency_id || '') : '', label ? (label.frequency_name || frequency?.name_th || '') : '');
            setAutocompleteValue('meal_relation_id', label ? (label.meal_relation_id || '') : '', label ? (label.meal_relation_name || mealRelation?.name_th || '') : '');
            setAutocompleteValue('label_time_id', label ? (label.label_time_id || '') : '', label ? (label.label_time_name || labelTime?.name_th || '') : '');
            setAutocompleteValue('advice_id', label ? (label.advice_id || '') : '', label ? (label.advice_name || advice?.name_th || '') : '');

            document.getElementById('indication_th').value = label ? (label.indication_th || '') : '';
            document.getElementById('indication_mm').value = label ? (label.indication_mm || '') : '';
            document.getElementById('indication_zh').value = label ? (label.indication_zh || '') : '';
            document.getElementById('show_barcode').checked = label ? !!label.show_barcode : false;
            document.getElementById('is_default').checked = label ? !!label.is_default : false;
            document.getElementById('is_active').checked = label ? !!label.is_active : true;
            modal.classList.add('flex');
            modal.classList.remove('hidden');
        }

        function closeLabelModal() {
            document.getElementById('label-modal')?.classList.remove('flex');
            document.getElementById('label-modal')?.classList.add('hidden');
        }

        async function loadLabelDropdowns() {
            const [freqRes, mealRes, dosageRes, timeRes, adviceRes] = await Promise.all([
                fetch(labelLookupUrls.frequencies),
                fetch(labelLookupUrls.mealRelations),
                fetch(labelLookupUrls.dosages),
                fetch(labelLookupUrls.times),
                fetch(labelLookupUrls.advices),
            ]);

            labelLookups.frequencies = await freqRes.json();
            labelLookups.mealRelations = await mealRes.json();
            labelLookups.dosages = await dosageRes.json();
            labelLookups.times = await timeRes.json();
            labelLookups.advices = await adviceRes.json();
        }

        // Helper: Get language value from label
        function getLangValue(label, field, lang = 'th') {
            if (lang === 'th') return label[field] || '';
            if (lang === 'mm') return label[`${field}_mm`] || label[field] || '';
            if (lang === 'zh') return label[`${field}_zh`] || label[field] || '';
            return label[field] || '';
        }

        // Helper: Format date in Thai Buddhist year
        function formatThaiDate(date) {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear() + 543; // Thai Buddhist year
            return `${day}/${month}/${year}`;
        }

        // Helper: Generate label preview HTML
        function generateLabelPreviewHTML(label, shopSettings, lang = 'th', isPrint = false, labelSettings = null) {
            const getLang = (obj, field) => {
                if (lang === 'mm') return obj[field + '_mm'] || obj[field] || '';
                if (lang === 'zh') return obj[field + '_zh'] || obj[field] || '';
                return obj[field] || '';
            };

            const productName = [label.name_for_print, label.trade_name, window.productName]
                .find(v => typeof v === 'string' && v.trim() !== '') || '';

            const previewLabel = {
                shop_name:     shopSettings?.shop_name    || '',
                shop_address:  shopSettings?.shop_address || '',
                shop_phone:    shopSettings?.shop_phone   || '',
                product_name:  productName,
                dosage:        getLang(label, 'dosage_name'),
                frequency:     getLang(label, 'frequency_name'),
                meal_relation: getLang(label, 'meal_relation_name'),
                label_time:    getLang(label, 'label_time_name'),
                indication:    lang === 'th' ? (label.indication_th || '')
                             : lang === 'mm' ? (label.indication_mm || '')
                             : (label.indication_zh || ''),
                advice:        getLang(label, 'advice_name'),
                show_barcode:  label.show_barcode && label.product_barcode,
                barcode:       label.product_barcode || '',
                barcode_id:    `barcode-${label.id}-${lang}`,
            };

            const previewSettings = {
                paper_width:    labelSettings?.paper_width    ?? 70,
                paper_height:   labelSettings?.paper_height   ?? 50,
                padding_top:    labelSettings?.padding_top    ?? 3,
                padding_right:  labelSettings?.padding_right  ?? 3,
                padding_bottom: labelSettings?.padding_bottom ?? 3,
                padding_left:   labelSettings?.padding_left   ?? 3,
                font_family:    labelSettings?.font_family    || 'Tahoma',
                line_spacing:   labelSettings?.line_spacing   ?? 1.4,
                row_styles:     labelSettings?.row_styles     || {},
            };

            const maxWidth = isPrint ? (previewSettings.paper_width * 3.78) : 380;
            const options  = { forPrint: isPrint };

            return generateLabelPreview(previewLabel, previewSettings, maxWidth, options);
        }

        // Function: Print label
        function printLabel(label, shopSettings, lang = 'th', labelSettings = null) {
            const pw = labelSettings?.paper_width ?? 70;
            const ph = labelSettings?.paper_height ?? 50;
            const content = generateLabelPreviewHTML(label, shopSettings, lang, true, labelSettings);

            const printWindow = window.open('', '', 'width=1000,height=600');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
                    <style>
                        @page {
                            size: ${pw}mm ${ph}mm;
                            margin: 0;
                        }
                        body {
                            margin: 0;
                            padding: 0;
                            font-family: '${labelSettings?.font_family || 'Tahoma'}', 'Google Sans', Arial, sans-serif;
                        }
                        .print-container {
                            width: ${pw}mm;
                            height: ${ph}mm;
                            overflow: hidden;
                        }
                    </style>
                </head>
                <body>
                    <div class="print-container">${content}</div>
                    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>
                    <script>
                        if ('${label.show_barcode}' === '1' && '${label.product_barcode}') {
                            JsBarcode('#barcode-${label.id}-${lang}', '${label.product_barcode}', { format: 'EAN13' });
                        }
                        setTimeout(() => window.print(), 500);
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }

        async function loadLabels() {
            const container = document.getElementById('labels-list-container');
            if (!container) return;
            container.innerHTML = '<div class="flex items-center justify-center h-32"><span class="text-gray-400">กำลังโหลด...</span></div>';
            await loadLabelDropdowns();
            fetch(labelApiBase, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(labels => renderLabelsTable(labels))
                .catch(() => {
                    container.innerHTML = '<div class="flex items-center justify-center h-32"><span class="text-red-400">โหลดล้มเหลว</span></div>';
                });
        }

        async function renderLabelsTable(labels) {
            const container = document.getElementById('labels-list-container');
            if (!container) return;
            if (!labels.length) {
                container.innerHTML = '<div class="flex items-center justify-center h-32"><span class="text-gray-400">ยังไม่มีฉลาก</span></div>';
                return;
            }


            // Fetch shop settings
            let shopSettings = {};
            try {
                const settingsRes = await fetch('/api/settings', { headers: { 'Accept': 'application/json' } });
                shopSettings = await settingsRes.json();
            } catch (e) {
                console.warn('Failed to load shop settings');
            }

            // Fetch label settings
            let labelSettings = null;
            try {
                const lsRes = await fetch('/api/label-settings', { headers: { 'Accept': 'application/json' } });
                labelSettings = (await lsRes.json()).data || null;
            } catch (e) {
                console.warn('Failed to load label settings');
            }

            let html = '';
            for (const label of labels) {
                html += `
                    <div class="label-card bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4 flex gap-4">
                        <div class="flex-shrink-0 w-[400px]">
                            <div class="border-2 border-gray-300 rounded bg-white p-3 label-preview-${label.id}" data-label-id="${label.id}">
                                ${generateLabelPreviewHTML(label, shopSettings, 'th', false, labelSettings)}
                            </div>
                            <div class="flex gap-2 mt-3 justify-center">
                                <button type="button" class="label-lang-btn px-3 py-1.5 rounded text-xs font-medium border language-th-${label.id}" data-lang="th" data-label-id="${label.id}" style="background: #10b981; color: white; border-color: #10b981;">ไทย</button>
                                <button type="button" class="label-lang-btn px-3 py-1.5 rounded text-xs font-medium border language-mm-${label.id}" data-lang="mm" data-label-id="${label.id}" style="background: white; color: #666; border-color: #ccc;">MM</button>
                                <button type="button" class="label-lang-btn px-3 py-1.5 rounded text-xs font-medium border language-zh-${label.id}" data-lang="zh" data-label-id="${label.id}" style="background: white; color: #666; border-color: #ccc;">ZH</button>
                            </div>
                        </div>
                        <div class="flex-1">
                            <form class="label-inline-form space-y-3" data-label-id="${label.id}">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <label for="label_name-${label.id}" class="block text-xs font-medium text-gray-600 mb-1">ชื่อฉลาก</label>
                                        <input type="text" id="label_name-${label.id}" name="label_name" required placeholder="ชื่อเรียกฉลาก" class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 text-sm focus:outline-none focus:border-emerald-400">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="dosage_id_search-${label.id}" class="block text-xs font-medium text-gray-600 mb-1">ปริมาณที่ใช้</label>
                                        <input type="hidden" name="dosage_id" id="dosage_id_hidden-${label.id}">
                                        <div class="relative">
                                            <input type="text" id="dosage_id_search-${label.id}" autocomplete="off" placeholder="ค้นหาปริมาณที่ใช้" class="w-full h-10 rounded-lg border border-gray-300 px-3 pr-10 text-sm focus:outline-none focus:border-emerald-400">
                                            <button type="button" id="clear-dosage_id-${label.id}" class="absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 rounded hover:bg-gray-100 text-gray-600 hidden">×</button>
                                            <div id="dosage_id_dropdown-${label.id}" class="hidden absolute z-30 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-56 overflow-auto"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="frequency_id_search-${label.id}" class="block text-xs font-medium text-gray-600 mb-1">ความถี่ที่ใช้</label>
                                        <input type="hidden" name="frequency_id" id="frequency_id_hidden-${label.id}">
                                        <div class="relative">
                                            <input type="text" id="frequency_id_search-${label.id}" autocomplete="off" placeholder="ค้นหาความถี่ที่" class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 pr-10 text-sm focus:outline-none focus:border-emerald-400">
                                            <button type="button" id="clear-frequency_id-${label.id}" class="absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 rounded hover:bg-gray-100 text-gray-600 hidden">×</button>
                                            <div id="frequency_id_dropdown-${label.id}" class="hidden absolute z-40 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-56 overflow-auto"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="meal_relation_id_search-${label.id}" class="block text-xs font-medium text-gray-600 mb-1">รูปแบบการทาน</label>
                                        <input type="hidden" name="meal_relation_id" id="meal_relation_id_hidden-${label.id}">
                                        <div class="relative">
                                            <input type="text" id="meal_relation_id_search-${label.id}" autocomplete="off" placeholder="ค้นหารูปแบบการทาน" class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 pr-10 text-sm focus:outline-none focus:border-emerald-400">
                                            <button type="button" id="clear-meal_relation_id-${label.id}" class="absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 rounded hover:bg-gray-100 text-gray-600 hidden">×</button>
                                            <div id="meal_relation_id_dropdown-${label.id}" class="hidden absolute z-40 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-56 overflow-auto"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="label_time_id_search-${label.id}" class="block text-xs font-medium text-gray-600 mb-1">เวลาทานยา</label>
                                        <input type="hidden" name="label_time_id" id="label_time_id_hidden-${label.id}">
                                        <div class="relative">
                                            <input type="text" id="label_time_id_search-${label.id}" autocomplete="off" placeholder="ค้นหาเวลาทานยา" class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 pr-10 text-sm focus:outline-none focus:border-emerald-400">
                                            <button type="button" id="clear-label_time_id-${label.id}" class="absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 rounded hover:bg-gray-100 text-gray-600 hidden">×</button>
                                            <div id="label_time_id_dropdown-${label.id}" class="hidden absolute z-40 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-56 overflow-auto"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Swap: Indication fields BEFORE advice field -->
                                <div>
                                    <label for="advice_id_search-${label.id}" class="block text-xs font-medium text-gray-600 mb-1">คำแนะนำ</label>
                                    <input type="hidden" name="advice_id" id="advice_id_hidden-${label.id}">
                                    <div class="relative">
                                        <input type="text" id="advice_id_search-${label.id}" autocomplete="off" placeholder="ค้นหาคำแนะนำ" class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3 pr-10 text-sm focus:outline-none focus:border-emerald-400">
                                        <button type="button" id="clear-advice_id-${label.id}" class="absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 rounded hover:bg-gray-100 text-gray-600 hidden">×</button>
                                        <div id="advice_id_dropdown-${label.id}" class="hidden absolute z-40 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-56 overflow-auto"></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label for="indication_th-${label.id}" class="block text-xs font-medium text-gray-600 mb-1">ข้อบ่งใช้ไทย</label>
                                        <textarea id="indication_th-${label.id}" name="indication_th" rows="2" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:border-emerald-400"></textarea>
                                    </div>
                                    <div>
                                        <label for="indication_mm-${label.id}" class="block text-xs font-medium text-gray-600 mb-1">ข้อบ่งใช้พม่า</label>
                                        <textarea id="indication_mm-${label.id}" name="indication_mm" rows="2" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:border-emerald-400"></textarea>
                                    </div>
                                    <div>
                                        <label for="indication_zh-${label.id}" class="block text-xs font-medium text-gray-600 mb-1">ข้อบ่งใช้จีน</label>
                                        <textarea id="indication_zh-${label.id}" name="indication_zh" rows="2" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:border-emerald-400"></textarea>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-5 mt-2">
                                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                        <input type="checkbox" id="show_barcode-${label.id}" name="show_barcode" value="1" class="sr-only peer">
                                        <span class="text-sm font-medium text-gray-600">แสดงบาร์โค้ด</span>
                                        <div class="relative w-14 h-7 rounded-full bg-gray-100 peer-checked:bg-emerald-500 transition-colors duration-300 ease-in-out after:content-[''] after:absolute after:top-1 after:left-1 after:h-5 after:w-7 after:rounded-full after:bg-white after:shadow-sm after:translate-x-0 peer-checked:after:translate-x-5 after:transition-transform after:duration-300 after:ease-in-out"></div>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                        <input type="checkbox" id="is_default-${label.id}" name="is_default" value="1" class="sr-only peer">
                                        <span class="text-sm font-medium text-gray-600">ตั้งเป็นฉลากเริ่มต้น</span>
                                        <div class="relative w-14 h-7 rounded-full bg-amber-500 peer-checked:bg-amber-500 transition-colors duration-300 ease-in-out after:content-[''] after:absolute after:top-1 after:left-1 after:h-5 after:w-7 after:rounded-full after:bg-white after:shadow-sm after:translate-x-0 peer-checked:after:translate-x-5 after:transition-transform after:duration-300 after:ease-in-out"></div>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                        <input type="checkbox" id="is_active-${label.id}" name="is_active" value="1" class="sr-only peer">
                                        <span class="text-sm font-medium text-gray-600">เปิดใช้งาน</span>
                                        <div class="relative w-14 h-7 rounded-full bg-gray-100 peer-checked:bg-emerald-500 transition-colors duration-300 ease-in-out after:content-[''] after:absolute after:top-1 after:left-1 after:h-5 after:w-7 after:rounded-full after:bg-white after:shadow-sm after:translate-x-0 peer-checked:after:translate-x-5 after:transition-transform after:duration-300 after:ease-in-out"></div>
                                    </label>
                                </div>
                                <div class="flex gap-2 pt-3 border-t border-gray-100 mt-2">
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium">💾 บันทึก</button>
                                    <button type="button" class="print-label-btn px-4 py-2 rounded-lg border border-purple-400 text-purple-600 hover:bg-purple-50 text-sm font-medium" data-id="${label.id}" data-lang="th">🖨 พิมพ์</button>
                                    <button type="button" class="delete-label-btn px-4 py-2 rounded-lg border border-red-400 text-red-600 hover:bg-red-50 text-sm font-medium" data-id="${label.id}">🗑 ลบ</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    `;
            }

            container.innerHTML = html;

            // Populate inline forms with existing label data
            labels.forEach(label => {
                const id = label.id;

                // label_name
                const nameEl = document.getElementById(`label_name-${id}`);
                if (nameEl) nameEl.value = label.label_name || '';

                // dosage_id
                const dosageHidden = document.getElementById(`dosage_id_hidden-${id}`);
                const dosageSearch = document.getElementById(`dosage_id_search-${id}`);
                const dosageClear  = document.getElementById(`clear-dosage_id-${id}`);
                if (dosageHidden) dosageHidden.value = label.dosage_id || '';
                if (dosageSearch) dosageSearch.value = label.dosage_name || '';
                if (dosageClear)  dosageClear.classList.toggle('hidden', !label.dosage_name);

                // frequency_id
                const freqHidden = document.getElementById(`frequency_id_hidden-${id}`);
                const freqSearch = document.getElementById(`frequency_id_search-${id}`);
                const freqClear  = document.getElementById(`clear-frequency_id-${id}`);
                if (freqHidden) freqHidden.value = label.frequency_id || '';
                if (freqSearch) freqSearch.value = label.frequency_name || '';
                if (freqClear)  freqClear.classList.toggle('hidden', !label.frequency_name);

                // meal_relation_id
                const mealHidden = document.getElementById(`meal_relation_id_hidden-${id}`);
                const mealSearch = document.getElementById(`meal_relation_id_search-${id}`);
                const mealClear  = document.getElementById(`clear-meal_relation_id-${id}`);
                if (mealHidden) mealHidden.value = label.meal_relation_id || '';
                if (mealSearch) mealSearch.value = label.meal_relation_name || '';
                if (mealClear)  mealClear.classList.toggle('hidden', !label.meal_relation_name);

                // label_time_id
                const timeHidden = document.getElementById(`label_time_id_hidden-${id}`);
                const timeSearch = document.getElementById(`label_time_id_search-${id}`);
                const timeClear  = document.getElementById(`clear-label_time_id-${id}`);
                if (timeHidden) timeHidden.value = label.label_time_id || '';
                if (timeSearch) timeSearch.value = label.label_time_name || '';
                if (timeClear)  timeClear.classList.toggle('hidden', !label.label_time_name);

                // advice_id
                const adviceHidden = document.getElementById(`advice_id_hidden-${id}`);
                const adviceSearch = document.getElementById(`advice_id_search-${id}`);
                const adviceClear  = document.getElementById(`clear-advice_id-${id}`);
                if (adviceHidden) adviceHidden.value = label.advice_id || '';
                if (adviceSearch) adviceSearch.value = label.advice_name || '';
                if (adviceClear)  adviceClear.classList.toggle('hidden', !label.advice_name);

                // indications
                const indThEl = document.getElementById(`indication_th-${id}`);
                const indMmEl = document.getElementById(`indication_mm-${id}`);
                const indZhEl = document.getElementById(`indication_zh-${id}`);
                if (indThEl) indThEl.value = label.indication_th || '';
                if (indMmEl) indMmEl.value = label.indication_mm || '';
                if (indZhEl) indZhEl.value = label.indication_zh || '';

                // checkboxes
                const showBarcodeEl = document.getElementById(`show_barcode-${id}`);
                const isDefaultEl   = document.getElementById(`is_default-${id}`);
                const isActiveEl    = document.getElementById(`is_active-${id}`);
                if (showBarcodeEl) showBarcodeEl.checked = !!label.show_barcode;
                if (isDefaultEl)   isDefaultEl.checked   = !!label.is_default;
                if (isActiveEl)    isActiveEl.checked     = !!label.is_active;
            });

            // Render barcodes if JsBarcode is loaded
            setTimeout(() => {
                if (typeof JsBarcode !== 'undefined') {
                    labels.forEach(label => {
                        if (label.show_barcode && label.product_barcode) {
                            try {
                                const barcodeId = `barcode-${label.id}-th`;
                                const barcodeEl = document.getElementById(barcodeId);
                                if (barcodeEl) {
                                    JsBarcode(`#${barcodeId}`, label.product_barcode, { format: 'EAN13' });
                                }
                            } catch (e) {
                                console.warn('Failed to render barcode for label', label.id);
                            }
                        }
                    });
                }
            }, 100);

            // Language switcher
            container.querySelectorAll('.label-lang-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const labelId = this.dataset.labelId;
                    const lang = this.dataset.lang;
                    const label = labels.find(l => l.id == labelId);
                    if (!label) return;

                    // Update preview
                    const preview = document.querySelector(`.label-preview-${labelId}`);
                    if (preview) {
                        preview.innerHTML = generateLabelPreviewHTML(label, shopSettings, lang, false, labelSettings);
                        // Re-render barcode if needed
                        if (label.show_barcode && label.product_barcode && typeof JsBarcode !== 'undefined') {
                            setTimeout(() => {
                                try {
                                    const barcodeId = `barcode-${labelId}-${lang}`;
                                    const barcodeEl = document.getElementById(barcodeId);
                                    if (barcodeEl) {
                                        JsBarcode(`#${barcodeId}`, label.product_barcode, { format: 'EAN13' });
                                    }
                                } catch (e) {}
                            }, 50);
                        }
                    }

                    // Update button styles
                    document.querySelectorAll(`.label-lang-btn[data-label-id="${labelId}"]`).forEach(b => {
                        if (b.dataset.lang === lang) {
                            b.style.background = '#10b981';
                            b.style.color = 'white';
                            b.style.borderColor = '#10b981';
                        } else {
                            b.style.background = 'white';
                            b.style.color = '#666';
                            b.style.borderColor = '#ccc';
                        }
                    });

                    // Update print button language
                    document.querySelector(`.print-label-btn[data-id="${labelId}"]`).dataset.lang = lang;
                });
            });

            // Populate inline forms with label data
            labels.forEach(label => {
                const form = container.querySelector(`.label-inline-form[data-label-id="${label.id}"]`);
                if (!form) return;
                form.querySelector(`[name="label_name"]`).value = label.label_name || '';
                form.querySelector(`[name="dosage_id"]`).value = label.dosage_id || '';
                form.querySelector(`[id^="dosage_id_search-"]`).value = label.dosage_name || '';
                form.querySelector(`[name="frequency_id"]`).value = label.frequency_id || '';
                form.querySelector(`[id^="frequency_id_search-"]`).value = label.frequency_name || '';
                form.querySelector(`[name="meal_relation_id"]`).value = label.meal_relation_id || '';
                form.querySelector(`[id^="meal_relation_id_search-"]`).value = label.meal_relation_name || '';
                form.querySelector(`[name="label_time_id"]`).value = label.label_time_id || '';
                form.querySelector(`[id^="label_time_id_search-"]`).value = label.label_time_name || '';
                form.querySelector(`[name="advice_id"]`).value = label.advice_id || '';
                form.querySelector(`[id^="advice_id_search-"]`).value = label.advice_name || '';
                form.querySelector(`[name="indication_th"]`).value = label.indication_th || '';
                form.querySelector(`[name="indication_mm"]`).value = label.indication_mm || '';
                form.querySelector(`[name="indication_zh"]`).value = label.indication_zh || '';
                form.querySelector(`[name="show_barcode"]`).checked = !!label.show_barcode;
                form.querySelector(`[name="is_default"]`).checked = !!label.is_default;
                form.querySelector(`[name="is_active"]`).checked = !!label.is_active;
            });


            // --- Silent Save Helper ---
            async function silentSaveLabel(labelId, formEl) {
                const formData = new FormData(formEl);
                let url = labelApiBase;
                let method = 'POST';
                if (labelId !== 'new') {
                    url = `${labelApiBase}/${labelId}`;
                    formData.append('_method', 'PUT');
                }
                // Checkbox values
                formData.set('show_barcode', formEl.querySelector('[name="show_barcode"]')?.checked ? '1' : '0');
                formData.set('is_default', formEl.querySelector('[name="is_default"]')?.checked ? '1' : '0');
                formData.set('is_active', formEl.querySelector('[name="is_active"]')?.checked ? '1' : '0');
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: formData,
                    });
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(data.message || 'บันทึกไม่สำเร็จ');
                    showToast(data.message || 'บันทึกสำเร็จ', 'success');
                    // For new label, reload all
                    if (labelId === 'new') {
                        loadLabels();
                    } else {
                        // Update preview in-place
                        const updatedLabel = data.label || null;
                        if (updatedLabel) {
                            // Update preview HTML
                            const preview = document.querySelector(`.label-preview-${labelId}`);
                            if (preview) {
                                preview.innerHTML = generateLabelPreviewHTML(updatedLabel, shopSettings, 'th', false, labelSettings);
                                // Re-render barcode if needed
                                if (updatedLabel.show_barcode && updatedLabel.product_barcode && typeof JsBarcode !== 'undefined') {
                                    setTimeout(() => {
                                        try {
                                            const barcodeId = `barcode-${labelId}-th`;
                                            const barcodeEl = document.getElementById(barcodeId);
                                            if (barcodeEl) {
                                                JsBarcode(`#${barcodeId}`, updatedLabel.product_barcode, { format: 'EAN13' });
                                            }
                                        } catch (e) {}
                                    }, 50);
                                }
                            }
                        }
                    }
                } catch (error) {
                    showToast(error.message, 'error');
                }
            }

            // Bind submit for each inline form (silent save)
            container.querySelectorAll('.label-inline-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const labelId = form.dataset.labelId;
                    silentSaveLabel(labelId, form);
                });
            });

            // --- Bind toggle auto-save for checkboxes ---
            container.querySelectorAll('.label-inline-form').forEach(form => {
                const labelId = form.dataset.labelId;
                ['show_barcode', 'is_default', 'is_active'].forEach(field => {
                    const checkbox = form.querySelector(`[name="${field}"]`);
                    if (checkbox) {
                        checkbox.addEventListener('change', function() {
                            silentSaveLabel(labelId, form);
                        });
                    }
                });
            });

            // Initialize autocomplete for all forms
            initializeAllLabelAutocompletes();

            // Print button
            container.querySelectorAll('.print-label-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const label = labels.find(l => l.id == btn.dataset.id);
                    const lang = btn.dataset.lang || 'th';
                    if (label) printLabel(label, shopSettings, lang, labelSettings);
                });
            });

            // Delete button
            container.querySelectorAll('.delete-label-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (confirm('ลบฉลากนี้?')) {
                        fetch(`/products/labels/${btn.dataset.id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        }).then(() => loadLabels()).catch(() => showToast('ลบล้มเหลว', 'error'));
                    }
                });
            });
        }

        initializeLabelAutocomplete();
        document.addEventListener('click', function(e) {
            const target = e.target;
            if (!(target instanceof Element)) return;

            labelSearchConfigs.forEach(config => {
                const field = config.field;
                if (target.closest(`#${field}_search`) || target.closest(`#${field}_dropdown`) || target.closest(`#clear-${field}`)) {
                    return;
                }
                document.getElementById(`${field}_dropdown`)?.classList.add('hidden');
            });
        });
        // --- End Label Tab ---

        const tabButtons = document.querySelectorAll('.tab-button');
        const tabPanels = document.querySelectorAll('.tab-panel');
        const historySubTabButtons = document.querySelectorAll('.history-subtab-button');
        const historySubTabPanels = document.querySelectorAll('.history-subtab-panel');
        const priceRetailInput = document.getElementById('price_retail');
        const priceWholesale1Input = document.getElementById('price_wholesale1_main');
        const priceWholesale2Input = document.getElementById('price_wholesale2_main');
        const latestCostElement = document.querySelector('[data-latest-cost]');
        const hasWholesale1Input = document.getElementById('has_wholesale1');
        const hasWholesale2Input = document.getElementById('has_wholesale2');
        const toggleWholesale1 = document.getElementById('toggle-has-wholesale1');
        const toggleWholesale2 = document.getElementById('toggle-has-wholesale2');
        const wholesale1Section = document.getElementById('wholesale1-section');
        const wholesale2Section = document.getElementById('wholesale2-section');
        const warningRetail = document.getElementById('warning-retail');
        const warningWh1 = document.getElementById('warning-wh1');
        const warningWh2 = document.getElementById('warning-wh2');
        const isFdaReportCheckbox = document.getElementById('is_fda_report');
        const drugLawSection = document.getElementById('drug_law_section');
        const genericNameSearchInput = document.getElementById('generic-name-search');
        const genericNameIdInput = document.getElementById('drug_generic_name_id');
        const genericNameDropdown = document.getElementById('generic-name-dropdown');
        const clearGenericNameBtn = document.getElementById('clear-generic-name');
        const genericSearchUrl = "{{ url('/products/search-generic-name') }}";
        const isSaleControlCheckbox = document.getElementById('is_sale_control');
        const saleControlQtyInput = document.querySelector('input[name="sale_control_qty"]');

        function markDirty(e) {
            if (!e || !e.target) return;
            console.log('markDirty:', e.target.id, e.target.closest('#label-modal'));
            if (e.target.closest('#unit-modal-form')) return;
            if (e.target.closest('#label-modal')) return; // ← เพิ่มบรรทัดนี้
            if (e.target.closest('#labels-list-container')) return;
            isDirty = true;
        }

        if (mainForm) {
            mainForm.addEventListener('input', markDirty);
            mainForm.addEventListener('change', markDirty);
            mainForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(e.target);
                formData.set('_method', 'PUT');

                try {
                    const response = await fetch(e.target.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!data.success && data.duplicate_fields) {
                        // reset ทุก barcode input ก่อน
                        ['barcode','barcode2','barcode3','barcode4'].forEach(name => {
                            const input = document.querySelector(`input[name="${name}"]`);
                            if (input) {
                                input.classList.remove('border-red-400');
                                input.classList.add('border-gray-300');
                            }
                        });
                        // highlight เฉพาะที่ซ้ำ
                        data.duplicate_fields.forEach(name => {
                            const input = document.querySelector(`input[name="${name}"]`);
                            if (input) {
                                input.classList.remove('border-gray-300');
                                input.classList.add('border-red-400');
                            }
                        });
                        showToast(data.message, 'error');
                        return;
                    }

                    if (data.success) {
                        showToast(data.message || 'บันทึกสำเร็จ', 'success');
                        isDirty = false;
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
                    }
                } catch (error) {
                    showToast(error.message || 'เกิดข้อผิดพลาด', 'error');
                }
            });
        }

        // Tab switching
        tabButtons.forEach(button => {
            button.addEventListener('click', async function(e) {
                e.preventDefault();
                const targetTab = this.getAttribute('data-tab');

                if (isDirty && mainForm && targetTab !== 'tab-labels') {
                    try {
                        await triggerAutoSave('บันทึกแล้ว');
                    } catch (error) {
                        notify('บันทึกไม่สำเร็จ กรุณาบันทึกด้วยตนเอง', 'error');
                    }
                }

                activateTab(this, targetTab);
                window.location.hash = targetTab;
            });
        });

        const initialTab = window.location.hash.replace('#', '');
        if (initialTab) {
            const matchedButton = document.querySelector(`.tab-button[data-tab="${initialTab}"]`);
            if (matchedButton) {
                activateTab(matchedButton, initialTab);
                if (initialTab === 'tab-labels') {
                    loadLabels();
                }
            }
        }

        function activateHistorySubTab(button, targetTab) {
            historySubTabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-emerald-200', 'bg-emerald-50', 'text-emerald-700');
                btn.classList.add('border-gray-200', 'bg-white', 'text-gray-600');
            });

            historySubTabPanels.forEach(panel => {
                panel.classList.add('hidden');
            });

            button.classList.add('active', 'border-emerald-200', 'bg-emerald-50', 'text-emerald-700');
            button.classList.remove('border-gray-200', 'bg-white', 'text-gray-600');
            document.getElementById(targetTab)?.classList.remove('hidden');
        }

        historySubTabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-history-tab');
                if (!targetTab) return;
                activateHistorySubTab(this, targetTab);
            });
        });

        const defaultHistoryTab = document.querySelector('.history-subtab-button[data-history-tab="history-sales"]');
        if (defaultHistoryTab) {
            activateHistorySubTab(defaultHistoryTab, 'history-sales');
        }

        // Sale control checkbox logic
        if (isSaleControlCheckbox && saleControlQtyInput) {
            isSaleControlCheckbox.addEventListener('change', function() {
                // Toggle visibility or styling if needed
                // For now, the field is always available, just checkbox toggles the flag
            });
        }

        function toggleDrugLawSection(show) {
            if (!drugLawSection) return;
            drugLawSection.classList.remove('max-h-0', 'max-h-[520px]', 'opacity-0', 'opacity-100');
            if (show) {
                drugLawSection.classList.add('max-h-[520px]', 'opacity-100');
            } else {
                drugLawSection.classList.add('max-h-0', 'opacity-0');
            }
        }

        isFdaReportCheckbox?.addEventListener('change', function() {
            toggleDrugLawSection(this.checked);
            isDirty = true;
        });

        toggleDrugLawSection(!!isFdaReportCheckbox?.checked);

        function renderGenericSuggestions(items) {
            if (!genericNameDropdown) return;
            if (!items.length) {
                genericNameDropdown.innerHTML = '<div class="px-3 py-2 text-sm text-gray-400">ไม่พบข้อมูล</div>';
                genericNameDropdown.classList.remove('hidden');
                return;
            }

            genericNameDropdown.innerHTML = items
                .map((item) => `<button type="button" class="generic-option w-full text-left px-3 py-2 text-sm hover:bg-gray-50" data-id="${item.id}" data-name="${String(item.name).replace(/"/g, '&quot;')}">${item.name}</button>`)
                .join('');
            genericNameDropdown.classList.remove('hidden');

            genericNameDropdown.querySelectorAll('.generic-option').forEach((btn) => {
                btn.addEventListener('click', function() {
                    if (genericNameIdInput) genericNameIdInput.value = this.dataset.id || '';
                    if (genericNameSearchInput) genericNameSearchInput.value = this.dataset.name || '';
                    clearGenericNameBtn?.classList.remove('hidden');
                    genericNameDropdown.classList.add('hidden');
                    isDirty = true;
                });
            });
        }

        let genericSearchTimer = null;
        genericNameSearchInput?.addEventListener('input', function() {
            const query = (this.value || '').trim();
            if (genericNameIdInput) {
                genericNameIdInput.value = '';
            }
            clearGenericNameBtn?.classList.toggle('hidden', query.length === 0);
            isDirty = true;

            if (genericSearchTimer) {
                clearTimeout(genericSearchTimer);
            }

            if (query.length < 2) {
                genericNameDropdown?.classList.add('hidden');
                return;
            }

            genericSearchTimer = setTimeout(async () => {
                try {
                    const response = await fetch(`${genericSearchUrl}?q=${encodeURIComponent(query)}`, {
                        headers: { 'Accept': 'application/json' },
                    });
                    const data = await response.json().catch(() => []);
                    renderGenericSuggestions(Array.isArray(data) ? data : []);
                } catch (error) {
                    genericNameDropdown?.classList.add('hidden');
                }
            }, 250);
        });

        clearGenericNameBtn?.addEventListener('click', function() {
            if (genericNameSearchInput) genericNameSearchInput.value = '';
            if (genericNameIdInput) genericNameIdInput.value = '';
            genericNameDropdown?.classList.add('hidden');
            clearGenericNameBtn.classList.add('hidden');
            isDirty = true;
        });

        document.addEventListener('click', function(e) {
            if (!genericNameDropdown || !genericNameSearchInput) return;
            const target = e.target;
            if (!(target instanceof Element)) return;
            if (target.closest('#generic-name-search') || target.closest('#generic-name-dropdown')) return;
            genericNameDropdown.classList.add('hidden');
        });

        if ((genericNameSearchInput?.value || '').trim().length > 0) {
            clearGenericNameBtn?.classList.remove('hidden');
        }

        // Profit calculation function
        function setSectionVisible(sectionEl, show) {
            if (!sectionEl) return;
            sectionEl.classList.remove('max-h-0', 'max-h-40', 'opacity-0', 'opacity-100');
            if (show) {
                sectionEl.classList.add('max-h-40', 'opacity-100');
            } else {
                sectionEl.classList.add('max-h-0', 'opacity-0');
            }
        }

        function setWarning(el, show, message) {
            if (!el) return;
            el.textContent = message || '';
            el.classList.toggle('hidden', !show);
        }

        function parsePrice(inputEl) {
            return parseFloat(inputEl?.value) || 0;
        }

        function updateProfitCalculation() {
            const latestCost = parseFloat(latestCostElement?.getAttribute('data-latest-cost')) || 0;
            const hasWh1 = (hasWholesale1Input?.value || '0') === '1';
            const hasWh2 = (hasWholesale2Input?.value || '0') === '1';
            const retailPrice = parsePrice(priceRetailInput);
            const wh1Price = hasWh1 ? parsePrice(priceWholesale1Input) : 0;
            const wh2Price = hasWh2 ? parsePrice(priceWholesale2Input) : 0;

            const calculate = (price) => {
                const safePrice = parseFloat(price) || 0;
                const profitPerUnit = safePrice - latestCost;
                const profitVsCost = latestCost > 0 ? (profitPerUnit / latestCost) * 100 : 0;
                const profitVsSale = safePrice > 0 ? (profitPerUnit / safePrice) * 100 : 0;
                return {
                    profitPerUnit,
                    profitVsCost,
                    profitVsSale,
                };
            };

            const retail = calculate(retailPrice);
            const wh1 = calculate(wh1Price);
            const wh2 = calculate(wh2Price);

            const setText = (id, value) => {
                const el = document.getElementById(id);
                if (!el) return;
                el.textContent = value.toFixed(2);
                el.classList.remove('text-red-600', 'text-green-600', 'text-gray-700');
                if (value > 0) {
                    el.classList.add('text-green-600');
                } else if (value < 0) {
                    el.classList.add('text-red-600');
                } else {
                    el.classList.add('text-gray-700');
                }
            };

            setText('profit-retail-per-unit', retail.profitPerUnit);
            setText('profit-retail-vs-cost', retail.profitVsCost);
            setText('profit-retail-vs-sale', retail.profitVsSale);

            setText('profit-wh1-per-unit', wh1.profitPerUnit);
            setText('profit-wh1-vs-cost', wh1.profitVsCost);
            setText('profit-wh1-vs-sale', wh1.profitVsSale);

            setText('profit-wh2-per-unit', wh2.profitPerUnit);
            setText('profit-wh2-vs-cost', wh2.profitVsCost);
            setText('profit-wh2-vs-sale', wh2.profitVsSale);

            setWarning(warningWh1, hasWh1 && wh1Price > retailPrice, '⚠ ราคาส่งไม่ควรแพงกว่าปลีก');
            setWarning(warningWh2, hasWh2 && wh2Price > retailPrice, '⚠ ราคาส่งไม่ควรแพงกว่าปลีก');
            setWarning(
                warningRetail,
                (hasWh1 && retailPrice < wh1Price) || (hasWh2 && retailPrice < wh2Price),
                '⚠ ราคาปลีกต่ำกว่าราคาส่ง'
            );
        }

        function syncWholesaleToggle(level, isEnabled, markAsDirty = true) {
            if (level === 1) {
                if (hasWholesale1Input) hasWholesale1Input.value = isEnabled ? '1' : '0';
                setSectionVisible(wholesale1Section, isEnabled);
                if (!isEnabled && priceWholesale1Input) {
                    priceWholesale1Input.value = '0';
                }
            }

            if (level === 2) {
                if (hasWholesale2Input) hasWholesale2Input.value = isEnabled ? '1' : '0';
                setSectionVisible(wholesale2Section, isEnabled);
                if (!isEnabled && priceWholesale2Input) {
                    priceWholesale2Input.value = '0';
                }
            }

            if (markAsDirty) {
                isDirty = true;
            }
            updateProfitCalculation();
        }

        // Listen to price inputs and recalculate in real-time
        if (priceRetailInput) {
            priceRetailInput.addEventListener('input', updateProfitCalculation);
        }
        if (priceWholesale1Input) {
            priceWholesale1Input.addEventListener('input', updateProfitCalculation);
            priceWholesale1Input.addEventListener('blur', function() {
                if (this.value === '' || isNaN(parseFloat(this.value))) this.value = '0';
            });
        }
        if (priceWholesale2Input) {
            priceWholesale2Input.addEventListener('input', updateProfitCalculation);
            priceWholesale2Input.addEventListener('blur', function() {
                if (this.value === '' || isNaN(parseFloat(this.value))) this.value = '0';
            });
        }

        toggleWholesale1?.addEventListener('change', function() {
            syncWholesaleToggle(1, this.checked, true);
        });

        toggleWholesale2?.addEventListener('change', function() {
            syncWholesaleToggle(2, this.checked, true);
        });

        // Apply initial toggle state
        syncWholesaleToggle(1, (hasWholesale1Input?.value || '0') === '1', false);
        syncWholesaleToggle(2, (hasWholesale2Input?.value || '0') === '1', false);

        // Initial calculation on page load
        updateProfitCalculation();
        updateAdjustStockPreview();
    });
</script>

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

@endsection
