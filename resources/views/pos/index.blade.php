@extends('layouts.app')

@section('content')

<div class="flex flex-col bg-slate-50 text-slate-800 overflow-hidden px-3 py-3 gap-3" style="height: 100dvh; box-sizing: border-box;">

    {{-- TOP SECTION: Header & Search (Left) + Grand Total (Right) --}}
    <div class="flex flex-row gap-4 shrink-0">
        
        {{-- Top Left --}}
        <div class="flex-1 flex flex-col gap-3">
            {{-- Hygeia Header (temporary mockup) --}}
            <div class="px-4 py-2 rounded-xl bg-linear-to-r from-emerald-600 to-sky-600 text-white shadow-md flex-1 flex flex-col justify-center">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-extrabold">Syntropic RX</h1>
                        <p class="text-sm opacity-90">หน้าจอขายสินค้า Test Version</p>
                    </div>
                    <div class="text-right text-xs">
                        <div>สาขา: กรุณาเลือก</div>
                        <div>วันที่: <span id="hygeia-date"></span> เวลา: <span id="hygeia-time">--:--:--</span></div>
                    </div>
                </div>
            </div>

            {{-- Search Input with Dropdown --}}
            <div class="flex gap-2 relative z-50 shrink-0">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" id="search-input"
                        placeholder="ค้นหารหัส, ชื่อยา หรือสแกนบาร์โค้ด [F2]..."
                        class="w-full h-14 pl-12 pr-4 rounded-xl border border-slate-300 shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-lg bg-white outline-none transition-all"
                        oninput="searchDrugs(this.value)" autocomplete="off" autofocus>
                </div>

                {{-- Sale Type Toggle --}}
                <div class="flex rounded-xl overflow-hidden border border-slate-300 shadow-sm shrink-0">
                    <button id="btn-sale-retail" onclick="setSaleType('retail')"
                        class="h-14 px-4 font-bold text-sm transition-colors bg-emerald-500 text-white">
                        ปลีก
                    </button>
                    <button id="btn-sale-wholesale" onclick="setSaleType('wholesale')"
                        class="h-14 px-4 font-bold text-sm transition-colors bg-white text-slate-500">
                        ส่ง
                    </button>
                </div>

                {{-- Customer Select --}}
                <button onclick="changeCustomer()" class="h-14 bg-white border border-slate-300 rounded-xl px-4 w-72 flex items-center justify-between hover:bg-slate-50 transition-colors shadow-sm shrink-0 truncate">
                    <div class="flex flex-col text-left pr-2 overflow-hidden">
                        <span class="text-xs text-slate-400 font-medium">ลูกค้า / สมาชิก</span>
                        <span class="text-sm font-bold text-emerald-600 truncate" id="cust-name">ลูกค้าทั่วไป (เงินสด)</span>
                    </div>
                    <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                {{-- Customer Info Button --}}
                <button id="btn-customer-info" onclick="openCustomerInfo()" disabled
                    class="w-14 h-14 bg-white border border-slate-300 rounded-xl flex flex-col items-center justify-center shadow-sm text-slate-300 shrink-0 disabled:cursor-not-allowed" title="ข้อมูลลูกค้า">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="text-[10px] leading-none mt-0.5">ข้อมูล</span>
                </button>

                {{-- Add Customer Button --}}
                <button onclick="openQuickAddCustomer()" class="w-14 h-14 bg-white border border-slate-300 rounded-xl flex flex-col items-center justify-center hover:bg-slate-50 shadow-sm text-slate-500 shrink-0" title="เพิ่มลูกค้าใหม่">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="text-[10px] leading-none mt-0.5">เพิ่มลูกค้า</span>
                </button>
            </div>
        </div>

        {{-- Top Right: Grand Total Box --}}
        <div class="w-72 bg-white rounded-xl shadow-sm border-2 border-emerald-50 p-5 flex flex-col justify-center relative overflow-hidden shrink-0">
            <div class="text-right mb-1">
                <span class="text-xl font-bold text-slate-700">ยอดสุทธิ</span>
            </div>
            <div class="text-right mb-1">
                <span class="text-5xl font-extrabold text-emerald-600" id="tb-total">0.00</span>
            </div>
        </div>

    </div>

    {{-- BOTTOM SECTION: Cart (Left) + Actions/Summary (Right) --}}
    <div class="flex-1 flex flex-row gap-4 min-h-0">
        
        {{-- Bottom Left Column: Warning + Cart --}}
        <div class="flex-1 flex flex-col gap-3 min-h-0">
            <div id="allergy-warn" class="hidden bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-600 items-center gap-2 font-bold shrink-0">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span id="allergy-warn-text">คำเตือน: พบประวัติแพ้ยาในรายการสั่งซื้อ!</span>
            </div>

            {{-- Cart Table --}}
            <div class="flex-1 bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col overflow-hidden relative min-h-0">
               {{-- Table Header --}}
                <div class="w-full grid gap-2 px-4 py-3 bg-slate-100 text-slate-600 text-sm font-bold border-b border-slate-200 items-center shrink-0"
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

        {{-- Bottom Right Column: Totals + Actions --}}
        <div class="w-72 shrink-0 flex flex-col gap-3 min-h-0">
            <div class="flex-1 flex flex-col gap-3 min-h-0">
                <button id="pay-btn" disabled onclick="handlePay()" class="w-full flex-1 px-6 py-6 rounded-lg bg-emerald-500 hover:bg-emerald-600 disabled:bg-slate-200 disabled:text-slate-400 text-white font-bold text-2xl shadow-md transition-all flex items-center justify-center gap-2 shrink-0">
                    รับชำระเงิน <span class="text-base bg-black/10 px-3 py-1 rounded-md font-medium ml-2">F9</span>
                </button>
                <div class="flex-1 flex flex-col gap-3 overflow-hidden">
                    <button class="w-full px-6 py-3 rounded-lg bg-white hover:bg-slate-50 text-slate-600 font-medium border border-slate-300 transition-colors shadow-sm shrink-0">เปิดลิ้นชัก</button>
                    <button onclick="printLabel()" class="w-full px-6 py-3 rounded-lg bg-white hover:bg-slate-50 text-slate-600 font-medium border border-slate-300 transition-colors shadow-sm shrink-0">พิมพ์ฉลากยา</button>
                    <button onclick="holdBill()" class="w-full px-6 py-3 rounded-lg bg-white hover:bg-slate-50 text-slate-600 font-medium border border-slate-300 transition-colors shadow-sm shrink-0">พักบิล</button>
                    <button onclick="showHeldBills()" class="w-full px-6 py-3 rounded-lg bg-white hover:bg-slate-50 text-slate-600 font-medium border border-slate-300 transition-colors shadow-sm shrink-0">เรียกบิลที่พัก</button>
                    <button onclick="clearCart()" class="w-full px-6 py-3 rounded-lg bg-white hover:bg-red-50 text-slate-600 hover:text-red-500 font-medium border border-slate-300 hover:border-red-200 transition-colors flex items-center justify-center gap-2 shadow-sm shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        ยกเลิกบิล [F8]
                    </button>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border-2 border-emerald-50 p-5 flex flex-col justify-center relative overflow-hidden shrink-0">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-slate-500 font-medium">บิลล่าสุด</span>
                    <span id="latest-bill-time" class="font-bold text-slate-700 text-lg">{{ $latestBillTime ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-slate-500 font-medium">จำนวนบิล</span>
                    <span id="daily-bill-count" class="font-bold text-slate-700 text-lg">{{ $dailyBills ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium">ยอดรวม</span>
                    <span id="daily-sales-total" class="font-bold text-emerald-500">{{ number_format($dailyTotal ?? 0, 2) }}</span>
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

        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl px-5 py-5 mb-5 border border-emerald-200 shadow-sm">
            <div class="flex justify-between items-end gap-2 mb-4">
                <div class="text-sm text-slate-600 font-semibold tracking-wide">ยอดสุทธิ</div>
                <div id="pay-total-display" class="text-5xl font-extrabold text-emerald-600 leading-none">0.00</div>
            </div>
            <div class="flex justify-between items-center gap-3 pt-4 border-t border-emerald-200">
                <div class="text-sm text-slate-600 font-semibold">ส่วนลดท้ายบิล</div>
                <input type="number" id="pay-bill-discount" min="0" step="0.01" class="text-right text-xl w-40 px-3 py-2 border border-emerald-300 rounded-lg bg-white hover:bg-emerald-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none font-bold text-emerald-700 shadow-sm transition-all" placeholder="0.00" oninput="updatePayChange()">
            </div>
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
            <div class="text-m text-slate-500 mb-1">รับเงินมา</div>
            <input type="number" id="pay-received" min="0" step="0.01" class="text-4xl font-extrabold text-slate-800 w-full bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-4 text-right outline-none focus:ring-2 focus:ring-emerald-400" placeholder="0.00">
        </div>

        <div class="mb-5">
            <div class="text-m text-slate-500 mb-1">เงินทอน</div>
            <input type="text" id="pay-change" readonly value="0.00" class="text-4xl font-extrabold w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-4 text-right text-emerald-600">
        </div>

        <div class="flex gap-2 justify-between">
            <button id="pay-cancel-btn" class="px-5 py-3 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold">ยกเลิก</button>
            <button id="pay-confirm-btn" class="px-6 py-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-lg">ยืนยันชำระเงิน</button>
        </div>
    </div>
</div>

<div id="customer-info-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-70 px-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="text-xl font-bold text-slate-800">ข้อมูลลูกค้า</h3>
            <button onclick="closeCustomerInfo()" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-6 space-y-3">
            <div>
                <span class="text-xs text-slate-400">ชื่อ-นามสกุล</span>
                <div id="cinfo-name" class="font-bold text-slate-800 text-lg">-</div>
            </div>
            <div>
                <span class="text-xs text-slate-400">เบอร์โทร</span>
                <div id="cinfo-phone" class="text-slate-700">-</div>
            </div>
            <div>
                <span class="text-xs text-slate-400">หมายเหตุ / ประวัติแพ้ยา</span>
                <div id="cinfo-note" class="text-slate-700 whitespace-pre-line">-</div>
            </div>
        </div>
    </div>
</div>

<div id="unit-picker-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-60 px-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-200">

        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <div>
                <h3 class="text-xl font-bold text-slate-800">เลือกหน่วย</h3>
                <p id="unit-picker-product-name" class="text-sm text-slate-400 mt-0.5"></p>
            </div>
            <button onclick="closeUnitPicker()" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
        </div>

        <div id="unit-picker-list" class="p-4 space-y-2 max-h-96 overflow-y-auto custom-scrollbar"></div>

    </div>
</div>

<!-- Search Results Modal -->
<div id="search-results-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 px-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl border border-slate-200" style="height: 560px; display: flex; flex-direction: column;">

        <div class="px-5 pt-5 pb-3 shrink-0">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" id="search-input-modal"
                    placeholder="ค้นหารหัส, ชื่อยา หรือสแกนบาร์โค้ด..."
                    class="w-full h-12 pl-11 pr-10 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-base bg-white outline-none transition-all"
                    oninput="searchDrugs(this.value)" autocomplete="off">
                <button onclick="closeSearchModal()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
            </div>
            <p id="search-modal-query" class="text-sm text-slate-400 mt-2 px-1"></p>
        </div>

        <div id="search-modal-list" class="flex-1 overflow-y-auto custom-scrollbar divide-y divide-slate-100 pb-2"></div>

    </div>
</div>

<!-- Product Detail Modal -->
<div id="product-detail-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-60 px-4">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 id="product-detail-name" class="text-xl font-bold text-slate-800"></h3>
            <button onclick="closeProductDetail()" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-5 space-y-4">
            <div>
                <div class="text-sm font-bold text-slate-500 mb-2">โครงสร้างราคา</div>
                <div id="product-detail-prices" class="space-y-2"></div>
            </div>
            <div>
                <div class="text-sm font-bold text-slate-500 mb-2">หน่วย</div>
                <div id="product-detail-units" class="space-y-2"></div>
            </div>
            <div>
                <div class="text-sm font-bold text-slate-500 mb-2">สต็อกคงเหลือ</div>
                <div id="product-detail-stock" class="text-2xl font-extrabold text-emerald-600"></div>
            </div>
        </div>
        <div class="px-5 pb-5">
            <button id="product-detail-add-btn" class="w-full py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-lg transition-colors">
                เพิ่มลงตะกร้า
            </button>
        </div>
    </div>
</div>

<script>
// --- State ---
let cart = {}, allProducts = [], heldBills = JSON.parse(localStorage.getItem('held_bills') || '[]');
let grandTotal = 0;
let scanLock = false;

// Sale Type Toggle
let currentSaleType = 'retail';

function setSaleType(type) {
    currentSaleType = type;

    const btnRetail = document.getElementById('btn-sale-retail');
    const btnWholesale = document.getElementById('btn-sale-wholesale');

    if (type === 'retail') {
        btnRetail.classList.add('bg-emerald-500', 'text-white');
        btnRetail.classList.remove('bg-white', 'text-slate-500');
        btnWholesale.classList.add('bg-white', 'text-slate-500');
        btnWholesale.classList.remove('bg-emerald-500', 'text-white');
    } else {
        btnWholesale.classList.add('bg-emerald-500', 'text-white');
        btnWholesale.classList.remove('bg-white', 'text-slate-500');
        btnRetail.classList.add('bg-white', 'text-slate-500');
        btnRetail.classList.remove('bg-emerald-500', 'text-white');
    }

    // Update all prices in cart
    Object.keys(cart).forEach(id => {
        const p = cart[id].product;
        if (type === 'wholesale') {
            const wsPrice = parseFloat(p.price_wholesale1);
            cart[id].customPrice = (wsPrice > 0) ? wsPrice : parseFloat(p.price_retail);
        } else {
            cart[id].customPrice = undefined;
        }
    });

    updateAll();
    returnFocusToSearch();
}

// Customer Info Modal
let currentCustomerData = null;

function openCustomerInfo() {
    if (!currentCustomerData) return;
    document.getElementById('cinfo-name').textContent = currentCustomerData.full_name || '-';
    document.getElementById('cinfo-phone').textContent = currentCustomerData.phone || '-';
    document.getElementById('cinfo-note').textContent = currentCustomerData.alert_note || '-';
    const modal = document.getElementById('customer-info-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeCustomerInfo() {
    const modal = document.getElementById('customer-info-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    returnFocusToSearch();
}

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
    returnFocusToSearch();
}

// Wire up buttons after DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('quick-add-customer-btn')?.addEventListener('click', openQuickAddCustomer);
    document.getElementById('quick-add-cancel-btn')?.addEventListener('click', closeQuickAddCustomer);
    document.getElementById('quick-add-x-btn')?.addEventListener('click', closeQuickAddCustomer);
    const saveBtn = document.getElementById('quick-add-save-btn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            submitQuickAddCustomer();
        });
    }

    document.getElementById('search-input-modal').addEventListener('keydown', function(e) {
        const modal = document.getElementById('search-results-modal');
        if (!modal || modal.classList.contains('hidden')) return;

        const items = document.querySelectorAll('#search-modal-list > div[id^="search-row-"]');
        const max = items.length;
        if (!max) return;

        function updateSelection(newIndex) {
            searchSelectedIndex = newIndex;
            items.forEach((el, idx) => {
                el.classList.remove('bg-emerald-100', 'bg-emerald-50');
                if (idx === searchSelectedIndex) {
                    el.classList.add('bg-emerald-100');
                    el.scrollIntoView({ block: 'nearest' });
                }
            });
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            updateSelection((searchSelectedIndex + 1) % max);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            updateSelection((searchSelectedIndex - 1 + max) % max);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (searchSelectedIndex >= 0 && items[searchSelectedIndex]) {
                items[searchSelectedIndex].click();
            }
        }
    });
});


// --- Search & Popup Logic ---
let timer;
const searchInput = document.getElementById('search-input');
const searchDropdown = { classList: { add: () => {}, remove: () => {} } };
const searchContainer = document.getElementById('search-results-container');
let searchSelectedIndex = -1;

function isAnyFixedModalOpen() {
    return Array.from(document.querySelectorAll('.fixed')).some(el => !el.classList.contains('hidden'));
}

function returnFocusToSearch() {
    setTimeout(() => {
        const modals = [...document.querySelectorAll('.fixed')].filter(el => 
            !el.classList.contains('hidden') && el.id !== 'toast-container'
        );
        if (modals.length > 0) return;
        document.getElementById('search-input')?.focus();
    }, 50);
}



// --- Keyboard navigation for search autocomplete ---
searchInput.addEventListener('keydown', function(e) {
    const modal = document.getElementById('search-results-modal');
    if (!modal || modal.classList.contains('hidden')) return;

    const items = document.querySelectorAll('#search-modal-list > div[id^="search-row-"]');
    const max = items.length;
    if (!max) return;

    function updateSelection(newIndex) {
        searchSelectedIndex = newIndex;
        items.forEach((el, idx) => {
            el.classList.remove('bg-emerald-100', 'bg-emerald-50');
            if (idx === searchSelectedIndex) {
                el.classList.add('bg-emerald-100');
                el.scrollIntoView({ block: 'nearest' });
            }
        });
    }

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        updateSelection((searchSelectedIndex + 1) % max);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        updateSelection((searchSelectedIndex - 1 + max) % max);
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (searchSelectedIndex >= 0 && items[searchSelectedIndex]) {
            items[searchSelectedIndex].click();
        }
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

let searchTimer;
async function searchDrugs(q) {
    if (!q.trim()) {
        document.getElementById('search-modal-list').innerHTML = '';
        document.getElementById('search-modal-query').textContent = '';
        return;
    }

    q = convertThaiBarcode(q);

    const modal = document.getElementById('search-results-modal');
    if (modal.classList.contains('hidden')) {
        openSearchModal();
        const modalInput = document.getElementById('search-input-modal');
        if (modalInput) {
            modalInput.value = q;
            modalInput.focus();
            document.getElementById('search-input').value = '';
        }
    }

    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => doSearch(q), 200);
}

async function doSearch(q) {
    try {
        const res = await fetch(`/pos/search?q=${encodeURIComponent(q)}`);
        allProducts = await res.json();
        renderSearchResults(allProducts);
    } catch (e) {
        console.error('Search failed:', e);
    }
}

function openSearchModal() {
    const modal = document.getElementById('search-results-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        const modalInput = document.getElementById('search-input-modal');
        if (modalInput) modalInput.focus();
    }, 50);
}

function closeSearchModal() {
    document.getElementById('search-results-modal').classList.add('hidden');
    document.getElementById('search-results-modal').classList.remove('flex');
    document.getElementById('search-input').value = '';
    document.getElementById('search-modal-list').innerHTML = '';
    document.getElementById('search-modal-query').textContent = '';
    returnFocusToSearch();
}

function addToCartFromModal(id) {
    addToCart(id);
    closeSearchModal();
}

function openProductDetail(productId) {
    const p = allProducts.find(x => x.id == productId);
    if (!p) return;

    document.getElementById('product-detail-name').textContent = p.trade_name;

    const stock = p.lots ? p.lots.reduce((s, l) => s + l.qty_on_hand, 0) : 0;
    document.getElementById('product-detail-stock').textContent = stock + ' ' + (p.unit_name ?? '');

    const costPrice = parseFloat(p.cost_price) || 0;
    const prices = [
        { label: 'ราคาปลีก', price: parseFloat(p.price_retail) || 0 },
        { label: 'ราคาส่ง 1', price: parseFloat(p.price_wholesale1) || 0 },
        { label: 'ราคาส่ง 2', price: parseFloat(p.price_wholesale2) || 0 },
    ].filter(x => x.price > 0);

    document.getElementById('product-detail-prices').innerHTML = prices.map(x => {
        const profit = x.price - costPrice;
        const profitPct = costPrice > 0 ? (profit / costPrice * 100) : 0;
        return `
        <div class="flex justify-between items-center bg-slate-50 rounded-lg px-4 py-2">
            <span class="text-sm text-slate-500">${x.label}</span>
            <div class="text-right">
                <span class="font-bold text-slate-800">${x.price.toLocaleString('th', {minimumFractionDigits: 2})} บาท</span>
                <span class="text-xs ml-2 ${profit >= 0 ? 'text-emerald-500' : 'text-red-500'}">
                    กำไร ${profit.toFixed(2)} (${profitPct.toFixed(1)}%)
                </span>
            </div>
        </div>`;
    }).join('');

    const extraUnits = p.product_units || p.productUnits || [];
    const baseUnit = { unit_name: p.unit_name ?? '-', qty_per_base: 1 };
    const units = [baseUnit, ...extraUnits];

    document.getElementById('product-detail-units').innerHTML = units.map(u => `
        <div class="flex justify-between items-center bg-slate-50 rounded-lg px-4 py-2">
            <span class="text-sm font-medium text-slate-700">${u.unit_name}</span>
            <span class="text-xs text-slate-400">${parseFloat(u.qty_per_base) > 1 ? u.qty_per_base + ' หน่วยฐาน' : 'หน่วยฐาน'}</span>
        </div>`).join('');

    document.getElementById('product-detail-add-btn').onclick = () => {
        addToCartFromModal(productId);
        closeProductDetail();
    };

    document.getElementById('product-detail-modal').classList.remove('hidden');
    document.getElementById('product-detail-modal').classList.add('flex');
}

function closeProductDetail() {
    document.getElementById('product-detail-modal').classList.add('hidden');
    document.getElementById('product-detail-modal').classList.remove('flex');
    returnFocusToSearch();
}

function renderSearchResults(products) {
    searchSelectedIndex = -1;
    const modal = document.getElementById('search-results-modal');
    const isModalOpen = !modal.classList.contains('hidden');
    const query = isModalOpen 
        ? document.getElementById('search-input-modal').value 
        : document.getElementById('search-input').value;
    document.getElementById('search-modal-query').textContent = `ค้นหา: "${query}" — พบ ${products.length} รายการ`;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    if (!products.length) {
        document.getElementById('search-modal-list').innerHTML = '<div class="p-8 text-center text-slate-400">ไม่พบข้อมูลยา หรือบาร์โค้ดนี้</div>';
        return;
    }

    document.getElementById('search-modal-list').innerHTML = products.map((p, index) => {
        const stock = p.lots ? p.lots.reduce((s, l) => s + l.qty_on_hand, 0) : 0;
        const price = parseFloat(p.price_retail);
        return `
        <div id="search-row-${p.id}"
            class="px-4 py-3 flex items-center gap-3 hover:bg-emerald-50 transition-colors cursor-pointer ${index === 0 ? 'bg-emerald-50' : ''}"
            onclick="addToCartFromModal('${p.id}')">
            <div class="flex-1">
                <div class="font-bold text-slate-800 text-base flex items-center gap-2">
                    ${p.trade_name}
                    ${stock === 0 ? '<span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-md">สินค้าหมด</span>' : ''}
                </div>
                <div class="text-sm text-slate-500 mt-0.5">
                    สต็อก: <span class="${stock > 0 ? 'text-emerald-600 font-semibold' : 'text-red-500'}">${stock}</span>
                    | รหัส: ${p.code ?? p.id}
                    | หน่วย: ${p.unit_name ?? '-'}
                </div>
            </div>
            <div class="text-right shrink-0">
                <div class="text-emerald-600 font-extrabold text-lg">${price.toLocaleString('th', {minimumFractionDigits: 2})}</div>
                <div class="text-xs text-slate-400">บาท</div>
            </div>
            <button type="button" onclick="event.stopPropagation(); openProductDetail('${p.id}')"
                class="shrink-0 px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium transition-colors">
                ดูเพิ่ม
            </button>
        </div>`;
    }).join('');

    searchSelectedIndex = 0;
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
    returnFocusToSearch();

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
    document.getElementById('discount-input').focus();
    document.getElementById('discount-input').select();
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

document.getElementById('discount-input').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        document.getElementById('discount-ok').click();
    } else if (event.key === 'Escape') {
        event.preventDefault();
        document.getElementById('discount-cancel').click();
    }
});

