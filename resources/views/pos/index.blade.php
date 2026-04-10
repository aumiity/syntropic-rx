@extends('layouts.app')

@section('content')

<div class="flex flex-col bg-slate-50 text-slate-800 overflow-hidden px-3 py-3" style="height: 100dvh; box-sizing: border-box;">

    {{-- Hygeia Header (temporary mockup) --}}
    <div class="mb-3 px-4 py-2 rounded-xl bg-linear-to-r from-emerald-600 to-sky-600 text-white shadow-md">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold">Syntropic RX</h1>
                <p class="text-sm opacity-90">หน้าจอขายสินค้า Test Version</p>
            </div>
            <div class="text-right text-xs">
                <div>สาขา: กรุณาเลือก</div>
                <div>เวลา: <span id="hygeia-time">--:--:--</span></div>
            </div>
        </div>

    </div>

    {{-- ========================================== --}}
    {{-- MAIN CONTENT: 2-COLUMN POS LAYOUT --}}
    {{-- ========================================== --}}
    <div class="flex-1 flex flex-row gap-4 min-h-0">

        {{-- Left Column: Search + Warning + Cart --}}
        <div class="flex-1 flex flex-col gap-3 min-h-0">

            {{-- Search Input with Dropdown --}}
            <div class="relative z-50">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" id="search-input"
                    placeholder="ค้นหารหัส, ชื่อยา หรือสแกนบาร์โค้ด [F2]..."
                    class="w-full h-14 pl-12 pr-4 rounded-xl border border-slate-300 shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-lg bg-white outline-none transition-all"
                    oninput="searchDrugs(this.value)" autocomplete="off" autofocus>

                {{-- Search Results Popup (Dropdown) --}}
                <div id="search-dropdown" class="absolute top-[110%] left-0 w-full bg-white shadow-2xl rounded-xl border border-slate-200 hidden max-h-100 overflow-y-auto custom-scrollbar">
                    <div id="search-results-container" class="py-2"></div>
                </div>
            </div>

            <div id="allergy-warn" class="hidden bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-600 items-center gap-2 font-bold">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span id="allergy-warn-text">คำเตือน: พบประวัติแพ้ยาในรายการสั่งซื้อ!</span>
            </div>

            {{-- Cart Table --}}
            <div class="flex-1 bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col overflow-hidden relative min-h-0">

               {{-- Table Header (ล็อกขนาด Grid ตายตัว 100%) --}}
                <div class="w-full grid gap-2 px-4 py-3 bg-slate-100 text-slate-600 text-sm font-bold border-b border-slate-200 items-center"
                     style="grid-template-columns: 50px 3fr 1fr 80px 1fr 1fr 1fr 50px;">
                    <div class="text-center">ลำดับ</div>
                    <div>รายการสินค้า (Product)</div>
                    <div class="text-center">หน่วย</div>
                    <div class="text-center">จำนวน</div>
                    <div class="text-center">ราคา/หน่วย</div>
                    <div class="text-center">ส่วนลด</div>
                    <div class="text-center">รวมเงิน</div>
                    <div class="text-center">ลบ</div>
                </div>

                {{-- Table Body (Cart Items) --}}
                <div class="flex-1 overflow-y-auto custom-scrollbar bg-white" id="cart-list">
                    <div id="empty-cart" class="h-full flex flex-col items-center justify-center text-slate-300 gap-3">
                        <svg class="w-16 h-16 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        <p class="text-lg font-medium">ยังไม่มีรายการสั่งซื้อ</p>
                        <p class="text-sm text-slate-400">พิมพ์ค้นหา หรือ สแกนบาร์โค้ด เพื่อเริ่มขาย</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- Right Column: Customer + Totals + Actions --}}
        <div class="w-72 shrink-0 flex flex-col gap-3 min-h-0">
            <div class="flex gap-2">
                <button onclick="changeCustomer()" class="flex-1 h-14 bg-white border border-slate-300 rounded-xl px-4 flex items-center justify-between hover:bg-slate-50 transition-colors shadow-sm">
                    <div class="flex flex-col text-left">
                        <span class="text-xs text-slate-400 font-medium">ลูกค้า / สมาชิก</span>
                        <span class="text-sm font-bold text-emerald-600" id="cust-name">ลูกค้าทั่วไป (เงินสด)</span>
                    </div>
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <button onclick="openQuickAddCustomer()" class="w-14 h-14 bg-white border border-slate-300 rounded-xl flex flex-col items-center justify-center hover:bg-slate-50 shadow-sm text-slate-500" title="เพิ่มลูกค้าใหม่">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="text-[10px] leading-none mt-0.5">เพิ่มลูกค้า</span>
                </button>
            </div>

            <div class="flex-1 flex flex-col gap-3 min-h-0">
                <button id="pay-btn" disabled onclick="handlePay()" class="w-full flex-1 px-6 py-6 rounded-lg bg-emerald-500 hover:bg-emerald-600 disabled:bg-slate-200 disabled:text-slate-400 text-white font-bold text-2xl shadow-md transition-all flex items-center justify-center gap-2">
                    รับชำระเงิน <span class="text-base bg-black/10 px-3 py-1 rounded-md font-medium ml-2">F9</span>
                </button>
                <button class="w-full px-6 py-3 rounded-lg bg-white hover:bg-slate-50 text-slate-600 font-medium border border-slate-300 transition-colors shadow-sm">เปิดลิ้นชัก</button>
                <button onclick="printLabel()" class="w-full px-6 py-3 rounded-lg bg-white hover:bg-slate-50 text-slate-600 font-medium border border-slate-300 transition-colors shadow-sm">พิมพ์ฉลากยา</button>
                <button onclick="holdBill()" class="w-full px-6 py-3 rounded-lg bg-white hover:bg-slate-50 text-slate-600 font-medium border border-slate-300 transition-colors shadow-sm">พักบิล</button>
                <button onclick="showHeldBills()" class="w-full px-6 py-3 rounded-lg bg-white hover:bg-slate-50 text-slate-600 font-medium border border-slate-300 transition-colors shadow-sm">เรียกบิลที่พัก</button>
                <button onclick="clearCart()" class="w-full px-6 py-3 rounded-lg bg-white hover:bg-red-50 text-slate-600 hover:text-red-500 font-medium border border-slate-300 hover:border-red-200 transition-colors flex items-center justify-center gap-2 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    ยกเลิกบิล [F8]
                </button>
            </div>
            <div class="bg-white rounded-xl shadow-sm border-2 border-emerald-50 p-5 flex flex-col relative overflow-hidden">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-slate-500 font-medium">จำนวนรายการ</span>
                    <span id="s-count" class="font-bold text-slate-700 text-lg">0</span>
                </div>
                <div class="flex justify-between items-center mb-4 pb-4 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">ส่วนลดรวม</span>
                    <span id="s-discount" class="font-bold text-emerald-500">0.00</span>
                </div>
                <div class="items-end text-right">
                    <span class="text-xl font-bold text-slate-700">ยอดสุทธิ</span>
                </div>
                <div class="items-end text-right">
                    <span class="text-5xl font-extrabold text-emerald-600" id="tb-total">0.00</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Scrollbar for sleek UI */
