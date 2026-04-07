@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <div class="max-w-5xl mx-auto">

        {{-- Page Header --}}
        <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">ประวัติการรับสินค้า</h1>
                <p class="text-sm text-gray-500">รายการรับยาเข้าสต๊อค</p>
            </div>
            <div class="flex items-center gap-2">
                @if($billHeader)
                    <a href="{{ route('pos.stock.receive') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">← กลับรายการบิล</a>
                @else
                    <a href="{{ route('pos.stock.receive') }}" class="rounded-lg bg-emerald-500 px-4 py-2 text-sm text-white hover:bg-emerald-600">รับสินค้าใหม่</a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if($billHeader)
            {{-- ===== SINGLE BILL MODE ===== --}}

            {{-- Bill Header Card --}}
            <div class="mb-5 rounded-xl border border-gray-200 bg-white p-5">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">{{ $billHeader->invoice_no }}</h2>
                    <div class="flex items-center gap-2">
                        @if($billHeader->is_cancelled ?? false)
                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700">
                                ยกเลิกแล้ว
                            </span>
                        @else
                            <span class="rounded-full px-3 py-1 text-xs font-medium
                                {{ ($billHeader->is_paid ?? false) ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ ($billHeader->is_paid ?? false) ? 'ชำระแล้ว' : 'ค้างชำระ' }}
                            </span>
                            <button type="button" onclick="openEditBillModal()"
                                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50">
                                แก้ไข
                            </button>
                            <button type="button" onclick="openCancelBillModal()"
                                class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600 hover:bg-red-50">
                                ยกเลิกบิล
                            </button>
                        @endif
                    </div>
                </div>

                @php $naText = '<span class="text-gray-300">N/A</span>'; @endphp
                <div class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm md:grid-cols-3">
                    <div>
                        <p class="mb-0.5 text-xs text-gray-400">เลขที่เอกสาร</p>
                        <p class="font-medium text-gray-800">{{ $billHeader->invoice_no ?: 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="mb-0.5 text-xs text-gray-400">เลขที่บิลผู้จำหน่าย</p>
                        <p class="font-medium text-gray-800">
                            @if(isset($billHeader->supplier_invoice_no))
                                {{ $billHeader->supplier_invoice_no ?: 'N/A' }}
                            @else
                                {!! $naText !!}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="mb-0.5 text-xs text-gray-400">ผู้จำหน่าย</p>
                        <p class="font-medium text-gray-800">{{ $billHeader->supplier_name ?: 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="mb-0.5 text-xs text-gray-400">วันที่รับสินค้า</p>
                        <p class="font-medium text-gray-800">
                            {{ $billHeader->created_at ? \Carbon\Carbon::parse($billHeader->created_at)->format('d/m/Y') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="mb-0.5 text-xs text-gray-400">การชำระเงิน</p>
                        <p class="font-medium text-gray-800">
                            @if(isset($billHeader->payment_type))
                                {{ $billHeader->payment_type === 'credit' ? 'เครดิต' : 'เงินสด' }}
                            @else
                                {!! $naText !!}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="mb-0.5 text-xs text-gray-400">วันครบกำหนดชำระ</p>
                        <p class="font-medium text-gray-800">
                            @if(isset($billHeader->due_date))
                                {{ $billHeader->due_date ? \Carbon\Carbon::parse($billHeader->due_date)->format('d/m/Y') : 'N/A' }}
                            @else
                                {!! $naText !!}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="mb-0.5 text-xs text-gray-400">วันที่ชำระเงิน</p>
                        <p class="font-medium text-gray-800">
                            @if(isset($billHeader->paid_date))
                                {{ $billHeader->paid_date ? \Carbon\Carbon::parse($billHeader->paid_date)->format('d/m/Y') : 'N/A' }}
                            @else
                                {!! $naText !!}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="mb-0.5 text-xs text-gray-400">จำนวนรายการ</p>
                        <p class="font-medium text-gray-800">{{ number_format($movements->total()) }} รายการ</p>
                    </div>
                    <div>
                        <p class="mb-0.5 text-xs text-gray-400">มูลค่ารวม</p>
                        <p class="text-lg font-semibold text-emerald-600">{{ number_format((float)$billHeader->total_value, 2) }} บาท</p>
                    </div>
                </div>
            </div>{{-- /Bill Header Card --}}

            {{-- Items Table --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-800">รายการยา</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium">#</th>
                                <th class="px-4 py-3 text-left font-medium">ชื่อยา</th>
                                <th class="px-4 py-3 text-left font-medium">Lot No.</th>
                                <th class="px-4 py-3 text-left font-medium">วันหมดอายุ</th>
                                <th class="px-4 py-3 text-center font-medium">จำนวน</th>
                                <th class="px-4 py-3 text-right font-medium">ราคาทุน/หน่วย</th>
                                <th class="px-4 py-3 text-right font-medium">รวม</th>
                                <th class="px-4 py-3 text-left font-medium">หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $i => $movement)
                                <tr class="border-t border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-800">{{ $movement->trade_name }}</p>
                                        @if($movement->barcode ?? $movement->code)
                                            <p class="text-[11px] text-gray-400">{{ $movement->barcode ?? $movement->code }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $movement->lot_number ?: '-' }}</td>
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ $movement->expiry_date ? \Carbon\Carbon::parse($movement->expiry_date)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-medium text-gray-800">{{ number_format($movement->qty_change) }}</td>
                                    <td class="px-4 py-3 text-right text-gray-700">{{ number_format($movement->unit_cost, 2) }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-800">{{ number_format($movement->qty_change * $movement->unit_cost, 2) }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $movement->note ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-sm text-gray-500">ไม่พบรายการ</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>{{-- /Items Table --}}

        @else
            {{-- ===== LIST MODE ===== --}}

            @php
                $totalQty = $movements->sum('qty_change');
                $totalValue = $movements->sum(fn($m) => $m->qty_change * $m->unit_cost);
            @endphp
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm">
                <div class="flex gap-6 text-emerald-800">
                    <div><strong>จำนวนรวม:</strong> {{ number_format($totalQty) }}</div>
                    <div><strong>มูลค่ารวม:</strong> {{ number_format($totalValue, 2) }} บาท</div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium">วันที่</th>
                                <th class="px-4 py-3 text-left font-medium">เลขที่เอกสาร</th>
                                <th class="px-4 py-3 text-left font-medium">สินค้า</th>
                                <th class="px-4 py-3 text-left font-medium">Lot</th>
                                <th class="px-4 py-3 text-left font-medium">ผู้จำหน่าย</th>
                                <th class="px-4 py-3 text-center font-medium">จำนวน</th>
                                <th class="px-4 py-3 text-right font-medium">ราคาทุน</th>
                                <th class="px-4 py-3 text-right font-medium">รวม</th>
                                <th class="px-4 py-3 text-left font-medium">ผู้บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $movement)
                                <tr class="border-t border-gray-100">
                                    <td class="px-4 py-3 text-gray-700">{{ \Carbon\Carbon::parse($movement->created_at)->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $movement->invoice_no ?: '-' }}</td>
                                    <td class="px-4 py-3">
                                        <p class="text-gray-800">{{ $movement->trade_name }}</p>
                                        <p class="text-[11px] text-gray-400">{{ $movement->barcode ?? $movement->code }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ $movement->lot_number }}<br>
                                        <span class="text-[11px] text-gray-400">หมดอายุ: {{ $movement->expiry_date ? \Carbon\Carbon::parse($movement->expiry_date)->format('d/m/Y') : '-' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $movement->supplier_name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center text-gray-700">{{ number_format($movement->qty_change) }}</td>
                                    <td class="px-4 py-3 text-right text-gray-700">{{ number_format($movement->unit_cost, 2) }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-800">{{ number_format($movement->qty_change * $movement->unit_cost, 2) }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $movement->created_by_name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-10 text-center text-sm text-gray-500">ไม่พบรายการ</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $movements->appends(request()->query())->links() }}
                </div>
            </div>
        @endif

    </div>
</div>

{{-- ===== MODALS (outside all cards, at body level) ===== --}}

@if($billHeader && !($billHeader->is_cancelled ?? false))

{{-- Edit Bill Modal --}}
<div id="edit-bill-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-lg rounded-xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <h3 class="text-base font-semibold text-gray-800">แก้ไขรายละเอียดบิล</h3>
            <button type="button" onclick="closeEditBillModal()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form method="POST" action="{{ route('pos.stock.bill.update') }}" class="px-5 py-4 space-y-4">
            @csrf
            @method('PATCH')
            <input type="hidden" name="invoice_no" value="{{ $billHeader->invoice_no }}">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">เลขที่เอกสาร</label>
                    <input type="text" value="{{ $billHeader->invoice_no }}" disabled
                        class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-400">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">เลขที่บิลผู้จำหน่าย</label>
                    <input type="text" name="supplier_invoice_no" value="{{ $billHeader->supplier_invoice_no ?? '' }}"
                        class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600">ผู้จำหน่าย <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="hidden" name="supplier_id" id="modal_supplier_id"
                        value="{{ DB::table('suppliers')->where('name', $billHeader->supplier_name)->value('id') }}">
                    <input type="text" id="modal_supplier_search" autocomplete="off"
                        value="{{ $billHeader->supplier_name ?? '' }}" placeholder="พิมพ์ค้นหาผู้จำหน่าย..."
                        class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none">
                    <div id="modal_supplier_dropdown"
                        class="absolute z-50 mt-1 hidden max-h-48 w-full overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg"></div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">วันที่รับสินค้า <span class="text-red-500">*</span></label>
                    <input type="date" name="receive_date"
                        value="{{ $billHeader->created_at ? \Carbon\Carbon::parse($billHeader->created_at)->format('Y-m-d') : '' }}"
                        class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">การชำระเงิน</label>
                    <select name="payment_type" id="modal_payment_type" onchange="toggleModalDueDate()"
                        class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none">
                        <option value="cash" {{ ($billHeader->payment_type ?? 'cash') === 'cash' ? 'selected' : '' }}>เงินสด</option>
                        <option value="credit" {{ ($billHeader->payment_type ?? '') === 'credit' ? 'selected' : '' }}>เครดิต</option>
                    </select>
                </div>
            </div>

            <div id="modal_due_date_row" class="{{ ($billHeader->payment_type ?? '') === 'credit' ? '' : 'hidden' }}">
                <label class="mb-1 block text-xs font-medium text-gray-600">วันครบกำหนดชำระ</label>
                <input type="date" name="due_date"
                    value="{{ isset($billHeader->due_date) && $billHeader->due_date ? \Carbon\Carbon::parse($billHeader->due_date)->format('Y-m-d') : '' }}"
                    class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none">
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_paid" id="modal_is_paid" value="1"
                        {{ ($billHeader->is_paid ?? false) ? 'checked' : '' }}
                        onchange="toggleModalPaidDate()"
                        class="h-4 w-4 rounded border-gray-300 text-emerald-500">
                    ชำระเงินแล้ว
                </label>
                <div id="modal_paid_date_wrap"
                    class="{{ ($billHeader->is_paid ?? false) ? 'flex' : 'hidden' }} items-center gap-2">
                    <label class="text-xs text-gray-500">วันที่ชำระ</label>
                    <input type="date" name="paid_date" id="modal_paid_date"
                        value="{{ isset($billHeader->paid_date) && $billHeader->paid_date ? \Carbon\Carbon::parse($billHeader->paid_date)->format('Y-m-d') : '' }}"
                        class="h-9 rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                <button type="button" onclick="closeEditBillModal()"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">ยกเลิก</button>
                <button type="submit"
                    class="rounded-lg bg-emerald-500 px-4 py-2 text-sm text-white hover:bg-emerald-600">บันทึก</button>
            </div>
        </form>
    </div>
</div>

{{-- Cancel Bill Modal --}}
<div id="cancel-bill-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <h3 class="text-base font-semibold text-red-700">ยืนยันยกเลิกบิล</h3>
            <button type="button" onclick="closeCancelBillModal()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <div class="px-5 py-4">
            <p class="mb-1 text-sm text-gray-700">ยกเลิกบิล <strong>{{ $billHeader->invoice_no }}</strong></p>
            <p class="mb-4 text-xs text-gray-500">สต็อกสินค้าในบิลนี้จะถูกหักคืน หากสินค้าบางส่วนถูกจำหน่ายไปแล้ว ระบบจะหักคืนเฉพาะส่วนที่เหลือ</p>
            <form method="POST" action="{{ route('pos.stock.bill.cancel') }}">
                @csrf
                <input type="hidden" name="invoice_no" value="{{ $billHeader->invoice_no }}">
                <div class="mb-4">
                    <label class="mb-1 block text-xs font-medium text-gray-600">หมายเหตุการยกเลิก</label>
                    <textarea name="cancel_note" rows="2"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-red-400 focus:outline-none"
                        placeholder="ระบุเหตุผล (ถ้ามี)"></textarea>
                </div>
                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button type="button" onclick="closeCancelBillModal()"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">ไม่ยกเลิก</button>
                    <button type="submit"
                        class="rounded-lg bg-red-500 px-4 py-2 text-sm text-white hover:bg-red-600">ยืนยันยกเลิกบิล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const modalSupplierList = @json($suppliers->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values());

function openEditBillModal() {
    const m = document.getElementById('edit-bill-modal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeEditBillModal() {
    const m = document.getElementById('edit-bill-modal');
    m.classList.add('hidden'); m.classList.remove('flex');
}
function openCancelBillModal() {
    const m = document.getElementById('cancel-bill-modal');
    if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
}
function closeCancelBillModal() {
    const m = document.getElementById('cancel-bill-modal');
    if (m) { m.classList.add('hidden'); m.classList.remove('flex'); }
}
function toggleModalDueDate() {
    const isCredit = document.getElementById('modal_payment_type').value === 'credit';
    document.getElementById('modal_due_date_row').classList.toggle('hidden', !isCredit);
}
function toggleModalPaidDate() {
    const isPaid = document.getElementById('modal_is_paid').checked;
    const wrap = document.getElementById('modal_paid_date_wrap');
    wrap.classList.toggle('hidden', !isPaid);
    wrap.classList.toggle('flex', isPaid);
    if (isPaid && !document.getElementById('modal_paid_date').value) {
        document.getElementById('modal_paid_date').value = new Date().toISOString().slice(0, 10);
    }
}

const modalSupplierSearch = document.getElementById('modal_supplier_search');
const modalSupplierHidden = document.getElementById('modal_supplier_id');
const modalSupplierDropdown = document.getElementById('modal_supplier_dropdown');

function renderModalSupplierOptions(query) {
    const search = query.trim().toLowerCase();
    const matches = modalSupplierList.filter(s => !search || s.name.toLowerCase().includes(search)).slice(0, 20);
    modalSupplierDropdown.innerHTML = matches.length
        ? matches.map(s => `<button type="button" class="block w-full border-b border-gray-100 px-3 py-2 text-left text-sm text-gray-700 hover:bg-emerald-50 last:border-b-0" data-id="${s.id}" data-name="${s.name.replace(/"/g, '&quot;')}">${s.name}</button>`).join('')
        : '<div class="px-3 py-2 text-xs text-gray-500">ไม่พบผู้จำหน่าย</div>';
    modalSupplierDropdown.classList.remove('hidden');
}
modalSupplierSearch?.addEventListener('input', () => renderModalSupplierOptions(modalSupplierSearch.value));
modalSupplierSearch?.addEventListener('focus', () => renderModalSupplierOptions(modalSupplierSearch.value));
modalSupplierSearch?.addEventListener('blur', () => setTimeout(() => modalSupplierDropdown.classList.add('hidden'), 150));
modalSupplierDropdown?.addEventListener('click', e => {
    const btn = e.target.closest('[data-id]');
    if (!btn) return;
    modalSupplierHidden.value = btn.dataset.id;
    modalSupplierSearch.value = btn.dataset.name;
    modalSupplierDropdown.classList.add('hidden');
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeEditBillModal(); closeCancelBillModal(); }
});
</script>

@endif

@endsection