window.removeItem = function(id) {
    delete cart[id];
    updateAll();
};

function clearCart() {
    if(Object.keys(cart).length === 0) return;
    if(confirm('ต้องการยกเลิกบิลนี้ใช่หรือไม่?')) {
        cart = {};
        closeSearchModal();
        updateAll();
        returnFocusToSearch();
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
                
                <div onclick="openUnitPicker('${p.id}')" 
                    class="text-center text-slate-500 font-medium cursor-pointer hover:bg-slate-100 px-2 py-1 rounded transition-colors"
                    title="คลิกเพื่อเปลี่ยนหน่วย">
                    ${cart[p.id].selectedUnit ? cart[p.id].selectedUnit.unit_name : (p.unit_name ?? '-')}
                </div>
                
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
    
    // ส่วนลดกับจำนวนที่แสดงตรงนี้ ถูกเปลี่ยนไปแสดงข้อมูลสรุปประจำวันแล้ว ทำให้ไม่ต้องอัปเดตตรงนี้อีก

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

function selectCustomer(id, name, isAlert, alertNote, phone = '') {
    currentCustomerId = id;
    currentCustomerName = name;
    currentCustomerData = { id, full_name: name, is_alert: isAlert, alert_note: alertNote, phone };
    document.getElementById('cust-name').textContent = name;

    const infoBtn = document.getElementById('btn-customer-info');
    if (infoBtn) {
        infoBtn.disabled = false;
        infoBtn.classList.remove('text-slate-300');
        infoBtn.classList.add('text-slate-500', 'hover:bg-slate-50');
    }

    const warningBox = document.getElementById('allergy-warn');
    const warningText = document.getElementById('allergy-warn-text');
    const hasAlertNote = Boolean(isAlert) && String(alertNote || '').trim() !== '';

    if (hasAlertNote) {
        warningText.textContent = 'คำเตือน: ' + alertNote;
        warningBox.classList.remove('hidden');
        warningBox.classList.add('flex');
    } else {
        warningBox.classList.add('hidden');
        warningBox.classList.remove('flex');
    }

    closeCustomerModal();
    returnFocusToSearch();
}

function selectWalkIn() {
    currentCustomerId = null;
    currentCustomerName = 'ลูกค้าทั่วไป (เงินสด)';
    currentCustomerData = null;
    document.getElementById('cust-name').textContent = currentCustomerName;

    const infoBtn = document.getElementById('btn-customer-info');
    if (infoBtn) {
        infoBtn.disabled = true;
        infoBtn.classList.add('text-slate-300');
        infoBtn.classList.remove('text-slate-500', 'hover:bg-slate-50');
    }

    const warningBox = document.getElementById('allergy-warn');
    warningBox.classList.add('hidden');
    warningBox.classList.remove('flex');

    closeCustomerModal();
    returnFocusToSearch();
}

function closeCustomerModal() {
    document.getElementById('customer-modal').classList.add('hidden');
    document.getElementById('customer-modal').classList.remove('flex');
    returnFocusToSearch();
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
    returnFocusToSearch();
}

function updatePayChange() {
    const changeEl = document.getElementById('pay-change');
    const receivedEl = document.getElementById('pay-received');
    const typeEl = document.querySelector('input[name="pay-type"]:checked');
    const discountEl = document.getElementById('pay-bill-discount');
    if (!changeEl || !receivedEl) return;

    const subtotal = getGrandTotal();
    const billDiscount = parseFloat(discountEl?.value) || 0;
    const total = Math.max(0, subtotal - billDiscount);

    const totalDisplay = document.getElementById('pay-total-display');
    if (totalDisplay) {
        totalDisplay.textContent = `฿ ${total.toLocaleString('th', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

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

    const subtotal = getGrandTotal();
    const subtotalDisplay = document.getElementById('pay-subtotal-display');
    const payReceived = document.getElementById('pay-received');
    const billDiscountEl = document.getElementById('pay-bill-discount');
    const cashRadio = document.querySelector('input[name="pay-type"][value="cash"]');
    const receivedWrap = document.getElementById('pay-received-wrap');
    const modal = document.getElementById('pay-modal');

    if (!modal || !payReceived || !cashRadio || !receivedWrap) return;

    if (subtotalDisplay) {
        subtotalDisplay.textContent = `฿ ${subtotal.toLocaleString('th', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
    const totalItemsDiscount = Object.values(cart).reduce((s, i) => s + (i.discount || 0), 0);
        if (billDiscountEl) billDiscountEl.value = totalItemsDiscount > 0 ? totalItemsDiscount : '';
        updatePayChange();

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
    const subtotal = getGrandTotal();
    const billDiscount = parseFloat(document.getElementById('pay-bill-discount')?.value) || 0;
    const totalAmount = Math.max(0, subtotal - billDiscount);
    const received = parseFloat(document.getElementById('pay-received').value) || 0;

    const items = Object.values(cart).map(item => ({
        product_id: item.product.id,
        item_name: item.product.trade_name,
        unit_name: item.selectedUnit ? item.selectedUnit.unit_name : (item.product.unit ? item.product.unit.name : item.product.unit_name),
        qty: item.qty,
        unit_price: item.customPrice ?? parseFloat(item.product.price_retail),
        discount: item.discount ?? 0,
        line_total: ((item.customPrice ?? parseFloat(item.product.price_retail)) * item.qty) - (item.discount ?? 0)
    }));

    const itemsDiscount = Object.values(cart).reduce((s, i) => s + (i.discount || 0), 0);

    const payload = {
        customer_id: currentCustomerId || null,
        sale_type: currentSaleType,
        subtotal: subtotal,
        bill_discount: billDiscount,
        total_discount: itemsDiscount + billDiscount,
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
            returnFocusToSearch();

            if (data.daily_bills !== undefined) {
                const el = document.getElementById('daily-bill-count');
                if (el) el.textContent = data.daily_bills;
            }
            if (data.latest_bill_time !== undefined) {
                const el = document.getElementById('latest-bill-time');
                if (el) el.textContent = data.latest_bill_time;
            }
            if (data.daily_total !== undefined) {
                const el = document.getElementById('daily-sales-total');
                if (el) el.textContent = parseFloat(data.daily_total).toLocaleString('th', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
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

document.getElementById('pay-cancel-btn')?.addEventListener('click', function() {
    hidePayModal();
    returnFocusToSearch();
});

document.addEventListener('keydown', function(e) {
    const isPrintableCharacter = e.key.length === 1 || /\d/.test(e.key);
    if (!isPrintableCharacter) return;

    if (isAnyFixedModalOpen()) return;

    const activeEl = document.activeElement;
    if (activeEl && (
        activeEl.tagName === 'INPUT' ||
        activeEl.tagName === 'TEXTAREA' ||
        activeEl.tagName === 'SELECT' ||
        activeEl.isContentEditable
    )) {
        return;
    }

    returnFocusToSearch();
});

// --- Keyboard Shortcuts ---
document.addEventListener('keydown', e => {
    if(e.key === 'F9') { e.preventDefault(); handlePay(); }
    if(e.key === 'F8') { e.preventDefault(); clearCart(); }
    if(e.key === 'F2') { e.preventDefault(); document.getElementById('search-input').focus(); }
    if (e.key === 'Escape') {
        const detailModal = document.getElementById('product-detail-modal');
        const searchModal = document.getElementById('search-results-modal');
        const payModal = document.getElementById('pay-modal');

        if (detailModal && !detailModal.classList.contains('hidden')) {
            e.preventDefault();
            closeProductDetail();
        } else if (searchModal && !searchModal.classList.contains('hidden')) {
            e.preventDefault();
            closeSearchModal();
            searchInput.value = '';
            searchInput.focus();
        } else if (payModal && !payModal.classList.contains('hidden')) {
            e.preventDefault();
            hidePayModal();
        }
    }
});

let currentUnitPickerId = null;

function openUnitPicker(productId) {
    const item = cart[productId];
    if (!item) return;

    const p = item.product;
    const extraUnits = (p.product_units || p.productUnits || []).filter(u => !u.is_disabled);

    const baseUnit = {
        id: 'base',
        unit_name: p.unit_name ?? '-',
        qty_per_base: 1,
        is_base_unit: true,
        is_disabled: false,
        price_retail: parseFloat(p.price_retail) || 0,
        price_wholesale1: parseFloat(p.price_wholesale1) || 0,
        price_wholesale2: parseFloat(p.price_wholesale2) || 0,
    };

    const units = [baseUnit, ...extraUnits];

    if (!units.length) {
        showToast('ไม่พบข้อมูลหน่วยสินค้า', 'error');
        return;
    }

    currentUnitPickerId = productId;
    document.getElementById('unit-picker-product-name').textContent = p.trade_name;

    const saleType = typeof currentSaleType !== 'undefined' ? currentSaleType : 'retail';
    const costPrice = parseFloat(p.cost_price) || 0;

    const list = document.getElementById('unit-picker-list');
    list.innerHTML = units
        .filter(u => !u.is_disabled)
        .map(u => {
            const qtyPerBase = parseFloat(u.qty_per_base) || 1;
            const price = saleType === 'wholesale'
                ? (parseFloat(u.price_wholesale1) > 0 ? parseFloat(u.price_wholesale1) : parseFloat(u.price_retail))
                : parseFloat(u.price_retail);
            const unitCost = costPrice * qtyPerBase;
            const profit = price - unitCost;
            const profitPct = unitCost > 0 ? (profit / unitCost * 100) : 0;

            const isSelected = item.selectedUnit
                ? item.selectedUnit.id == u.id
                : u.is_base_unit;

            return `
            <div onclick="selectUnit('${u.id}')"
                class="px-4 py-3 rounded-xl border cursor-pointer transition-colors ${isSelected ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:bg-slate-50'}"
                id="unit-option-${u.id}">
                <div class="flex justify-between items-center">
                    <span class="font-bold text-slate-800 text-lg">${u.unit_name}</span>
                    <span class="font-extrabold text-emerald-600 text-lg">${price.toLocaleString('th', {minimumFractionDigits: 2})} บาท</span>
                </div>
                <div class="flex justify-between text-xs text-slate-400 mt-1">
                    <span>${qtyPerBase > 1 ? qtyPerBase + ' หน่วยฐาน' : 'หน่วยฐาน'}</span>
                    <span>ต้นทุน: ${unitCost.toFixed(2)} | กำไร: <span class="${profit >= 0 ? 'text-emerald-500' : 'text-red-500'}">${profit.toFixed(2)} (${profitPct.toFixed(1)}%)</span></span>
                </div>
            </div>`;
        }).join('');

    const modal = document.getElementById('unit-picker-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeUnitPicker() {
    const modal = document.getElementById('unit-picker-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    currentUnitPickerId = null;
    returnFocusToSearch();
}

function selectUnit(unitId) {
    const item = cart[currentUnitPickerId];
    if (!item) return;

    const p = item.product;
    const extraUnits = (p.product_units || p.productUnits || []).filter(u => !u.is_disabled);

    const baseUnit = {
        id: 'base',
        unit_name: p.unit_name ?? '-',
        qty_per_base: 1,
        is_base_unit: true,
        is_disabled: false,
        price_retail: parseFloat(p.price_retail) || 0,
        price_wholesale1: parseFloat(p.price_wholesale1) || 0,
        price_wholesale2: parseFloat(p.price_wholesale2) || 0,
    };

    const allUnits = [baseUnit, ...extraUnits];
    const unit = allUnits.find(u => u.id == unitId);
    if (!unit) return;

    const saleType = typeof currentSaleType !== 'undefined' ? currentSaleType : 'retail';
    const price = saleType === 'wholesale'
        ? (parseFloat(unit.price_wholesale1) > 0 ? parseFloat(unit.price_wholesale1) : parseFloat(unit.price_retail))
        : parseFloat(unit.price_retail);

    item.selectedUnit = unit;
    item.customPrice = price;

    updateAll();
    closeUnitPicker();
    showToast('เปลี่ยนหน่วยเป็น ' + unit.unit_name + ' เรียบร้อยแล้ว', 'success');
}

// --- Hygeia-style helper: realtime clock ---
function updateHygeiaTime() {
    const timeEl = document.getElementById('hygeia-time');
    const dateEl = document.getElementById('hygeia-date');
    if(timeEl) {
        const now = new Date();
        timeEl.textContent = now.toLocaleTimeString('th-TH', { hour12: false });
        if (dateEl && dateEl.textContent === '') {
            dateEl.textContent = now.toLocaleDateString('th-TH', { year: 'numeric', month: 'short', day: 'numeric' });
        }
    }
}
setInterval(updateHygeiaTime, 1000);
updateHygeiaTime();

document.addEventListener('click', function() {
    setTimeout(() => {
        const modals = [...document.querySelectorAll('.fixed')].filter(el => 
            !el.classList.contains('hidden') && el.id !== 'toast-container'
        );
        if (modals.length > 0) return;
        const active = document.activeElement;
        if (active && ['INPUT','TEXTAREA','SELECT'].includes(active.tagName)) return;
        document.getElementById('search-input')?.focus();
    }, 100);
});

</script>

@endsection