.custom-scrollbar::-webkit-scrollbar { width: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
</style>

<style>
/* Custom Scrollbar for sleek UI */
.custom-scrollbar::-webkit-scrollbar { width: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }

/* ซ่อนลูกศรขึ้น/ลง ในช่องพิมพ์ตัวเลขจำนวนสินค้า */
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
    -webkit-appearance: none; 
    margin: 0; 
}
input[type=number] {
    -moz-appearance: textfield;
}
</style>

<!-- Modals -->
<div id="price-modal" class="fixed inset-0 bg-black/40 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-96 shadow-lg">
        <h3 class="text-lg font-bold text-slate-800 mb-1">เปลี่ยนราคา</h3>
        <p id="price-modal-product-name" class="text-sm text-slate-500 mb-4"></p>

        <!-- Quick price buttons -->
        <div class="flex gap-2 mb-4" id="price-quick-buttons">
            <button type="button" id="btn-price-retail" class="flex-1 py-2 rounded-lg border text-sm font-medium transition-colors"></button>
            <button type="button" id="btn-price-wholesale1" class="flex-1 py-2 rounded-lg border text-sm font-medium transition-colors hidden"></button>
            <button type="button" id="btn-price-wholesale2" class="flex-1 py-2 rounded-lg border text-sm font-medium transition-colors hidden"></button>
        </div>

        <!-- Price input -->
        <input type="number" id="price-input" 
            class="w-full text-right border border-slate-300 rounded-lg px-3 py-2 mb-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-lg font-bold" 
            min="0" step="0.01" placeholder="ใส่ราคาใหม่">

        <!-- Cost & Profit display -->
        <div class="bg-slate-50 rounded-lg px-4 py-3 mb-4 text-sm space-y-1">
            <div class="flex justify-between">
                <span class="text-slate-500">ต้นทุนล่าสุด</span>
                <span id="price-modal-cost" class="font-medium text-slate-700">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">ต้นทุนเฉลี่ย (lots)</span>
                <span id="price-modal-avg-cost" class="font-medium text-slate-700">-</span>
            </div>
            <div class="flex justify-between border-t border-slate-200 pt-1 mt-1">
                <span class="text-slate-500">กำไร</span>
                <span id="price-modal-profit" class="font-semibold">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">% กำไร</span>
                <span id="price-modal-profit-pct" class="font-semibold">-</span>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <button id="price-cancel" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition-colors">ยกเลิก</button>
            <button id="price-ok" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">ตกลง</button>
        </div>
    </div>
</div>

<div id="discount-modal" class="fixed inset-0 bg-black/40 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96 shadow-lg">
        <h3 class="text-lg font-bold text-slate-800 mb-4">ใส่ส่วนลด</h3>
        <input type="number" id="discount-input" class="w-full border border-slate-300 rounded px-3 py-2 mb-4 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" min="0" step="0.01" placeholder="ใส่ส่วนลด">
        <div class="flex justify-end gap-2">
            <button id="discount-cancel" class="px-4 py-2 bg-slate-200 text-slate-700 rounded hover:bg-slate-300 transition-colors">ยกเลิก</button>
            <button id="discount-ok" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors">ตกลง</button>
        </div>
    </div>
</div>

<div id="customer-modal" class="fixed inset-0 bg-black/40 bg-opacity-50 hidden items-center justify-center z-70 px-4">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="text-xl font-bold text-slate-800">เลือกลูกค้า</h3>
            <button onclick="closeCustomerModal()" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-4">
            <input type="text" id="cust-search-input"
                oninput="searchCustomers(this.value)"
                placeholder="ค้นหาชื่อ, เบอร์โทร, รหัส HN..."
                class="w-full h-11 px-4 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none text-base mb-3">
            <div class="mb-2">
                <button onclick="selectWalkIn()" class="w-full px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-left transition-colors">
                    👤 ลูกค้าทั่วไป (เงินสด) — ไม่ระบุชื่อ
                </button>
            </div>
            <div id="cust-search-results" class="max-h-72 overflow-y-auto custom-scrollbar rounded-xl border border-slate-100"></div>
        </div>
    </div>
</div>

