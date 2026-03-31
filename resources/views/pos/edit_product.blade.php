@extends('layouts.app')

@section('content')
<div class="p-6 w-full">

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
            <button type="button" data-tab="tab-2" class="tab-button min-h-11 px-4 py-2.5 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800 rounded-t-lg">หน่วยอื่นๆ</button>
            <button type="button" data-tab="tab-3" class="tab-button min-h-11 px-4 py-2.5 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800 rounded-t-lg">ประเภทยาตามกฎหมาย</button>
            <button type="button" data-tab="tab-4" class="tab-button min-h-11 px-4 py-2.5 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800 rounded-t-lg">ฉลาก</button>
            <button type="button" data-tab="tab-5" class="tab-button min-h-11 px-4 py-2.5 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800 rounded-t-lg">สต๊อค</button>
            <button type="button" data-tab="tab-6" class="tab-button min-h-11 px-4 py-2.5 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800 rounded-t-lg">ข้อมูลอื่นๆ</button>
            <button type="button" data-tab="tab-7" class="tab-button min-h-11 px-4 py-2.5 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800 rounded-t-lg">ประวัติ</button>
        </div>

        <!-- Tab Panels -->
        <div class="space-y-5">

            <!-- Tab 1: ข้อมูลหลัก -->
            <div id="tab-1" class="tab-panel active bg-white border border-gray-200 rounded-xl p-5">
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <!-- รหัสสินค้า -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">รหัสสินค้า <span class="text-gray-400 text-xs">(สร้างอัตโนมัติ)</span></label>
                        <input type="text" readonly value="{{ $product->code }}" class="w-full h-10 rounded-lg bg-gray-100 border border-gray-200 px-3 text-sm text-gray-600">
                    </div>
                    <!-- ชื่อสินค้า (Trade Name) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ชื่อสินค้า (Trade Name) <span class="text-red-500">*</span></label>
                        <input type="text" name="trade_name" value="{{ old('trade_name', $product->trade_name) }}" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400" required>
                    </div>
                    <!-- ชื่อพิมพ์ (ฉลากยา) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ชื่อพิมพ์ (ฉลากยา)</label>
                        <input type="text" name="name_for_print" value="{{ old('name_for_print', $product->name_for_print) }}" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <!-- ชื่อที่ตั้งเอง (คำค้น) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ชื่อที่ตั้งเอง (คำค้น)</label>
                        <input type="text" name="search_keywords" value="{{ old('search_keywords', $product->search_keywords) }}" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <!-- ประเภทสินค้า -->
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
                    <!-- หน่วยขาย -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">หน่วยขาย</label>
                        <select name="unit_id" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                            <option value="">-- เลือกหน่วย --</option>
                            @foreach($itemUnits as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- เลขบาร์โค้ด 1 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">เลขบาร์โค้ด 1</label>
                        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <!-- เลขบาร์โค้ด 2 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">เลขบาร์โค้ด 2</label>
                        <input type="text" name="barcode2" value="{{ old('barcode2', $product->barcode2) }}" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <!-- จำนวนเริ่มต้นการขาย -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">จำนวนเริ่มต้นการขาย</label>
                        <input type="number" name="default_qty" value="{{ old('default_qty', $product->default_qty ?? 1) }}" min="1" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                </div>

                <!-- Info Row: ราคาทุนเฉลี่ย, จำนวนคงเหลือ -->
                <div class="grid grid-cols-3 gap-4 bg-gray-50 rounded-lg p-4 border border-gray-200 mb-6">
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">ราคาทุนเฉลี่ย</p>
                        <p class="text-sm font-semibold text-gray-800" data-avg="{{ $product->lots?->avg('cost_price') ?? 0 }}">฿{{ number_format($product->lots?->avg('cost_price') ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">จำนวนคงเหลือ</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $product->lots?->sum('qty_on_hand') ?? 0 }} หน่วย</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">ราคาทุนล่าสุด</p>
                        <p class="text-sm font-semibold text-gray-800">
                            ฿{{ number_format($product->lots?->sortByDesc('created_at')->first()?->cost_price ?? 0, 2) }}
                        </p>
                    </div>
                </div>

                <!-- Price Section -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-600 mb-4 pb-2 border-b border-gray-100">ราคา</h3>
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <!-- ราคาขายปลีก -->
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">ราคาขายปลีก <span class="text-red-500">*</span></label>
                            <input type="number" name="price_retail" id="price_retail" value="{{ old('price_retail', $product->price_retail) }}" step="0.01" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400" required>
                        </div>
                        <!-- ราคาส่ง ระดับ 1 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">ราคาส่ง ระดับ 1</label>
                            <input type="number" name="price_wholesale1" value="{{ old('price_wholesale1', $product->price_wholesale1) }}" step="0.01" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                        </div>
                        <!-- ราคาส่ง ระดับ 2 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">ราคาส่ง ระดับ 2</label>
                            <input type="number" name="price_wholesale2" value="{{ old('price_wholesale2', $product->price_wholesale2) }}" step="0.01" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                        </div>
                        <!-- จุดสั่งซื้อ -->
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">จุดสั่งซื้อ (Reorder Point)</label>
                            <input type="number" name="reorder_point" value="{{ old('reorder_point', $product->reorder_point) }}" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                        </div>
                        <!-- จำนวนที่ต้องการมี -->
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">จำนวนที่ต้องการมี (Safety Stock)</label>
                            <input type="number" name="safety_stock" value="{{ old('safety_stock', $product->safety_stock) }}" min="0" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                        </div>
                    </div>

                    <!-- Profit Calculation Row (Read-only) -->
                    <div class="grid grid-cols-3 gap-4 bg-amber-50 rounded-lg p-4 border border-amber-200">
                        <div>
                            <p class="text-xs font-medium text-amber-600 mb-1">กำไรต่อหน่วย</p>
                            <p class="text-sm font-semibold text-amber-800">฿<span id="profit-per-unit">0.00</span></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-amber-600 mb-1">กำไรเทียบทุน (%)</p>
                            <p class="text-sm font-semibold text-amber-800"><span id="profit-vs-cost">0.00</span>%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: หน่วยอื่นๆ -->

            <div id="tab-2" class="tab-panel hidden bg-white border border-gray-200 rounded-xl p-5">
                <div class="mb-4">
                    <span class="block text-sm font-medium text-gray-700 mb-1">หน่วยฐานของสินค้า:</span>
                    <span class="inline-block px-3 py-2 rounded bg-gray-100 text-gray-800 text-base font-semibold min-h-[44px]">{{ $product->unit->name ?? '-' }}</span>
                </div>

                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full text-sm border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">หน่วย</th>
                                <th class="px-3 py-2 text-right font-medium text-gray-600">จำนวนเทียบหน่วยฐาน</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">Barcode</th>
                                <th class="px-3 py-2 text-center font-medium text-gray-600">ขายได้</th>
                                <th class="px-3 py-2 text-center font-medium text-gray-600">รับเข้าได้</th>
                                <th class="px-3 py-2 text-center font-medium text-gray-600">ลบ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($product->productUnits as $unit)
                                <tr class="border-b border-gray-100">
                                    <td class="px-3 py-2 min-h-[44px]">{{ $unit->unit->name ?? '-' }}</td>
                                    <td class="px-3 py-2 text-right min-h-[44px]">{{ $unit->qty_per_base }}</td>
                                    <td class="px-3 py-2 min-h-[44px]">{{ $unit->barcode }}</td>
                                    <td class="px-3 py-2 text-center min-h-[44px]">
                                        @if($unit->is_for_sale)
                                            <span class="inline-block w-6 h-6 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center">✓</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-center min-h-[44px]">
                                        @if($unit->is_for_purchase)
                                            <span class="inline-block w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">✓</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-center min-h-[44px]">
                                        <form action="{{ route('products.units.destroy', [$product, $unit]) }}" method="POST" onsubmit="return confirm('ลบหน่วยนี้?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center px-3 py-2 rounded bg-red-50 text-red-600 hover:bg-red-100 min-h-[44px]" title="ลบ">
                                                ลบ
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-gray-400 py-4">ไม่มีหน่วยอื่นๆ</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 pt-4 mt-4">
                    <form action="{{ route('products.units.store', $product) }}" method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">หน่วย</label>
                            <select name="unit_id" class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400 min-h-[44px]" required>
                                <option value="">-- เลือก --</option>
                                @foreach($itemUnits as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">จำนวนเทียบหน่วยฐาน</label>
                            <input type="number" name="qty_per_base" step="0.0001" min="0.0001" class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400 min-h-[44px]" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Barcode</label>
                            <input type="text" name="barcode" class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400 min-h-[44px]">
                        </div>
                        <div class="flex items-center min-h-[44px]">
                            <label class="inline-flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                                <input type="checkbox" name="is_for_sale" value="1" class="w-5 h-5 rounded">
                                ขายได้
                            </label>
                        </div>
                        <div class="flex items-center min-h-[44px]">
                            <label class="inline-flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                                <input type="checkbox" name="is_for_purchase" value="1" class="w-5 h-5 rounded">
                                รับเข้าได้
                            </label>
                        </div>
                        <div>
                            <button type="submit" class="w-full h-11 px-4 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700 min-h-[44px]">เพิ่มหน่วย</button>
                        </div>
                    </form>
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
</div>

<!-- Tab Switching & Profit Calculation JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabPanels = document.querySelectorAll('.tab-panel');
        const priceRetailInput = document.getElementById('price_retail');
        const avgCostElement = document.querySelector('[data-avg]');
        const profitPerUnitSpan = document.getElementById('profit-per-unit');
        const profitVsCostSpan = document.getElementById('profit-vs-cost');
        const isSaleControlCheckbox = document.getElementById('is_sale_control');
        const saleControlQtyInput = document.querySelector('input[name="sale_control_qty"]');

        // Tab switching
        tabButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const targetTab = this.getAttribute('data-tab');

                // Remove active state from all buttons and panels
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'text-emerald-600', 'border-emerald-600');
                    btn.classList.add('text-gray-600', 'border-transparent');
                });
                tabPanels.forEach(panel => {
                    panel.classList.add('hidden');
                    panel.classList.remove('active');
                });

                // Add active state to clicked button and corresponding panel
                this.classList.add('active', 'text-emerald-600', 'border-emerald-600');
                this.classList.remove('text-gray-600', 'border-transparent');
                document.getElementById(targetTab).classList.remove('hidden');
                document.getElementById(targetTab).classList.add('active');
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

            profitPerUnitSpan.textContent = profitPerUnit.toFixed(2);
            profitVsCostSpan.textContent = profitVsCost.toFixed(2);
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
