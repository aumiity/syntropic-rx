@extends('layouts.app')

@section('content')
@php
    $oldProductIds = old('product_id', []);
    $rowCount = max(1, is_array($oldProductIds) ? count($oldProductIds) : 1);
    $paymentType = old('payment_type', 'cash');
    $isPaid = old('is_paid', $paymentType === 'cash');
    $activeTab = 'tab-receive';
    $selectedSupplierId = old('supplier_id');
    $selectedSupplier = $selectedSupplierId ? $suppliers->firstWhere('id', (int) $selectedSupplierId) : null;
    $supplierLookup = $suppliers->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values();

    $productLookup = $products->map(function ($product) {
        $identifier = $product->barcode ?: $product->code ?: 'ไม่มีรหัส';
        $units = $product->productUnits
            ->where('is_disabled', false)
            ->filter(fn ($unit) => $unit->is_for_purchase || $unit->is_base_unit)
            ->sortByDesc(fn ($unit) => (int) $unit->is_base_unit)
            ->values()
            ->map(fn ($unit) => [
                'id' => $unit->id,
                'unit_name' => $unit->unit_name,
                'qty_per_base' => $unit->qty_per_base,
                'price_retail' => $unit->price_retail,
                'is_base_unit' => (bool) $unit->is_base_unit,
            ])
            ->values();
        $baseUnit = $units->firstWhere('is_base_unit', true) ?? $units->first();

        return [
            'id' => $product->id,
            'name' => $product->trade_name,
            'label' => $product->trade_name . ' (' . $identifier . ')',
            'barcode' => $identifier,
            'unit_name' => $baseUnit['unit_name'] ?? optional($product->unit)->unit_name ?? $product->unit_name ?? '-',
            'base_unit' => $baseUnit,
            'units' => $units,
        ];
    })->values();
@endphp