<!-- Quick Add Customer Modal -->
<div id="quick-add-customer-modal" class="fixed inset-0 bg-black/40 bg-opacity-50 hidden items-center justify-center z-80 px-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="text-xl font-bold text-slate-800">เพิ่มลูกค้าใหม่</h3>
            <button id="quick-add-x-btn" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
        </div>
        <form class="p-6">
            <div class="mb-4">
                <label for="quick-add-full-name" class="block text-sm font-medium text-slate-700 mb-1">ชื่อ-นามสกุล <span class="text-red-500">*</span></label>
                <input type="text" id="quick-add-full-name" name="full_name" required class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="ชื่อ-นามสกุล">
            </div>
            <div class="mb-4">
                <label for="quick-add-phone" class="block text-sm font-medium text-slate-700 mb-1">เบอร์โทรศัพท์</label>
                <input type="text" id="quick-add-phone" name="phone" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="เบอร์โทรศัพท์">
            </div>
            <div class="mb-4">
                <label for="quick-add-gender" class="block text-sm font-medium text-slate-700 mb-1">เพศ</label>
                <select id="quick-add-gender" name="gender" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">ไม่ระบุ</option>
                    <option value="ชาย">ชาย</option>
                    <option value="หญิง">หญิง</option>
                </select>
            </div>
            <div class="mb-6">
                <label for="quick-add-alert-note" class="block text-sm font-medium text-slate-700 mb-1">หมายเหตุ</label>
                <textarea id="quick-add-alert-note" name="alert_note" rows="2" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="ประวัติแพ้ยา ถ้ามี"></textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" id="quick-add-cancel-btn" class="px-5 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold">ยกเลิก</button>
                <button type="submit" id="quick-add-save-btn" class="px-6 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold">บันทึก</button>
            </div>
        </form>
    </div>
</div>

<div id="pay-modal" class="fixed inset-0 bg-black/40 bg-opacity-50 hidden items-center justify-center z-60 px-4">
    <div class="bg-white rounded-2xl w-full max-w-xl shadow-2xl border border-slate-200 p-6">
        <h3 class="text-2xl font-bold text-slate-800 mb-4">ชำระเงิน</h3>

        <div class="bg-emerald-50 rounded-xl px-4 py-4 mb-5 border border-emerald-100">
            <div class="text-sm text-slate-500">ยอดสุทธิ</div>
            <div id="pay-total-display" class="text-4xl font-extrabold text-emerald-600 leading-tight">0.00</div>
        </div>

        <div id="pay-type-radio" class="mb-4">
            <label class="inline-flex items-center gap-2 mr-4 cursor-pointer">
                <input type="radio" name="pay-type" value="cash" checked class="text-emerald-600 focus:ring-emerald-500">
                <span class="font-medium text-slate-700">เงินสด</span>
            </label>
            <label class="inline-flex items-center gap-2 mr-4 cursor-pointer">
                <input type="radio" name="pay-type" value="transfer" class="text-emerald-600 focus:ring-emerald-500">
                <span class="font-medium text-slate-700">โอนเงิน</span>
            </label>
        </div>

        <div id="pay-received-wrap" class="mb-3">
            <div class="text-sm text-slate-500 mb-1">รับเงินมา</div>
            <input type="number" id="pay-received" min="0" step="0.01" class="text-4xl font-extrabold text-slate-800 w-full bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-4 text-right outline-none focus:ring-2 focus:ring-emerald-400" placeholder="0.00">
        </div>

        <div class="mb-5">
            <div class="text-sm text-slate-500 mb-1">เงินทอน</div>
            <input type="text" id="pay-change" readonly value="0.00" class="text-4xl font-extrabold w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-4 text-right text-emerald-600">
        </div>

        <div class="flex gap-2 justify-between">
            <button id="pay-cancel-btn" class="px-5 py-3 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold">ยกเลิก</button>
            <button id="pay-confirm-btn" class="px-6 py-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-lg">ยืนยันชำระเงิน</button>
        </div>
    </div>
</div>

<script>
// --- State ---
let cart = {}, allProducts = [], heldBills = JSON.parse(localStorage.getItem('held_bills') || '[]');
let grandTotal = 0;
let scanLock = false;

// --- Quick Add Customer Submit Logic ---
async function submitQuickAddCustomer() {
    const modal = document.getElementById('quick-add-customer-modal');
    const full_name = modal.querySelector('#quick-add-full-name').value.trim();
    const phone = modal.querySelector('#quick-add-phone').value.trim();
    const gender = modal.querySelector('#quick-add-gender').value;
    const alert_note = modal.querySelector('#quick-add-alert-note').value.trim();

    try {
        const res = await fetch("{{ route('pos.customers.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
            },
            body: JSON.stringify({ full_name, phone, gender, alert_note })
        });
        const data = await res.json();
        if (data.success === true) {
            selectCustomer(data.customer.id, data.customer.full_name, data.customer.is_alert, data.customer.alert_note);
            closeQuickAddCustomer();
            closeCustomerModal();
            showToast('เพิ่มลูกค้าใหม่เรียบร้อยแล้ว', 'success');
        } else {
            showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาด', 'error');
    }
}

// --- Quick Add Customer Modal Logic ---
function openQuickAddCustomer() {
    const modal = document.getElementById('quick-add-customer-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    // Clear all form fields
    modal.querySelector('form').reset();
}

function closeQuickAddCustomer() {
    const modal = document.getElementById('quick-add-customer-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Wire up buttons after DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('quick-add-customer-btn')?.addEventListener('click', openQuickAddCustomer);
    document.getElementById('quick-add-cancel-btn')?.addEventListener('click', closeQuickAddCustomer);
    document.getElementById('quick-add-x-btn')?.addEventListener('click', closeQuickAddCustomer);
    // Wire save button if route exists
    const saveBtn = document.getElementById('quick-add-save-btn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            submitQuickAddCustomer();
        });
    }
});


// --- Search & Popup Logic ---
let timer;
const searchInput = document.getElementById('search-input');
const searchDropdown = document.getElementById('search-dropdown');
const searchContainer = document.getElementById('search-results-container');
let searchSelectedIndex = -1;

