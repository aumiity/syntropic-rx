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

        <!-- Tab Bar -->
        <div class="mb-6 flex flex-wrap gap-2 border-b border-gray-200">
            <button type="button" data-tab="tab-1" class="tab-button active min-h-11 px-4 py-2.5 text-sm font-medium text-emerald-600 border-b-2 border-emerald-600 rounded-t-lg">ข้อมูลหลัก</button>
            <button type="button" data-tab="tab-2" class="tab-button min-h-11 px-4 py-2.5 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800 rounded-t-lg">หน่วยสินค้า</button>
            <button type="button" data-tab="tab-3" class="tab-button min-h-11 px-4 py-2.5 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800 rounded-t-lg">ยาและประเภทยา</button>
            <button type="button" data-tab="tab-4" class="tab-button min-h-11 px-4 py-2.5 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800 rounded-t-lg">ฉลาก</button>
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
                                    onclick="document.getElementById('modal-edit-unit').classList.remove('hidden')"
                                >
                                    แก้ไขหน่วยขาย
                                </button>
                            </div>
                            <input id="base-unit-hidden" type="hidden" name="base_unit_name" value="{{ old('base_unit_name', $baseUnitName) }}">
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                            ราคาทุนล่าสุด: ฿{{ number_format($latestCost, 4) }} | ทุนเฉลี่ย: ฿<span data-avg="{{ $avgCost }}">{{ number_format($avgCost, 4) }}</span>
                        </div>

                        <div>
                            <label class="block text-base font-semibold text-gray-700 mb-1">ราคาขายปลีก</label>
                            <input type="number" name="price_retail" id="price_retail" value="{{ old('price_retail', $product->price_retail) }}" step="0.01" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400" data-required="true" data-error-msg="กรุณากรอกราคาขายปลีก">
                            <p id="retail-unit-text" class="mt-1 text-xs text-gray-500">ต่อ 1 ({{ $baseUnitName }})</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">ราคาส่ง ระดับ 1</label>
                                <input type="number" name="price_wholesale1" value="{{ old('price_wholesale1', $product->price_wholesale1) }}" step="0.01" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">ราคาส่ง ระดับ 2</label>
                                <input type="number" name="price_wholesale2" value="{{ old('price_wholesale2', $product->price_wholesale2) }}" step="0.01" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                            </div>
                        </div>

                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs font-medium text-amber-600 mb-1">กำไรต่อหน่วย</p>
                                <p class="text-sm font-semibold text-amber-800">฿<span id="profit-per-unit">0.00</span></p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-amber-600 mb-1">เทียบทุน</p>
                                <p class="text-sm font-semibold text-amber-800"><span id="profit-vs-cost">0.00</span>%</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-amber-600 mb-1">เทียบขาย</p>
                                <p class="text-sm font-semibold text-amber-800"><span id="profit-vs-sale">0.00</span>%</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">บาร์โค้ด 1</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">บาร์โค้ด 2</label>
                            <input type="text" name="barcode2" value="{{ old('barcode2', $product->barcode2) }}" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">ชื่อที่ตั้งเอง (คำค้น)</label>
                            <input type="text" name="search_keywords" value="{{ old('search_keywords', $product->search_keywords) }}" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
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

                        <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                            <p class="text-sm font-medium text-gray-700">
                                จำนวนคงเหลือ: {{ $product->lots?->sum('qty_on_hand') ?? 0 }} {{ $baseUnitName }}
                            </p>
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
                                    <td colspan="5" class="text-center text-gray-400 py-6">ยังไม่มีหน่วยสินค้า</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 3: ประเภทยาตามกฎหมาย -->
            <div id="tab-3" class="tab-panel hidden bg-white border border-gray-200 rounded-xl p-5">

                <!-- FDA Report Checkbox -->
                <div class="mb-4">
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer font-medium">
                        <input type="checkbox" name="is_fda_report" id="is_fda_report" value="1"
                            {{ old('is_fda_report', $product->is_fda_report) ? 'checked' : '' }}
                            class="w-4 h-4 rounded">
                        สินค้านี้เป็นยาตามกฎหมาย (รายงาน ขย.9)
                    </label>
                </div>

                <!-- Drug Law Section (collapsible) -->
                <div id="drug_law_section" class="hidden border border-gray-200 rounded-lg p-4 bg-gray-50">

                    <!-- ประเภทยาตามกฎหมาย -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">ประเภทยาตามกฎหมาย</label>
                        <select name="drug_type_id" id="drug_type_id" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                            <option value="">-- เลือกประเภท --</option>
                            @foreach($drugTypes->where('is_disabled', false)->sortBy('name_th') as $type)
                                <option value="{{ $type->id }}" {{ old('drug_type_id', $product->drug_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name_th }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Report Checkboxes -->
                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" name="is_fda11_report" value="1"
                                {{ old('is_fda11_report', $product->is_fda11_report) ? 'checked' : '' }}
                                class="w-4 h-4 rounded">
                            ต้องรายงาน ขย.11
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" name="is_fda13_report" value="1"
                                {{ old('is_fda13_report', $product->is_fda13_report) ? 'checked' : '' }}
                                class="w-4 h-4 rounded">
                            ต้องรายงาน ขย.13
                        </label>
                    </div>
                </div>

                <script>
                    (function () {
                        const cb = document.getElementById('is_fda_report');
                        const section = document.getElementById('drug_law_section');
                        const drugTypeSelect = document.getElementById('drug_type_id');

                        function toggle() {
                            if (cb.checked) {
                                section.classList.remove('hidden');
                            } else {
                                section.classList.add('hidden');
                                drugTypeSelect.value = '';
                            }
                        }

                        // Run on page load
                        toggle();

                        cb.addEventListener('change', toggle);
                    })();
                </script>

            </div>

            <!-- Tab 4: ฉลาก -->
            <div id="tab-4" class="tab-panel hidden bg-white border border-gray-200 rounded-xl p-5">
                <div class="flex items-center justify-center h-40">
                    <p class="text-gray-400 text-center">🚧 อยู่ระหว่างพัฒนา</p>
                </div>
            </div>

            <!-- Tab 5: สต๊อค -->
            <div id="tab-5" class="tab-panel hidden bg-white border border-gray-200 rounded-xl p-5">
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-600 mb-4 pb-2 border-b border-gray-100">รายละเอียดล็อต</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="px-3 py-2 text-left font-medium text-gray-600 text-xs">ล็อตที่</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600 text-xs">Lot Number</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600 text-xs">วันหมดอายุ</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-600 text-xs">ราคาทุน</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-600 text-xs">ราคาขาย</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-600 text-xs">รับเข้า</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-600 text-xs">คงเหลือ</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600 text-xs">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $now = now();
                                    $totalOnHand = 0;
                                    $sortedLots = $product->lots->sortBy('expiry_date')->values();
                                @endphp
                                @forelse($sortedLots as $idx => $lot)
                                    @php
                                        $totalOnHand += $lot->qty_on_hand;
                                        $daysToExpiry = $now->diffInDays($lot->expiry_date, false);
                                        $rowClass = $daysToExpiry < 30 ? 'bg-red-50' : ($daysToExpiry < 90 ? 'bg-yellow-50' : '');
                                        $statusBadge = $daysToExpiry < 0 ? 'text-red-600 font-semibold' : ($daysToExpiry < 30 ? 'text-red-600' : ($daysToExpiry < 90 ? 'text-yellow-600' : 'text-green-600'));
                                    @endphp
                                    <tr class="border-b border-gray-100 {{ $rowClass }}">
                                        <td class="px-3 py-2 text-gray-800">{{ $idx + 1 }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $lot->lot_number }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $lot->expiry_date->format('d/m/Y') }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700">฿{{ number_format($lot->cost_price, 2) }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700">฿{{ number_format($lot->sell_price ?? 0, 2) }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700">{{ $lot->qty_received }}</td>
                                        <td class="px-3 py-2 text-right font-medium text-gray-800">{{ $lot->qty_on_hand }}</td>
                                        <td class="px-3 py-2 text-xs {{ $statusBadge }}">
                                            @if($daysToExpiry < 0)
                                                ❌ หมดอายุแล้ว
                                            @elseif($daysToExpiry < 30)
                                                🔴 ใกล้หมดอายุ
                                            @elseif($daysToExpiry < 90)
                                                🟡 เตือน
                                            @else
                                                ✅ ปกติ
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="border-b border-gray-100">
                                        <td colspan="8" class="px-3 py-4 text-center text-gray-400 text-sm">ไม่มีข้อมูลล็อต</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50 font-semibold border-t border-gray-200">
                                    <td colspan="6" class="px-3 py-2 text-right text-gray-700">รวมทั้งหมด:</td>
                                    <td class="px-3 py-2 text-right text-gray-800">{{ $totalOnHand }}</td>
                                    <td class="px-3 py-2"></td>
                                </tr>
                            </tfoot>
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
                <div class="flex items-center justify-center h-40">
                    <p class="text-center text-gray-400 italic">ฟีเจอร์ประวัติการใช้งานอยู่ระหว่างพัฒนา จะเปิดใช้งานในเวอร์ชันถัดไป</p>
                </div>
            </div>

            {{-- Submit Button Row (always visible) --}}
            <div class="flex justify-end gap-3 pb-6 pt-4">
                <a href="{{ route('products.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50">ยกเลิก</a>
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

    <div id="unit-modal" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center p-4">
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
                        <input type="number" id="qty_per_base" name="qty_per_base" step="0.0001" min="0.0001" required class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Barcode</label>
                        <input type="text" id="barcode" name="barcode" class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">ราคาปลีก</label>
                        <input type="number" id="price_retail_unit" name="price_retail" step="0.01" min="0" class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">ราคาส่ง 1</label>
                        <input type="number" id="price_wholesale1" name="price_wholesale1" step="0.01" min="0" class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
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

        const storeUnitUrl = "{{ route('product_units.store', $product) }}";
        const autoSaveUrl = "{{ route('products.autosave', $product) }}";
        const unitBaseUrl = "{{ url('/products/units') }}";
        const baseUnitModal = document.getElementById('modal-edit-unit');
        const baseUnitInput = document.getElementById('modal-unit-input');
        const baseUnitHidden = document.getElementById('base-unit-hidden');
        const baseUnitDisplay = document.getElementById('base-unit-display');
        const retailUnitText = document.getElementById('retail-unit-text');
        let modalMode = 'create';
        let isDirty = false;

        window.closeBaseUnitModal = function() {
            baseUnitModal?.classList.add('hidden');
            baseUnitModal?.classList.remove('flex');
        };

        window.confirmBaseUnitEdit = function() {
            const unitName = (baseUnitInput?.value || '').trim();
            if (!unitName) {
                notify('กรุณาระบุหน่วยขาย', 'error');
                return;
            }

            if (baseUnitHidden) baseUnitHidden.value = unitName;
            if (baseUnitDisplay) baseUnitDisplay.textContent = unitName;
            if (retailUnitText) retailUnitText.textContent = `ต่อ 1 (${unitName})`;
            isDirty = true;
            window.closeBaseUnitModal();
        };

        if (baseUnitModal) {
            baseUnitModal.addEventListener('click', function(e) {
                if (e.target === baseUnitModal) {
                    window.closeBaseUnitModal();
                }
            });
        }

        function notify(message, type = 'info') {
            if (typeof showToast === 'function') {
                showToast(message, type);
            } else {
                console.log(`[${type}] ${message}`);
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
            unitModal.classList.remove('hidden');
        }

        function closeModal() {
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
                price_wholesale1: document.getElementById('price_wholesale1').value,
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

        async function submitUnitForm(e) {
            e.preventDefault();
            if (!unitModalForm.reportValidity()) return;

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
                document.getElementById('price_wholesale1').value = unit.price_wholesale1 ?? '';
                document.getElementById('is_for_sale').checked = !!unit.is_for_sale;
                document.getElementById('is_for_purchase').checked = !!unit.is_for_purchase;
            } else {
                modalMode = 'create';
                unitModalTitle.textContent = 'เพิ่มหน่วยสินค้า';
            }

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

        addUnitBtn?.addEventListener('click', () => window.openUnitModal());
        closeUnitBtn?.addEventListener('click', closeModal);
        cancelUnitBtn?.addEventListener('click', closeModal);
        unitModal?.addEventListener('click', (e) => {
            if (e.target === unitModal) closeModal();
        });
        unitModalForm?.addEventListener('submit', submitUnitForm);

        const tabButtons = document.querySelectorAll('.tab-button');
        const tabPanels = document.querySelectorAll('.tab-panel');
        const priceRetailInput = document.getElementById('price_retail');
        const avgCostElement = document.querySelector('[data-avg]');
        const profitPerUnitSpan = document.getElementById('profit-per-unit');
        const profitVsCostSpan = document.getElementById('profit-vs-cost');
        const isSaleControlCheckbox = document.getElementById('is_sale_control');
        const saleControlQtyInput = document.querySelector('input[name="sale_control_qty"]');

        function markDirty(e) {
            if (!e || !e.target) return;
            if (e.target.closest('#unit-modal-form')) return;
            isDirty = true;
        }

        if (mainForm) {
            mainForm.addEventListener('input', markDirty);
            mainForm.addEventListener('change', markDirty);
        }

        // Tab switching
        tabButtons.forEach(button => {
            button.addEventListener('click', async function(e) {
                e.preventDefault();
                const targetTab = this.getAttribute('data-tab');

                if (isDirty && mainForm) {
                    notify('กำลังบันทึกอัตโนมัติ...', 'info');

                    const formData = new FormData(mainForm);
                    formData.append('_method', 'PUT');

                    try {
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

                        notify('บันทึกอัตโนมัติแล้ว ✓', 'success');
                    } catch (error) {
                        notify('บันทึกไม่สำเร็จ กรุณาบันทึกด้วยตนเอง', 'error');
                    } finally {
                        isDirty = false;
                    }
                }

                activateTab(this, targetTab);
            });
        });

        // Sale control checkbox logic
        if (isSaleControlCheckbox && saleControlQtyInput) {
            isSaleControlCheckbox.addEventListener('change', function() {
                // Toggle visibility or styling if needed
                // For now, the field is always available, just checkbox toggles the flag
            });
        }

        // Profit calculation function
        function updateProfitCalculation() {
            const priceRetail = parseFloat(priceRetailInput.value) || 0;
            const avgCost = parseFloat(avgCostElement.getAttribute('data-avg')) || 0;
            const profitPerUnit = priceRetail - avgCost;
            const profitVsCost = avgCost > 0 ? (profitPerUnit / avgCost) * 100 : 0;
            const profitVsSale = priceRetail > 0 ? (profitPerUnit / priceRetail) * 100 : 0;
            const profitVsSaleEl = document.getElementById('profit-vs-sale');

            profitPerUnitSpan.textContent = profitPerUnit.toFixed(2);
            profitVsCostSpan.textContent = profitVsCost.toFixed(2);
            if (profitVsSaleEl) {
                profitVsSaleEl.textContent = profitVsSale.toFixed(2);
            }
        }

        // Listen to price_retail input changes
        if (priceRetailInput) {
            priceRetailInput.addEventListener('input', updateProfitCalculation);
            // Initial calculation on page load
            updateProfitCalculation();
        }
    });
</script>
@endsection