<div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <div class="max-w-full mx-auto">
        <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">รับสินค้า</h1>
                <p class="text-sm text-gray-500">บันทึกรายการรับสินค้าใหม่เข้าสต๊อค</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-4 flex items-center justify-between">
            <a href="{{ route('reports.purchases') }}" class="text-sm text-slate-500 hover:text-emerald-600 flex items-center gap-1">
                ← ดูประวัติการรับสินค้า
            </a>
        </div>

        <div class="space-y-5">
            <div id="tab-receive" class="tab-panel active">
                <form action="{{ route('pos.stock.receive.store') }}" method="POST" id="stock-receive-form" class="space-y-5">
                    @csrf

                    <div class="rounded-xl border border-gray-200 bg-white p-5">
                        <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">ข้อมูลการรับสินค้า</h2>
                                <p class="text-sm text-gray-500">เลขที่เอกสารจะถูกสร้างให้อัตโนมัติ และบันทึกรายละเอียดการชำระเงินได้ทันที</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-500">
                                วันที่วันนี้: {{ now()->format('d/m/Y') }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-600">เลขที่เอกสาร (auto)</label>
                                <input type="text" name="invoice_no" value="{{ old('invoice_no', $nextPoNumber) }}" readonly class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-600">ผู้จำหน่าย <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="hidden" name="supplier_id" id="supplier_id_hidden" value="{{ old('supplier_id') }}">
                                    <input type="text" id="supplier_search" autocomplete="off" value="{{ $selectedSupplier?->name ?? '' }}"
                                        placeholder="พิมพ์ค้นหาผู้จำหน่าย..."
                                        class="h-11 w-full rounded-lg border border-gray-300 px-3 pr-10 text-sm focus:border-emerald-400 focus:outline-none">
                                    <button type="button" id="clear_supplier" class="absolute right-2 top-1/2 hidden h-6 w-6 -translate-y-1/2 rounded text-gray-600 hover:bg-gray-100">×</button>
                                    <div id="supplier_dropdown" class="absolute z-30 mt-1 hidden max-h-56 w-full overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg"></div>
                                </div>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-600">เลขที่บิล</label>
                                <input type="text" name="supplier_invoice_no" value="{{ old('supplier_invoice_no') }}" class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none" placeholder="เลขที่บิลจากผู้จำหน่าย">
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-600">วันที่สั่งซื้อ</label>
                                <input id="receive_date" type="date" name="receive_date"
                                    value="{{ old('receive_date') ? \Carbon\Carbon::parse(old('receive_date'))->format('Y-m-d') : now()->format('Y-m-d') }}"
                                    class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-600">การชำระเงิน</label>
                                <select id="payment_type" name="payment_type" class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none">
                                    <option value="cash" {{ $paymentType === 'cash' ? 'selected' : '' }}>เงินสด</option>
                                    <option value="credit" {{ $paymentType === 'credit' ? 'selected' : '' }}>เครดิต</option>
                                </select>
                            </div>
                            <div id="due-date-wrapper" class="{{ $paymentType === 'credit' ? '' : 'hidden' }}">
                                <label class="mb-1 block text-sm font-medium text-gray-600">วันครบกำหนดชำระ</label>
                                <div class="space-y-2">
                                    <input id="due_date" type="date" name="due_date" value="{{ old('due_date') }}" class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none">
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" data-due-days="30" class="due-shortcut rounded-lg border border-gray-200 px-2.5 py-1 text-xs text-gray-600 hover:bg-gray-50">+30 วัน</button>
                                        <button type="button" data-due-days="60" class="due-shortcut rounded-lg border border-gray-200 px-2.5 py-1 text-xs text-gray-600 hover:bg-gray-50">+60 วัน</button>
                                        <button type="button" data-due-days="90" class="due-shortcut rounded-lg border border-gray-200 px-2.5 py-1 text-xs text-gray-600 hover:bg-gray-50">+90 วัน</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-col gap-3 rounded-xl border border-dashed border-gray-200 bg-gray-50 px-4 py-3 md:flex-row md:items-center md:justify-between">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input id="is_paid" type="checkbox" name="is_paid" value="1" {{ $isPaid ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-emerald-500 focus:ring-emerald-400">
                                ชำระเงินแล้ว
                            </label>

                            <div id="paid-date-wrapper" class="{{ $isPaid ? '' : 'hidden' }} flex items-center gap-2">
                                <label class="text-sm text-gray-600">วันที่ชำระ</label>
                                <input id="paid_date" type="date" name="paid_date" value="{{ old('paid_date', now()->toDateString()) }}" class="h-10 rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white p-5">
                        <div>
                            <div class="overflow-x-auto rounded-xl border border-gray-200">
                                <table class="min-w-full text-sm" id="import-table">
                                    <thead class="bg-gray-50 text-gray-600">
                                        <tr>
                                            <th class="px-2 py-3 text-center font-medium w-8">#</th>
                                            <th class="px-2 py-3 text-left font-medium">Barcode / ชื่อยา</th>
                                            <th class="px-2 py-3 text-left font-medium w-24">หน่วย</th>
                                            <th class="px-2 py-3 text-left font-medium w-20">จำนวน</th>
                                            <th class="px-2 py-3 text-left font-medium w-28">ล็อต</th>
                                            <th class="px-2 py-3 text-left font-medium w-28">วันผลิต</th>
                                            <th class="px-2 py-3 text-left font-medium w-28">วันหมดอายุ</th>
                                            <th class="px-2 py-3 text-left font-medium w-28">ราคารวม (บาท)</th>
                                            <th class="px-2 py-3 text-center font-medium w-10">-</th>
                                        </tr>
                                    </thead>
                                    <tbody id="import-items-body"></tbody>
                                </table>
                            </div>
                            <div class="mt-4 flex items-center justify-between flex-wrap gap-3">
                                <button type="button" id="add-import-row"
                                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                                    + เพิ่มรายการ
                                </button>

                                <div class="flex items-center gap-4">
                                    <button type="button" id="open-bill-discount"
                                        class="rounded-lg border border-emerald-300 px-4 py-2 text-sm text-emerald-600 hover:bg-emerald-50">
                                        ปรับยอดท้ายบิล
                                    </button>
                                    <div class="text-right space-y-1">
                                        <div id="summary-before-adjust" class="hidden">
                                            <p class="text-sm text-gray-500">
                                                ยอดก่อนปรับ
                                                <span id="import-subtotal" class="font-medium text-gray-700 ml-2">0.00</span> บาท
                                            </p>
                                        </div>
                                        <div id="summary-discount-row" class="hidden">
                                            <p class="text-sm text-emerald-600">
                                                ส่วนลด
                                                <span id="summary-discount-amount" class="font-medium ml-2">0.00</span> บาท
                                            </p>
                                        </div>
                                        <div id="summary-surcharge-row" class="hidden">
                                            <p class="text-sm text-amber-600">
                                                ค่าเพิ่ม
                                                <span id="summary-surcharge-amount" class="font-medium ml-2">0.00</span> บาท
                                            </p>
                                        </div>
                                        <p class="text-xs text-gray-500">ยอดรวมทั้งหมด</p>
                                        <p class="text-2xl font-semibold text-emerald-600">
                                            <span id="import-grand-total">0.00</span> บาท
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="bill-discount-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
                                                                            <div id="bill-toast" class="fixed bottom-6 left-1/2 z-50 hidden -translate-x-1/2 rounded-xl border border-red-200 bg-white px-5 py-3 shadow-lg">
                                                                                <p id="bill-toast-msg" class="text-sm font-medium text-red-600"></p>
                                                                            </div>
                            <div class="w-96 rounded-xl border border-gray-200 bg-white p-5 shadow-xl">
                                <h3 class="mb-4 text-base font-medium text-gray-800">ปรับยอดท้ายบิล</h3>

                                <div class="mb-3 grid grid-cols-3 items-center gap-2">
                                    <label class="text-sm text-gray-600">ส่วนลด</label>
                                    <input type="number" id="bill-discount-value" min="0" step="0.01" value=""
                                        placeholder="0.00"
                                        class="col-span-1 h-10 rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none">
                                    <select id="bill-discount-type"
                                        class="h-10 rounded-lg border border-gray-300 px-2 text-sm focus:border-emerald-400 focus:outline-none">
                                        <option value="amount">บาท</option>
                                        <option value="percent">%</option>
                                    </select>
                                </div>

                                <div class="mb-5 grid grid-cols-3 items-center gap-2">
                                    <label class="text-sm text-gray-600">ค่าเพิ่ม</label>
                                    <input type="number" id="bill-surcharge-value" min="0" step="0.01" value=""
                                        placeholder="0.00"
                                        class="col-span-1 h-10 rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none">
                                    <select id="bill-surcharge-type"
                                        class="h-10 rounded-lg border border-gray-300 px-2 text-sm focus:border-emerald-400 focus:outline-none">
                                        <option value="amount">บาท</option>
                                        <option value="percent">%</option>
                                    </select>
                                </div>

                                <div class="flex gap-2">
                                    <button type="button" id="bill-discount-cancel"
                                        class="h-10 flex-1 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50">
                                        ยกเลิก
                                    </button>
                                    <button type="button" id="bill-discount-confirm"
                                        class="h-10 flex-1 rounded-lg bg-emerald-500 text-sm text-white hover:bg-emerald-600">
                                        ตกลง
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" id="primary-submit-btn" class="rounded-lg bg-emerald-500 px-6 py-3 text-sm font-medium text-white hover:bg-emerald-600">บันทึกการรับเข้า</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const initialProducts = @json($productLookup);
    const supplierList = @json($supplierLookup);

    function switchPurchaseTab(tabId) {
        document.querySelectorAll('.tab-button').forEach((button) => {
            const isActive = button.dataset.tab === tabId;
            button.classList.toggle('active', isActive);
            button.classList.toggle('text-emerald-600', isActive);
            button.classList.toggle('border-emerald-600', isActive);
            button.classList.toggle('text-gray-600', !isActive);
            button.classList.toggle('border-transparent', !isActive);
        });

        document.querySelectorAll('.tab-panel').forEach((panel) => {
            panel.classList.toggle('hidden', panel.id !== tabId);
            panel.classList.toggle('active', panel.id === tabId);
        });
    }

    function parseDDMMYY(value) {
        const clean = String(value ?? '').replace(/\D/g, '');
        if (clean.length !== 6 && clean.length !== 8) {
            return null;
        }

        const dd = clean.slice(0, 2);
        const mm = clean.slice(2, 4);
        const yyyy = clean.length === 6 ? `20${clean.slice(4, 6)}` : clean.slice(4, 8);
        const iso = `${yyyy}-${mm}-${dd}`;
        const date = new Date(`${iso}T00:00:00`);

        if (
            Number.isNaN(date.getTime()) ||
            date.getFullYear() !== Number(yyyy) ||
            date.getMonth() + 1 !== Number(mm) ||
            date.getDate() !== Number(dd)
        ) {
            return null;
        }

        return {
            display: `${dd}/${mm}/${yyyy}`,
            iso,
        };
    }

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('stock-receive-form');
        const paymentType = document.getElementById('payment_type');
        const dueDateWrapper = document.getElementById('due-date-wrapper');
        const dueDateInput = document.getElementById('due_date');
        const receiveDateInput = document.getElementById('receive_date');
        const isPaidCheckbox = document.getElementById('is_paid');
        const paidDateWrapper = document.getElementById('paid-date-wrapper');
        const paidDateInput = document.getElementById('paid_date');
        const supplierHidden = document.getElementById('supplier_id_hidden');
        const supplierSearch = document.getElementById('supplier_search');
        const clearSupplierButton = document.getElementById('clear_supplier');
        const supplierDropdown = document.getElementById('supplier_dropdown');

        document.querySelectorAll('.tab-button').forEach((button) => {
            button.addEventListener('click', () => switchPurchaseTab(button.dataset.tab));
        });

                function updateImportRowNumbers() {
                        document.querySelectorAll('#import-items-body tr[data-import-row]').forEach((row, i) => {
                                const cell = row.querySelector('.import-row-num');
                                if (cell) cell.textContent = i + 1;
                        });
                }

                function updateImportGrandTotal() {
                        let sum = 0;
                        document.querySelectorAll('#import-items-body tr[data-import-row]').forEach((row) => {
                                sum += parseFloat(row.querySelector('.import-total')?.value || 0);
                        });
                        const el = document.getElementById('import-grand-total');
                        if (el) el.textContent = sum.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }

                function populateImportUnitSelect(row, units) {
                        const select = row.querySelector('.import-unit-select');
                        const unitIdInput = row.querySelector('.import-unit-id');
                        if (!select) return;
                        select.innerHTML = '<option value="">-</option>';
                        (units || []).forEach((u) => {
                                const opt = document.createElement('option');
                                opt.value = u.id;
                                opt.dataset.qtyPerBase = u.qty_per_base;
                                opt.textContent = u.unit_name;
                                if (u.is_base_unit) opt.selected = true;
                                select.appendChild(opt);
                        });
                        if (unitIdInput) unitIdInput.value = select.value;
                }

                function createImportRow() {
                        const tr = document.createElement('tr');
                        tr.setAttribute('data-import-row', '');
                        tr.className = 'border-t border-gray-100';
                        tr.innerHTML = `
                                <td class="import-row-num px-2 py-3 text-center text-gray-500 align-top"></td>
                                <td class="min-w-64 px-2 py-3 align-top">
                                    <input type="hidden" class="import-product-id">
                                    <input type="hidden" class="import-unit-id">
                                    <div class="selected-product hidden items-start justify-between gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2">
                                        <div class="min-w-0">
                                            <div class="selected-product-label truncate font-medium text-gray-700"></div>
                                            <div class="selected-product-meta text-[11px] text-gray-500"></div>
                                        </div>
                                        <button type="button" class="clear-selected-product shrink-0 text-xs font-medium text-emerald-600 hover:text-emerald-700">เปลี่ยน</button>
                                    </div>
                                    <div class="product-search-wrap relative">
                                        <input type="text" class="product-search-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-400 focus:outline-none" placeholder="Barcode / ชื่อยา">
                                        <div class="product-search-results absolute z-30 mt-1 hidden max-h-56 w-full overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg"></div>
                                    </div>
                                </td>
                                <td class="px-2 py-3 align-top">
                                    <select class="import-unit-select w-full rounded-lg border border-gray-300 px-2 py-2 text-sm focus:border-emerald-400 focus:outline-none">
                                        <option value="">-</option>
                                    </select>
                                </td>
                                <td class="px-2 py-3 align-top">
                                    <input type="number" class="import-qty w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-400 focus:outline-none" min="1" value="1">
                                </td>
                                <td class="px-2 py-3 align-top">
                                    <input type="text" class="import-lot w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-400 focus:outline-none" placeholder="LOT...">
                                </td>
                                <td class="px-2 py-3 align-top">
                                    <input type="text" class="import-mfg expiry-date-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-400 focus:outline-none" placeholder="ddmmyy">
                                </td>
                                <td class="px-2 py-3 align-top">
                                    <input type="text" class="import-exp expiry-date-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-400 focus:outline-none" placeholder="ddmmyy">
                                </td>
                                <td class="px-2 py-3 align-top">
                                    <input type="number" class="import-total w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-400 focus:outline-none" min="0" step="0.01" value="0">
                                </td>
                                <td class="px-2 py-3 text-center align-top">
                                    <button type="button" class="remove-import-row rounded-lg border border-red-200 px-3 py-2 text-xs text-red-600 hover:bg-red-50">ลบ</button>
                                </td>
                        `;
                        return tr;
                }

                const importBody = document.getElementById('import-items-body');
                for (let i = 0; i < 3; i++) {
                        importBody.appendChild(createImportRow());
                }
                updateImportRowNumbers();

                document.getElementById('add-import-row').addEventListener('click', () => {
                        importBody.appendChild(createImportRow());
                        updateImportRowNumbers();
                });

                document.getElementById('import-items-body').addEventListener('input', (e) => {
                    if (!e.target.classList.contains('product-search-input')) return;
                    const row = e.target.closest('tr[data-import-row]');
                    if (!row) return;
                    const query = e.target.value.trim();
                    if (query.length < 1) {
                        row.querySelector('.product-search-results')?.classList.add('hidden');
                        return;
                    }
                    clearTimeout(row._importSearchTimer);
                    row._importSearchTimer = setTimeout(async () => {
                        const products = await searchProducts(query);
                        renderSearchResults(row, products);
                    }, 250);
                });

                document.getElementById('import-items-body').addEventListener('mousedown', (e) => {
                    const row = e.target.closest('tr[data-import-row]');
                    if (!row) return;

                    const selectBtn = e.target.closest('[data-select-product]');
                    if (selectBtn) {
                        selectSearchResult(row, selectBtn);
                        return;
                    }
                });

                document.getElementById('import-items-body').addEventListener('click', (e) => {
                    const row = e.target.closest('tr[data-import-row]');
                    if (!row) return;

                    if (e.target.closest('.clear-selected-product')) {
                        row.querySelector('.selected-product').classList.add('hidden');
                        row.querySelector('.selected-product').classList.remove('flex');
                        row.querySelector('.product-search-wrap').classList.remove('hidden');
                        row.querySelector('.product-search-input').value = '';
                        row.querySelector('.product-search-results').classList.add('hidden');
                        row.querySelector('.import-product-id').value = '';
                        row.querySelector('.import-unit-id').value = '';
                        populateImportUnitSelect(row, []);
                        return;
                    }

                    if (e.target.closest('.remove-import-row')) {
                        const rows = document.querySelectorAll('#import-items-body tr[data-import-row]');
                        if (rows.length > 1) row.remove();
                        else row.querySelector('.import-product-id').value = '';
                        updateImportRowNumbers();
                        updateImportGrandTotal();
                    }
                });

                document.getElementById('import-items-body').addEventListener('change', (e) => {
                    if (!e.target.classList.contains('import-unit-select')) return;
                    const row = e.target.closest('tr[data-import-row]');
                    if (!row) return;
                    row.querySelector('.import-unit-id').value = e.target.value;
                });

                document.getElementById('import-items-body').addEventListener('input', (e) => {
                    if (e.target.classList.contains('import-total')) updateImportGrandTotal();
                });

                document.getElementById('import-items-body').addEventListener('paste', async (e) => {
                    const target = e.target.closest('tr[data-import-row]');
                    if (!target) return;
                    e.preventDefault();
                    const text = e.clipboardData.getData('text');
                    const lines = text.split('\n').map((l) => l.trim()).filter(Boolean);
                    const allRows = [...document.querySelectorAll('#import-items-body tr[data-import-row]')];
                    const startIndex = allRows.indexOf(target);

                    for (let i = 0; i < lines.length; i++) {
                        const cols = lines[i].split('\t');
                        const [barcode, qty, lot, mfgRaw, expRaw, totalPrice] = cols;
                        let row = allRows[startIndex + i];
                        if (!row) {
                            row = createImportRow();
                            document.getElementById('import-items-body').appendChild(row);
                            allRows.push(row);
                        }
                        if (qty) row.querySelector('.import-qty').value = parseFloat(qty) || 1;
                        if (lot) row.querySelector('.import-lot').value = lot.trim();
                        if (mfgRaw) row.querySelector('.import-mfg').value = mfgRaw.trim();
                        if (expRaw) row.querySelector('.import-exp').value = expRaw.trim();
                        if (totalPrice) row.querySelector('.import-total').value = parseFloat(totalPrice.replace(/,/g, '')) || 0;

                        if (barcode) {
                            const barcodeClean = barcode.trim();
                            row.querySelector('.product-search-input').value = barcodeClean;
                            const results = await searchProducts(barcodeClean);
                            if (results.length === 1) {
                                applyProductSelection(row, results[0]);
                                populateImportUnitSelect(row, (results[0].units || []).filter((u) => u.is_for_purchase || u.is_base_unit));
                                row.querySelector('.import-product-id').value = results[0].id;
                            }
                        }
                    }
                    updateImportRowNumbers();
                    updateImportGrandTotal();
                });

                function submitImportRows() {
                    const rows = [...document.querySelectorAll('#import-items-body tr[data-import-row]')];
                    let valid = true;

                    rows.forEach((row) => {
                        const pid = row.querySelector('.import-product-id').value;
                        const qty = parseFloat(row.querySelector('.import-qty').value);
                        const exp = row.querySelector('.import-exp').value.trim();
                        ['.import-product-id', '.import-qty', '.import-exp'].forEach((sel) => {
                            row.querySelector(sel)?.classList.remove('border-red-400');
                        });
                        if (!pid) {
                            row.querySelector('.product-search-input').classList.add('border-red-400');
                            valid = false;
                        }
                        if (!qty || qty < 1) {
                            row.querySelector('.import-qty').classList.add('border-red-400');
                            valid = false;
                        }
                        if (!exp) {
                            row.querySelector('.import-exp').classList.add('border-red-400');
                            valid = false;
                        }
                    });

                    if (!valid) {
                        alert('กรุณากรอกข้อมูลให้ครบ: ชื่อยา, จำนวน และวันหมดอายุ');
                        return;
                    }

                    const totalSum = rows.reduce((s, row) => s + (parseFloat(row.querySelector('.import-total').value) || 0), 0);
                    const confirmed = confirm(
                        `ยืนยันการรับสินค้า?\nจำนวน ${rows.length} รายการ\nมูลค่ารวม ${totalSum.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} บาท`
                    );
                    if (!confirmed) return;

                    const form = document.getElementById('stock-receive-form');
                    form.querySelectorAll('.generated-import-input').forEach((el) => el.remove());

                    rows.forEach((row) => {
                        const qty = parseFloat(row.querySelector('.import-qty').value) || 1;
                        const total = parseFloat(row.querySelector('.import-total').value) || 0;
                        const costPrice = (total / qty).toFixed(4);

                        const getMfgIso = () => {
                            const input = row.querySelector('.import-mfg');
                            if (input.dataset.isoValue) return input.dataset.isoValue;
                            const parsed = parseDDMMYY(input.value.replace(/\D/g, ''));
                            return parsed ? parsed.iso : '';
                        };
                        const getExpIso = () => {
                            const input = row.querySelector('.import-exp');
                            if (input.dataset.isoValue) return input.dataset.isoValue;
                            const parsed = parseDDMMYY(input.value.replace(/\D/g, ''));
                            return parsed ? parsed.iso : '';
                        };

                        const fields = {
                            'product_id[]': row.querySelector('.import-product-id').value,
                            'unit_id[]': row.querySelector('.import-unit-id').value,
                            'qty_received[]': qty,
                            'lot_number[]': row.querySelector('.import-lot').value,
                            'manufactured_date[]': getMfgIso(),
                            'expiry_date[]': getExpIso(),
                            'cost_price[]': costPrice,
                            'discount[]': '0',
                            'line_total[]': total,
                        };

                        Object.entries(fields).forEach(([name, value]) => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.className = 'generated-import-input';
                            input.name = name;
                            input.value = value;
                            form.appendChild(input);
                        });
                    });

                    form.submit();
                }

                document.getElementById('import-submit-btn')?.addEventListener('click', submitImportRows);

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = String(value ?? '');
            return div.innerHTML;
        }

        function parseNumber(value) {
            const number = parseFloat(value);
            return Number.isFinite(number) ? number : 0;
        }

        function formatMoney(value) {
            return parseNumber(value).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        function getSearchResultRow(target) {
            return target.closest('tr[data-import-row]');
        }

        function togglePaidDateField() {
            const isChecked = Boolean(isPaidCheckbox?.checked);
            paidDateWrapper?.classList.toggle('hidden', !isChecked);
            if (isChecked && paidDateInput && !paidDateInput.value) {
                paidDateInput.value = new Date().toISOString().slice(0, 10);
            }
            if (!isChecked && paidDateInput) {
                paidDateInput.value = '';
            }
        }

        function togglePaymentFields() {
            const isCredit = paymentType?.value === 'credit';
            dueDateWrapper?.classList.toggle('hidden', !isCredit);
            if (!isCredit && dueDateInput) {
                dueDateInput.value = '';
            }
            if (isPaidCheckbox) {
                isPaidCheckbox.checked = !isCredit;
                togglePaidDateField();
            }
        }

        function resetDateInput(input) {
            if (!input) {
                return;
            }

            input.value = '';
            delete input.dataset.isoValue;
            input.classList.remove('border-red-400', 'border-emerald-400');
        }

        function applyProductSelection(row, product) {
            const productIdInput = row.querySelector('.import-product-id');
            if (productIdInput) {
                productIdInput.value = product.id;
            }

            const units = (product.units || []).filter((unit) => unit.is_for_purchase || unit.is_base_unit);
            populateImportUnitSelect(row, units);
            const unitSelect = row.querySelector('.import-unit-select');
            const unitIdInput = row.querySelector('.import-unit-id');
            const selectedUnitId = unitSelect?.value || product.base_unit?.id || units[0]?.id || '';
            const selectedUnit = units.find((unit) => String(unit.id) === String(selectedUnitId)) || product.base_unit || units[0] || null;
            if (unitIdInput) {
                unitIdInput.value = selectedUnitId;
            }

            row.querySelector('.selected-product-label').textContent = product.label || product.name || '';
            row.querySelector('.selected-product-meta').textContent = `หน่วย: ${selectedUnit?.unit_name || product.unit_name || '-'}`;
            row.querySelector('.product-search-input').value = '';
            row.querySelector('.product-search-input').classList.remove('border-red-400');

            const selectedBox = row.querySelector('.selected-product');
            selectedBox.classList.remove('hidden');
            selectedBox.classList.add('flex');

            row.querySelector('.product-search-wrap').classList.add('hidden');
            row.querySelector('.product-search-results').classList.add('hidden');
        }

        function selectSearchResult(row, selectButton) {
            if (!row || !selectButton) {
                return;
            }

            const matchedProduct = (row._searchResults || []).find((product) => String(product.id) === String(selectButton.dataset.productId));
            applyProductSelection(row, matchedProduct || {
                id: selectButton.dataset.productId,
                label: selectButton.dataset.productLabel,
                unit_name: selectButton.dataset.productUnit,
                units: [],
            });
        }

        async function searchProducts(query) {
            const response = await fetch(`/api/products/search?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                return [];
            }

            const items = await response.json();
            return items.map((item) => ({
                id: item.id,
                name: item.trade_name,
                label: `${item.trade_name} (${item.barcode || 'ไม่มีรหัส'})`,
                unit_name: item.base_unit?.unit_name || item.unit_name || '-',
                base_unit: item.base_unit || null,
                units: Array.isArray(item.units) ? item.units : [],
            }));
        }

        function renderSearchResults(row, products) {
            const resultsBox = row.querySelector('.product-search-results');
            if (!resultsBox) {
                return;
            }

            row._searchResults = products;

            if (!products.length) {
                resultsBox.innerHTML = '<div class="px-3 py-2 text-xs text-gray-500">ไม่พบสินค้า</div>';
                resultsBox.classList.remove('hidden');
                return;
            }

            resultsBox.innerHTML = products.map((product) => `
                <button
                    type="button"
                    class="block w-full border-b border-gray-100 px-3 py-2 text-left hover:bg-emerald-50 last:border-b-0"
                    data-select-product
                    data-product-id="${product.id}"
                    data-product-label="${escapeHtml(product.label)}"
                    data-product-unit="${escapeHtml(product.unit_name || '-')}"
                >
                    <div class="font-medium text-gray-700">${escapeHtml(product.label)}</div>
                    <div class="mt-0.5 text-[11px] text-gray-500">หน่วย: ${escapeHtml(product.unit_name || '-')}</div>
                </button>
            `).join('');
            resultsBox.classList.remove('hidden');
        }

        function updateSupplierClearState() {
            const hasValue = Boolean(supplierSearch?.value?.trim());
            clearSupplierButton?.classList.toggle('hidden', !hasValue);
        }

        function renderSupplierOptions(query = '') {
            if (!supplierDropdown) {
                return;
            }

            const search = query.trim().toLowerCase();
            const matches = supplierList
                .filter((supplier) => !search || String(supplier.name).toLowerCase().includes(search))
                .slice(0, 20);

            if (!matches.length) {
                supplierDropdown.innerHTML = '<div class="px-3 py-2 text-xs text-gray-500">ไม่พบผู้จำหน่าย</div>';
                supplierDropdown.classList.remove('hidden');
                return;
            }

            supplierDropdown.innerHTML = matches.map((supplier) => `
                <button
                    type="button"
                    class="block w-full border-b border-gray-100 px-3 py-2 text-left text-sm text-gray-700 hover:bg-emerald-50 last:border-b-0"
                    data-supplier-id="${supplier.id}"
                    data-supplier-name="${escapeHtml(supplier.name)}"
                >
                    ${escapeHtml(supplier.name)}
                </button>
            `).join('');
            supplierDropdown.classList.remove('hidden');
        }

                function showBillToast(message) {
                    const toast = document.getElementById('bill-toast');
                    const msg = document.getElementById('bill-toast-msg');
                    msg.textContent = message;
                    toast.classList.remove('hidden');
                    clearTimeout(toast._timer);
                    toast._timer = setTimeout(() => toast.classList.add('hidden'), 3000);
                }

                function closeBillModal() {
            const modal = document.getElementById('bill-discount-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('open-bill-discount').addEventListener('click', () => {
            const modal = document.getElementById('bill-discount-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.getElementById('bill-discount-value').focus();
        });

        document.getElementById('bill-discount-cancel').addEventListener('click', closeBillModal);

        document.getElementById('bill-discount-modal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('bill-discount-modal')) closeBillModal();
        });

        document.getElementById('bill-discount-confirm').addEventListener('click', () => {
            const rows = [...document.querySelectorAll('#import-items-body tr[data-import-row]')];
            const hasItems = rows.some(row => row.querySelector('.import-product-id')?.value);
            if (!hasItems) {
                showBillToast('กรุณาเพิ่มรายการยาก่อนปรับยอดท้ายบิล');
                return;
            }

            const discountVal = parseFloat(document.getElementById('bill-discount-value').value) || 0;
            const discountType = document.getElementById('bill-discount-type').value;
            const surchargeVal = parseFloat(document.getElementById('bill-surcharge-value').value) || 0;
            const surchargeType = document.getElementById('bill-surcharge-type').value;

            const rawTotals = rows.map(row => parseFloat(row.querySelector('.import-total')?.value) || 0);
            const sumRaw = rawTotals.reduce((a, b) => a + b, 0);
            if (sumRaw === 0) {
                showBillToast('ยอดรวมเป็น 0 ไม่สามารถปรับยอดได้');
                return;
            }

            const discountAmount = discountType === 'percent' ? sumRaw * (discountVal / 100) : discountVal;
            const surchargeAmount = surchargeType === 'percent' ? sumRaw * (surchargeVal / 100) : surchargeVal;
            const netAdjust = surchargeAmount - discountAmount;

            rows.forEach((row, i) => {
                const share = (rawTotals[i] / sumRaw) * netAdjust;
                const newTotal = Math.max((rawTotals[i] + share), 0);
                row.querySelector('.import-total').value = newTotal.toFixed(2);
            });

            const finalTotal = sumRaw + netAdjust;

            document.getElementById('import-subtotal').textContent =
                sumRaw.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('summary-discount-amount').textContent =
                discountAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('summary-surcharge-amount').textContent =
                surchargeAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('import-grand-total').textContent =
                Math.max(finalTotal, 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            const hasDiscount = discountAmount > 0;
            const hasSurcharge = surchargeAmount > 0;
            const hasAdjust = hasDiscount || hasSurcharge;

            document.getElementById('summary-before-adjust').classList.toggle('hidden', !hasAdjust);
            document.getElementById('summary-discount-row').classList.toggle('hidden', !hasDiscount);
            document.getElementById('summary-surcharge-row').classList.toggle('hidden', !hasSurcharge);

            closeBillModal();
        });

        supplierSearch?.addEventListener('input', () => {
            if (supplierHidden) {
                supplierHidden.value = '';
            }
            renderSupplierOptions(supplierSearch.value);
            updateSupplierClearState();
        });

        supplierSearch?.addEventListener('focus', () => {
            renderSupplierOptions(supplierSearch.value);
        });

        supplierSearch?.addEventListener('blur', () => {
            window.setTimeout(() => supplierDropdown?.classList.add('hidden'), 150);
        });

        supplierSearch?.addEventListener('keydown', function(event) {
            if (!['ArrowDown', 'ArrowUp', 'Enter'].includes(event.key)) return;
            if (supplierDropdown?.classList.contains('hidden')) return;

            const items = Array.from(supplierDropdown.querySelectorAll('button[data-supplier-id]'));
            if (!items.length) return;

            const current = supplierDropdown.querySelector('button[data-supplier-id].bg-emerald-100');
            let index = current ? items.indexOf(current) : -1;

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                if (current) current.classList.remove('bg-emerald-100');
                index = (index + 1) % items.length;
                items[index].classList.add('bg-emerald-100');
                items[index].scrollIntoView({ block: 'nearest' });
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                if (current) current.classList.remove('bg-emerald-100');
                index = (index - 1 + items.length) % items.length;
                items[index].classList.add('bg-emerald-100');
                items[index].scrollIntoView({ block: 'nearest' });
            } else if (event.key === 'Enter') {
                event.preventDefault();
                if (current) current.click();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (!['ArrowDown', 'ArrowUp', 'Enter'].includes(event.key)) return;

            const input = event.target;
            if (!input.classList.contains('product-search-input')) return;

            const row = getSearchResultRow(input);
            if (!row) return;

            const resultsBox = row.querySelector('.product-search-results');
            if (!resultsBox || resultsBox.classList.contains('hidden')) return;

            const items = Array.from(resultsBox.querySelectorAll('button[data-select-product]'));
            if (!items.length) return;

            const current = resultsBox.querySelector('button[data-select-product].bg-emerald-100');
            let index = current ? items.indexOf(current) : -1;

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                if (current) current.classList.remove('bg-emerald-100');
                index = (index + 1) % items.length;
                items[index].classList.add('bg-emerald-100');
                items[index].scrollIntoView({ block: 'nearest' });
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                if (current) current.classList.remove('bg-emerald-100');
                index = (index - 1 + items.length) % items.length;
                items[index].classList.add('bg-emerald-100');
                items[index].scrollIntoView({ block: 'nearest' });
            } else if (event.key === 'Enter') {
                event.preventDefault();
                if (current) selectSearchResult(row, current);
            }
        });

        clearSupplierButton?.addEventListener('click', () => {
            if (supplierHidden) {
                supplierHidden.value = '';
            }
            if (supplierSearch) {
                supplierSearch.value = '';
                supplierSearch.focus();
            }
            supplierDropdown?.classList.add('hidden');
            updateSupplierClearState();
        });

        supplierDropdown?.addEventListener('click', (event) => {
            const option = event.target.closest('[data-supplier-id]');
            if (!option) {
                return;
            }

            if (supplierHidden) {
                supplierHidden.value = option.dataset.supplierId || '';
            }
            if (supplierSearch) {
                supplierSearch.value = option.dataset.supplierName || '';
            }
            supplierDropdown.classList.add('hidden');
            updateSupplierClearState();
        });

        document.addEventListener('input', (event) => {
            if (event.target.classList.contains('expiry-date-input') || event.target.classList.contains('manufactured-date-input')) {
                const input = event.target;
                const clean = input.value.replace(/\D/g, '');

                if (clean.length === 6 || clean.length === 8) {
                    const parsed = parseDDMMYY(clean);
                    if (parsed) {
                        input.value = parsed.display;
                        input.dataset.isoValue = parsed.iso;
                        input.classList.remove('border-red-400');
                        input.classList.add('border-emerald-400');
                    } else {
                        delete input.dataset.isoValue;
                        input.classList.remove('border-emerald-400');
                        input.classList.add('border-red-400');
                    }
                } else if (clean.length === 0) {
                    delete input.dataset.isoValue;
                    input.classList.remove('border-red-400', 'border-emerald-400');
                }
            }

        });

        document.addEventListener('blur', (event) => {
            if (!event.target.classList.contains('expiry-date-input') && !event.target.classList.contains('manufactured-date-input')) return;
            const input = event.target;
            const clean = input.value.replace(/\D/g, '');
            if (clean.length === 6 || clean.length === 8) {
                const parsed = parseDDMMYY(clean);
                if (parsed) {
                    input.value = parsed.display;
                    input.dataset.isoValue = parsed.iso;
                    input.classList.remove('border-red-400');
                    input.classList.add('border-emerald-400');
                } else {
                    delete input.dataset.isoValue;
                    input.classList.remove('border-emerald-400');
                    input.classList.add('border-red-400');
                }
            }
        }, true);

        document.addEventListener('click', (event) => {
            if (!event.target.closest('.product-search-wrap')) {
                document.querySelectorAll('.product-search-results').forEach((box) => box.classList.add('hidden'));
            }

            if (!event.target.closest('#supplier_search') && !event.target.closest('#supplier_dropdown') && !event.target.closest('#clear_supplier')) {
                supplierDropdown?.classList.add('hidden');
            }
        });

        paymentType?.addEventListener('change', togglePaymentFields);
        isPaidCheckbox?.addEventListener('change', togglePaidDateField);

        document.querySelectorAll('.due-shortcut').forEach((button) => {
            button.addEventListener('click', () => {
                const days = parseInt(button.dataset.dueDays, 10) || 0;
                const baseDate = receiveDateInput?.value ? new Date(receiveDateInput.value) : new Date();
                baseDate.setDate(baseDate.getDate() + days);
                if (dueDateInput) {
                    dueDateInput.value = baseDate.toISOString().slice(0, 10);
                }
            });
        });

        document.getElementById('primary-submit-btn')?.addEventListener('click', () => {
            const hasImportData = Array.from(document.querySelectorAll('#import-items-body .import-product-id'))
                .some((input) => Boolean(input.value));
            if (hasImportData) {
                submitImportRows();
                return;
            }

            form?.requestSubmit();
        });

        form?.addEventListener('submit', function () {
            document.querySelectorAll('.expiry-date-input, .manufactured-date-input').forEach((input) => {
                if (input.dataset.isoValue) {
                    input.value = input.dataset.isoValue;
                    return;
                }

                const parsed = parseDDMMYY(input.value.replace(/\D/g, ''));
                if (parsed) {
                    input.value = parsed.iso;
                }
            });
        });

        updateSupplierClearState();
        togglePaymentFields();
        updateImportGrandTotal();
    });
</script>
@endsection