document.addEventListener('click', function(event) {
    if (!searchInput.contains(event.target) && !searchDropdown.contains(event.target)) {
        searchDropdown.classList.add('hidden');
    }
});

// --- Keyboard navigation for search autocomplete ---
searchInput.addEventListener('keydown', function(e) {
    const items = searchContainer.querySelectorAll(':scope > div');
    const max = items.length;
    if (!max) return;

    function updateSelection(newIndex) {
        searchSelectedIndex = newIndex;
        items.forEach((el, idx) => {
            if (idx === searchSelectedIndex) {
                el.classList.add('bg-emerald-100');
                el.scrollIntoView({ block: 'nearest' });
            } else {
                el.classList.remove('bg-emerald-100');
            }
        });
    }

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        let next = searchSelectedIndex;
        do {
            next = (next + 1) % max;
        } while (items[next].classList.contains('cursor-not-allowed') && next !== searchSelectedIndex);
        updateSelection(next);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        let prev = searchSelectedIndex;
        do {
            prev = (prev - 1 + max) % max;
        } while (items[prev].classList.contains('cursor-not-allowed') && prev !== searchSelectedIndex);
        updateSelection(prev);
    } else if (e.key === 'Enter') {
        if (searchSelectedIndex >= 0) {
            const selected = items[searchSelectedIndex];
            if (!selected.classList.contains('cursor-not-allowed')) {
                // Extract product id from onclick="addToCart('ID')"
                const onclick = selected.getAttribute('onclick');
                const match = onclick && onclick.match(/addToCart\('(.+?)'\)/);
                if (match) {
                    addToCart(match[1]);
                }
            }
        } else if (max === 1) {
            const only = items[0];
            if (!only.classList.contains('cursor-not-allowed')) {
                const onclick = only.getAttribute('onclick');
                const match = onclick && onclick.match(/addToCart\('(.+?)'\)/);
                if (match) {
                    addToCart(match[1]);
                }
            }
        }
    } else if (e.key === 'Escape') {
        searchDropdown.classList.add('hidden');
        searchInput.value = '';
    }
});

const thaiToNum = {'ๅ':'1','/':'2','_':'3','-':'3','ภ':'4','ถ':'5','ุ':'6','ึ':'7','ค':'8','ต':'9','จ':'0'};

function convertThaiBarcode(str) {
    const mappable = (c) => thaiToNum[c] !== undefined;
    // Check if string has 4+ consecutive mappable characters
    let consecutive = 0;
    for (const c of str) {
        if (mappable(c)) {
            consecutive++;
            if (consecutive >= 4) {
                // Convert all mappable chars, keep others as-is
                return [...str].map(c => thaiToNum[c] ?? c).join('');
            }
        } else {
            consecutive = 0;
        }
    }
    return str;
}

function searchDrugs(q) {
    clearTimeout(timer);
    if (!q.trim()) {
        searchDropdown.classList.add('hidden');
        return;
    }

    q = convertThaiBarcode(q);

    timer = setTimeout(async () => {
        try {
            const res = await fetch(`/pos/search?q=${encodeURIComponent(q)}`);
            allProducts = await res.json();
            renderSearchResults(allProducts);

            if (allProducts.length === 1) {
                const product = allProducts[0];
                addToCart(product.id);
            }
        } catch (e) {
            console.error('Search failed:', e);
        }
    }, 200);
}

function renderSearchResults(products) {
    searchSelectedIndex = -1;
    searchDropdown.classList.remove('hidden');

    if (!products.length) {
        searchContainer.innerHTML = '<div class="p-4 text-center text-slate-500">ไม่พบข้อมูลยา หรือ บาร์โค้ดนี้</div>';
        return;
    }

    searchContainer.innerHTML = products.map(p => {
        const stock = p.lots ? p.lots.reduce((s,l) => s+l.qty_on_hand, 0) : 0;
        const price = parseFloat(p.price_retail);
        const out = false;

        return `
        <div onclick="addToCart('${p.id}')" 
             class="px-4 py-3 border-b border-slate-100 last:border-0 flex justify-between items-center hover:bg-emerald-50 cursor-pointer">
            <div class="flex-1">
                <div class="font-bold text-slate-800 text-lg flex items-center gap-2">
                    ${p.trade_name}
                    ${stock === 0 ? '<span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-md">สินค้าหมด</span>' : ''}
                </div>
                <div class="text-sm text-slate-500 mt-0.5">สต็อกคงเหลือ: <span class="${stock > 0 ? 'text-emerald-600 font-semibold' : 'text-red-500'}">${stock}</span> | รหัส: ${p.id}</div>
            </div>
            <div class="text-right">
                <div class="text-emerald-600 font-extrabold text-lg">${price.toLocaleString()}</div>
            </div>
        </div>`;
    }).join('');
}

// --- Cart Actions ---
function addToCart(id) {
    if (scanLock) return;
    scanLock = true;
    setTimeout(() => { scanLock = false; }, 500);

    const p = allProducts.find(product => product.id == id);
    if (!p) return;

    if(cart[id]) {
        cart[id].qty = parseInt(cart[id].qty) + 1;
    } else {
        cart[id] = {product: p, qty: 1};
    }

    searchInput.value = '';
    searchDropdown.classList.add('hidden');
    searchInput.focus();

    updateAll();
}

// 🛠️ ปรับปรุง changeQty ใหม่ บังคับเป็นตัวเลขทั้งหมด
window.changeQty = function(id, d) {
    if (!cart[id]) return;
    
    // บังคับให้จำนวนปัจจุบันและค่าที่ส่งมาบวกลบ เป็นตัวเลข (Integer) ป้องกันบั๊ก String ต่อกัน
    const currentQty = parseInt(cart[id].qty) || 0;
    const diff = parseInt(d) || 0;
    
    cart[id].qty = currentQty + diff;
    
    if (cart[id].qty <= 0) {
        delete cart[id];
    }
    
    updateAll();
};

window.setQty = function(id, value) {
    if (!cart[id]) return;
    const newQty = parseInt(value);
    
    if (isNaN(newQty) || newQty <= 0) {
        delete cart[id];
    } else {
        cart[id].qty = newQty;
    }
    updateAll();
};

window.changePrice = function(id) {
    if (!cart[id]) return;
    const p = cart[id].product;
    const currentPrice = cart[id].customPrice ?? parseFloat(p.price_retail);

    // Set product name
    document.getElementById('price-modal-product-name').textContent = p.trade_name;

    // Set quick buttons
    const btnRetail = document.getElementById('btn-price-retail');
    const btnWholesale1 = document.getElementById('btn-price-wholesale1');
    const btnWholesale2 = document.getElementById('btn-price-wholesale2');

    btnRetail.textContent = 'ปลีก ' + parseFloat(p.price_retail).toLocaleString() + ' บาท';
    btnRetail.onclick = () => { document.getElementById('price-input').value = parseFloat(p.price_retail); updatePriceModalStats(); };

    if (p.price_wholesale1 && parseFloat(p.price_wholesale1) > 0) {
        btnWholesale1.textContent = 'ส่ง 1 ' + parseFloat(p.price_wholesale1).toLocaleString() + ' บาท';
        btnWholesale1.classList.remove('hidden');
        btnWholesale1.onclick = () => { document.getElementById('price-input').value = parseFloat(p.price_wholesale1); updatePriceModalStats(); };
    } else {
        btnWholesale1.classList.add('hidden');
    }

    if (p.price_wholesale2 && parseFloat(p.price_wholesale2) > 0) {
        btnWholesale2.textContent = 'ส่ง 2 ' + parseFloat(p.price_wholesale2).toLocaleString() + ' บาท';
        btnWholesale2.classList.remove('hidden');
        btnWholesale2.onclick = () => { document.getElementById('price-input').value = parseFloat(p.price_wholesale2); updatePriceModalStats(); };
    } else {
        btnWholesale2.classList.add('hidden');
    }

    // Cost price
    const costPrice = parseFloat(p.cost_price) || 0;
    document.getElementById('price-modal-cost').textContent = costPrice > 0 ? costPrice.toLocaleString() + ' บาท' : '-';

    // Average cost from lots
    const avgCost = p.lots && p.lots.length > 0
        ? p.lots.reduce((sum, l) => sum + (parseFloat(l.cost_price) * l.qty_on_hand), 0) / p.lots.reduce((sum, l) => sum + l.qty_on_hand, 0)
        : 0;
    document.getElementById('price-modal-avg-cost').textContent = avgCost > 0 ? avgCost.toFixed(2) + ' บาท' : '-';

    window.currentPriceId = id;
    window.priceModalAvgCost = avgCost > 0 ? avgCost : costPrice;

    document.getElementById('price-input').value = currentPrice;
    updatePriceModalStats();

    document.getElementById('price-modal').classList.remove('hidden');
    document.getElementById('price-modal').classList.add('flex');
    document.getElementById('price-input').focus();
    document.getElementById('price-input').select();
};

function updatePriceModalStats() {
    const price = parseFloat(document.getElementById('price-input').value) || 0;
    const cost = window.priceModalAvgCost || 0;
    const profit = price - cost;
    const profitPct = cost > 0 ? (profit / cost * 100) : 0;

    const profitEl = document.getElementById('price-modal-profit');
    const profitPctEl = document.getElementById('price-modal-profit-pct');

    profitEl.textContent = profit.toFixed(2) + ' บาท';
    profitEl.className = 'font-semibold ' + (profit >= 0 ? 'text-emerald-600' : 'text-red-500');

    profitPctEl.textContent = profitPct.toFixed(1) + ' %';
    profitPctEl.className = 'font-semibold ' + (profitPct >= 0 ? 'text-emerald-600' : 'text-red-500');
}

window.changeDiscount = function(id) {
    if (!cart[id]) return;
    const currentDiscount = cart[id].discount || 0;
    document.getElementById('discount-input').value = currentDiscount;
    document.getElementById('discount-modal').classList.remove('hidden');
    document.getElementById('discount-modal').classList.add('flex');
    window.currentDiscountId = id;
};

// Modal event listeners
document.getElementById('price-ok').addEventListener('click', function() {
    const newPrice = parseFloat(document.getElementById('price-input').value);
    if (!isNaN(newPrice) && newPrice >= 0) {
        cart[window.currentPriceId].customPrice = newPrice;
        updateAll();
    }
    document.getElementById('price-modal').classList.add('hidden');
    document.getElementById('price-modal').classList.remove('flex');
});

document.getElementById('price-cancel').addEventListener('click', function() {
    document.getElementById('price-modal').classList.add('hidden');
    document.getElementById('price-modal').classList.remove('flex');
});

document.getElementById('price-input').addEventListener('input', updatePriceModalStats);

document.getElementById('discount-ok').addEventListener('click', function() {
    const newDiscount = parseFloat(document.getElementById('discount-input').value);
    if (!isNaN(newDiscount) && newDiscount >= 0) {
        cart[window.currentDiscountId].discount = newDiscount;
        updateAll();
    }
    document.getElementById('discount-modal').classList.add('hidden');
    document.getElementById('discount-modal').classList.remove('flex');
});

document.getElementById('discount-cancel').addEventListener('click', function() {
    document.getElementById('discount-modal').classList.add('hidden');
    document.getElementById('discount-modal').classList.remove('flex');
});

window.removeItem = function(id) {
    delete cart[id];
    updateAll();
};

function clearCart() {
    if(Object.keys(cart).length === 0) return;
    if(confirm('ต้องการยกเลิกบิลนี้ใช่หรือไม่?')) {
        cart = {};
        searchInput.value = '';
        searchDropdown.classList.add('hidden');
        updateAll();
        searchInput.focus();
    }
}

// --- Render Main Table ---
// --- Render Main Table ---
function updateAll() {
    const list = document.getElementById('cart-list');
    const emptyState = document.getElementById('empty-cart'); // โค้ดที่ Copilot สร้างอาจจะไม่มีตัวนี้
    const keys = Object.keys(cart);

    grandTotal = 0;

    if (!keys.length) {
        list.innerHTML = '';
        // เช็คว่ามีกล่อง empty-cart ไหม ถ้ามีค่อยแสดง ถ้าไม่มีก็สร้างข้อความใส่ลงไปเลย
        if (emptyState) {
            list.appendChild(emptyState);
            emptyState.classList.remove('hidden');
            emptyState.classList.add('flex');
        } else {
            list.innerHTML = `<div class="flex flex-col items-center justify-center h-full text-slate-300 gap-3 py-10">
                <p class="text-xl font-medium">ยังไม่มีรายการสั่งซื้อ</p>
                <p class="text-sm text-slate-400">พิมพ์ค้นหา หรือ สแกนบาร์โค้ด เพื่อเริ่มขาย</p>
            </div>`;
        }
    } else {
        // เช็คว่ามีกล่อง empty-cart ไหม ถ้ามีค่อยซ่อน
        if (emptyState) {
            emptyState.classList.add('hidden');
            emptyState.classList.remove('flex');
        }

        list.innerHTML = keys.map((id, index) => {
            const item = cart[id];
            const p = item.product;
            const price = parseFloat(p.price_retail);
            const effectivePrice = item.customPrice ?? price;
            const discount = item.discount ?? 0;
            const lineTotal = (effectivePrice * item.qty) - discount;
            grandTotal += lineTotal;

           return `
            <div class="w-full grid gap-2 px-4 py-3 border-b border-slate-100 items-center hover:bg-slate-50 transition-colors" 
                 style="grid-template-columns: 50px 3fr 1fr 80px 1fr 1fr 1fr 50px;">
                 
                <div class="text-center text-slate-500 font-medium">${index + 1}</div>
                
                <div>
                    <div class="font-bold text-slate-800 text-base line-clamp-1">${p.trade_name}</div>
                </div>
                
                <div class="text-center text-slate-500 font-medium">${p.unit ? p.unit.name : (p.unit_name ?? '-')}</div>
                
                <div class="flex justify-center">
                    <div class="flex items-center bg-white border border-slate-300 rounded-lg overflow-hidden shadow-sm focus-within:ring-2 focus-within:ring-emerald-500 w-max max-w-[120px]">
                        <button type="button" data-action="changeQty" data-id="${p.id}" data-delta="-1" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors font-bold text-lg">-</button>
                        <input type="number" min="1" value="${item.qty}" onchange="setQty('${p.id}', this.value)" class="w-8 text-center font-bold text-slate-800 border-x border-slate-200 h-8 outline-none bg-white flex-1">
                        <button type="button" data-action="changeQty" data-id="${p.id}" data-delta="1" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors font-bold text-lg">+</button>
                    </div>
                </div>
                
                <div class="text-center">
                    <div onclick="changePrice('${p.id}')" class="cursor-pointer text-slate-700 font-medium hover:bg-slate-100 px-2 py-1 rounded transition-colors inline-block" title="คลิกเพื่อแก้ราคา">${effectivePrice.toLocaleString('th', {minimumFractionDigits: 2})}</div>
                </div>
                
                <div class="text-center">
                    <div onclick="changeDiscount('${p.id}')" class="cursor-pointer text-slate-500 font-medium hover:bg-slate-100 px-2 py-1 rounded transition-colors inline-block" title="คลิกเพื่อใส่ส่วนลด">${discount > 0 ? '-' + discount.toLocaleString('th', {minimumFractionDigits: 2}) : '-'}</div>
                </div>
                
                <div class="text-center font-bold text-emerald-600 text-lg">${lineTotal.toLocaleString('th', {minimumFractionDigits: 2})}</div>
                
                <div class="flex justify-center">
                    <button type="button" data-action="removeItem" data-id="${p.id}" class="w-8 h-8 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </div>`;
        }).join('');
    }

    // ใส่ if ดักเผื่อ Copilot ตั้งชื่อ ID ไม่ตรงด้วย
    const tbTotal = document.getElementById('tb-total');
    if (tbTotal) tbTotal.textContent = grandTotal.toLocaleString('th', {minimumFractionDigits: 2});
    
    const sCount = document.getElementById('s-count');
    if (sCount) sCount.textContent = keys.length;

    const sDiscount = document.getElementById('s-discount');
    if (sDiscount) {
        const totalDiscount = Object.values(cart).reduce((sum, item) => sum + (item.discount ?? 0), 0);
        sDiscount.textContent = totalDiscount.toLocaleString('th', {minimumFractionDigits: 2});
    }

    const payBtn = document.getElementById('pay-btn');
    if (payBtn) payBtn.disabled = !keys.length;
}

// --- Event Listeners ---
const cartList = document.getElementById('cart-list');
cartList.addEventListener('click', function(event) {
    const btn = event.target.closest('button[data-action]');
    if (btn) {
        const action = btn.dataset.action;
        const id = btn.dataset.id;

        if (action === 'changeQty') {
            const delta = Number(btn.dataset.delta || 0);
            changeQty(id, delta);
            return;
        }

        if (action === 'removeItem') {
            removeItem(id);
            return;
        }
    }
});

// --- General Actions ---
const CUSTOMER_SEARCH_URL = '{{ route("pos.customers.search") }}';
let currentCustomerId = null;
let currentCustomerName = 'ลูกค้าทั่วไป (เงินสด)';

function syncHeldBillsStorage() {
    localStorage.setItem('held_bills', JSON.stringify(heldBills));
}

function calculateCartTotal(cartData = {}) {
    return Object.values(cartData).reduce((sum, item) => {
        const price = parseFloat(item?.product?.price_retail) || 0;
        const effectivePrice = item.customPrice ?? price;
        const discount = item.discount ?? 0;
        return sum + ((effectivePrice * item.qty) - discount);
    }, 0);
}

window.holdBill = function() {
    if (!Object.keys(cart).length) {
        showToast('ยังไม่มีรายการสำหรับพักบิล', 'error');
        return;
    }

    heldBills.unshift({
        id: Date.now(),
        customerId: currentCustomerId,
        customerName: currentCustomerName,
        cart: JSON.parse(JSON.stringify(cart)),
        createdAt: new Date().toISOString()
    });

    heldBills = heldBills.slice(0, 20);
    syncHeldBillsStorage();
    cart = {};
    selectWalkIn();
    updateAll();
    searchInput.focus();
    showToast('พักบิลเรียบร้อยแล้ว', 'success');
};

window.showHeldBills = function() {
    if (!heldBills.length) {
        showToast('ยังไม่มีบิลที่พักไว้', 'error');
        return;
    }

    const choices = heldBills.map((bill, index) => {
        const total = calculateCartTotal(bill.cart || {});
        const time = bill.createdAt
            ? new Date(bill.createdAt).toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' })
            : '--:--';

        return `${index + 1}. ${bill.customerName || 'ลูกค้าทั่วไป (เงินสด)'} • ฿ ${total.toLocaleString('th', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} • ${time}`;
    }).join('\n');

    const selected = window.prompt(`เลือกบิลที่พักไว้ (ใส่หมายเลข)\n\n${choices}`, '1');
    if (selected === null) return;

    const selectedIndex = Number.parseInt(selected, 10) - 1;
    const bill = heldBills[selectedIndex];

    if (!bill) {
        showToast('ไม่พบบิลที่เลือก', 'error');
        return;
    }

    cart = bill.cart || {};
    currentCustomerId = bill.customerId ?? null;
    currentCustomerName = bill.customerName || 'ลูกค้าทั่วไป (เงินสด)';
    document.getElementById('cust-name').textContent = currentCustomerName;

    const warningBox = document.getElementById('allergy-warn');
    warningBox.classList.add('hidden');
    warningBox.classList.remove('flex');

    heldBills.splice(selectedIndex, 1);
    syncHeldBillsStorage();
    updateAll();
    searchInput.focus();
    showToast('เรียกบิลที่พักเรียบร้อยแล้ว', 'success');
};

window.printLabel = function() {
    showToast('ฟังก์ชันพิมพ์ฉลากในหน้านี้ยังไม่พร้อมใช้งาน', 'error');
};

function changeCustomer() {
    document.getElementById('cust-search-input').value = '';
    document.getElementById('cust-search-results').innerHTML = '';
    document.getElementById('customer-modal').classList.remove('hidden');
    document.getElementById('customer-modal').classList.add('flex');
    setTimeout(() => document.getElementById('cust-search-input').focus(), 100);
}

function selectCustomer(id, name, isAlert, alertNote) {
    currentCustomerId = id;
    currentCustomerName = name;
    document.getElementById('cust-name').textContent = name;

    const warningBox = document.getElementById('allergy-warn');
    const warningText = document.getElementById('allergy-warn-text');
    const hasAlertNote = Boolean(isAlert) && String(alertNote || '').trim() !== '';

    if (hasAlertNote) {
        warningText.textContent = `คำเตือน: ${alertNote}`;
        warningBox.classList.remove('hidden');
        warningBox.classList.add('flex');
    } else {
        warningText.textContent = 'คำเตือน: พบประวัติแพ้ยาในรายการสั่งซื้อ!';
        warningBox.classList.add('hidden');
        warningBox.classList.remove('flex');
    }

    closeCustomerModal();
}

function selectWalkIn() {
    currentCustomerId = null;
    currentCustomerName = 'ลูกค้าทั่วไป (เงินสด)';
    document.getElementById('cust-name').textContent = currentCustomerName;

    const warningBox = document.getElementById('allergy-warn');
    const warningText = document.getElementById('allergy-warn-text');
    warningText.textContent = 'คำเตือน: พบประวัติแพ้ยาในรายการสั่งซื้อ!';
    warningBox.classList.add('hidden');
    warningBox.classList.remove('flex');

    closeCustomerModal();
}

function closeCustomerModal() {
    document.getElementById('customer-modal').classList.add('hidden');
    document.getElementById('customer-modal').classList.remove('flex');
}

let custSearchTimer;
function searchCustomers(q) {
    clearTimeout(custSearchTimer);
    const resultsEl = document.getElementById('cust-search-results');
    if (!q.trim()) {
        resultsEl.innerHTML = '';
        return;
    }
    custSearchTimer = setTimeout(async () => {
        const res = await fetch(`${CUSTOMER_SEARCH_URL}?q=${encodeURIComponent(q)}`);
        const customers = await res.json();
        if (!customers.length) {
            resultsEl.innerHTML = '<div class="p-4 text-center text-slate-400">ไม่พบลูกค้า</div>';
            return;
        }
        resultsEl.innerHTML = customers.map(c => {
            const safeName = JSON.stringify(c.full_name || '');
            const safeAlertNote = JSON.stringify(c.alert_note || '');

            return `
              <div onclick='selectCustomer(${c.id}, ${safeName}, ${c.is_alert ? 'true' : 'false'}, ${safeAlertNote})'
                 class="px-4 py-3 border-b border-slate-100 hover:bg-emerald-50 cursor-pointer flex justify-between items-center">
                <div>
                    <div class="font-semibold text-slate-800">${c.full_name} ${c.is_alert ? '<span class="text-xs bg-red-100 text-red-600 px-1.5 py-0.5 rounded ml-1">⚠ แจ้งเตือน</span>' : ''}</div>
                    <div class="text-xs text-slate-400">${c.code || ''} ${c.phone ? '· ' + c.phone : ''}</div>
                </div>
            </div>`;
        }).join('');
    }, 200);
}

function getGrandTotal() {
    return Object.keys(cart).reduce((sum, id) => {
        const item = cart[id];
        const price = parseFloat(item.product.price_retail) || 0;
        const effectivePrice = item.customPrice ?? price;
        const discount = item.discount ?? 0;
        return sum + ((effectivePrice * item.qty) - discount);
    }, 0);
}

function hidePayModal() {
    const modal = document.getElementById('pay-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function updatePayChange() {
    const changeEl = document.getElementById('pay-change');
    const receivedEl = document.getElementById('pay-received');
    const typeEl = document.querySelector('input[name="pay-type"]:checked');
    if (!changeEl || !receivedEl) return;

    const total = getGrandTotal();
    let change = 0;

    if (typeEl && typeEl.value === 'cash') {
        const received = parseFloat(receivedEl.value) || 0;
        change = received - total;
    }

    changeEl.value = `฿ ${change.toLocaleString('th', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    changeEl.classList.toggle('text-red-600', change < 0);
    changeEl.classList.toggle('text-emerald-600', change >= 0);
}

function handlePay() {
    if (!Object.keys(cart).length) return;

    const total = getGrandTotal();
    const totalDisplay = document.getElementById('pay-total-display');
    const payReceived = document.getElementById('pay-received');
    const cashRadio = document.querySelector('input[name="pay-type"][value="cash"]');
    const receivedWrap = document.getElementById('pay-received-wrap');
    const modal = document.getElementById('pay-modal');

    if (!modal || !totalDisplay || !payReceived || !cashRadio || !receivedWrap) return;

    totalDisplay.textContent = `฿ ${total.toLocaleString('th', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    payReceived.value = '';
    cashRadio.checked = true;
    receivedWrap.classList.remove('hidden');
    updatePayChange();

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    payReceived.focus();
}

async function submitBill() {
    const csrfToken = '{{ csrf_token() }}';
    const payType = document.querySelector('input[name="pay-type"]:checked').value;
    const totalAmount = getGrandTotal();
    const received = parseFloat(document.getElementById('pay-received').value) || 0;

    const items = Object.values(cart).map(item => ({
        product_id: item.product.id,
        item_name: item.product.trade_name,
        unit_name: item.product.unit ? item.product.unit.name : item.product.unit_name,
        qty: item.qty,
        unit_price: item.customPrice ?? parseFloat(item.product.price_retail),
        discount: item.discount ?? 0,
        line_total: ((item.customPrice ?? parseFloat(item.product.price_retail)) * item.qty) - (item.discount ?? 0)
    }));

    const payload = {
        customer_id: currentCustomerId || null,
        subtotal: totalAmount,
        total_discount: Object.values(cart).reduce((s, i) => s + (i.discount || 0), 0),
        total_amount: totalAmount,
        cash_amount: payType === 'cash' ? received : 0,
        transfer_amount: payType === 'transfer' ? totalAmount : 0,
        card_amount: payType === 'card' ? totalAmount : 0,
        change_amount: payType === 'cash' ? Math.max(0, received - totalAmount) : 0,
        items: items

    };

    document.getElementById('pay-confirm-btn').disabled = true;

    try {
        const res = await fetch('{{ route("pos.bill.save") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
            showToast('บันทึกบิล ' + data.invoice_no + ' เรียบร้อยแล้ว', 'success');
            cart = {};
            updateAll();
            document.getElementById('pay-modal').classList.add('hidden');
            document.getElementById('pay-modal').classList.remove('flex');
        } else {
            showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
        }
    } catch(e) {
        showToast('ไม่สามารถเชื่อมต่อได้', 'error');
    } finally {
        document.getElementById('pay-confirm-btn').disabled = false;
    }
}

document.getElementById('pay-type-radio')?.addEventListener('change', function(event) {
    const target = event.target;
    if (!(target instanceof HTMLInputElement) || target.name !== 'pay-type') return;

    const receivedWrap = document.getElementById('pay-received-wrap');
    if (!receivedWrap) return;

    if (target.value === 'cash') {
        receivedWrap.classList.remove('hidden');
    } else {
        receivedWrap.classList.add('hidden');
    }

    updatePayChange();
});

document.getElementById('pay-received')?.addEventListener('input', updatePayChange);
document.getElementById('pay-received')?.addEventListener('change', updatePayChange);
document.getElementById('pay-received')?.addEventListener('keydown', function(event) {
    const modal = document.getElementById('pay-modal');
    if (event.key === 'Enter' && modal && !modal.classList.contains('hidden')) {
        event.preventDefault();
        submitBill();
    }
});

document.getElementById('pay-confirm-btn')?.addEventListener('click', function() {
    submitBill();
});

document.getElementById('pay-cancel-btn')?.addEventListener('click', hidePayModal);

// --- Keyboard Shortcuts ---
document.addEventListener('keydown', e => {
    if(e.key === 'F9') { e.preventDefault(); handlePay(); }
    if(e.key === 'F8') { e.preventDefault(); clearCart(); }
    if(e.key === 'F2') { e.preventDefault(); searchInput.focus(); }
    if (e.key === 'Escape') {
        const payModal = document.getElementById('pay-modal');
        if (payModal && !payModal.classList.contains('hidden')) {
            e.preventDefault();
            hidePayModal();
        }
    }
});

// --- Hygeia-style helper: realtime clock ---
function updateHygeiaTime() {
    const el = document.getElementById('hygeia-time');
    if(el) {
        const now = new Date();
        el.textContent = now.toLocaleTimeString('th-TH', { hour12: false });
    }
}
setInterval(updateHygeiaTime, 1000);
updateHygeiaTime();
</script>

@endsection
