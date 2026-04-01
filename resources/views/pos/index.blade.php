@extends('layouts.app')

@section('content')

<div class="flex flex-col h-screen bg-slate-50 text-slate-800 overflow-hidden px-4 py-4" style="font-family: 'SF Pro TH', 'SF Pro Text', -apple-system, BlinkMacSystemFont, sans-serif;">

    {{-- Hygeia Header (temporary mockup) --}}
    <div class="mb-4 px-4 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-sky-600 text-white shadow-md">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold">Hygeia POS</h1>
                <p class="text-sm opacity-90">หน้าจอขายสินค้า (จำลองหน้าตาโปรแกรมเดิม)</p>
            </div>
            <div class="text-right text-xs">
                <div>สาขา: กรุณาเลือก</div>
                <div>เวลา: <span id="hygeia-time">--:--:--</span></div>
            </div>
        </div>

    </div>

    {{-- ========================================== --}}
    {{-- TOP SECTION: SEARCH & CUSTOMER INFO --}}
    {{-- ========================================== --}}
    <div class="flex gap-4 mb-4">

        {{-- Search Input with Dropdown --}}
        <div class="flex-1 relative z-50">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" id="search-input"
                placeholder="ค้นหารหัส, ชื่อยา หรือสแกนบาร์โค้ด [F2]..."
                class="w-full h-14 pl-12 pr-4 rounded-xl border border-slate-300 shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-lg bg-white outline-none transition-all"
                oninput="searchDrugs(this.value)" autocomplete="off" autofocus>

            {{-- Search Results Popup (Dropdown) --}}
            <div id="search-dropdown" class="absolute top-[110%] left-0 w-full bg-white shadow-2xl rounded-xl border border-slate-200 hidden max-h-[400px] overflow-y-auto custom-scrollbar">
                <div id="search-results-container" class="py-2"></div>
            </div>
        </div>

        {{-- Customer & Doctor Selection --}}
        <div class="w-[400px] flex gap-2">
            <button onclick="changeCustomer()" class="flex-1 bg-white border border-slate-300 rounded-xl px-4 flex items-center justify-between hover:bg-slate-50 transition-colors shadow-sm">
                <div class="flex flex-col text-left">
                    <span class="text-xs text-slate-400 font-medium">ลูกค้า / สมาชิก</span>
                    <span class="text-sm font-bold text-emerald-600" id="cust-name">ลูกค้าทั่วไป (เงินสด)</span>
                </div>
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <button class="w-14 h-14 bg-white border border-slate-300 rounded-xl flex items-center justify-center hover:bg-slate-50 shadow-sm text-slate-500" title="เพิ่มข้อมูลแพทย์">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </button>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MAIN SECTION: ORDER LIST TABLE --}}
    {{-- ========================================== --}}
    <div class="flex-1 bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col overflow-hidden relative">

       {{-- Table Header (ล็อกขนาด Grid ตายตัว 100%) --}}
        <div class="w-full grid gap-2 px-4 py-3 bg-slate-100 text-slate-600 text-sm font-bold border-b border-slate-200 items-center" 
             style="grid-template-columns: 50px 3fr 1fr 80px 1fr 1fr 1fr 50px;">
            <div class="text-center">ลำดับ</div>
            <div>รายการสินค้า (Product)</div>
            <div class="text-center">หน่วย</div>
            <div class="text-center">จำนวน (Qty)</div>
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

    {{-- ========================================== --}}
    {{-- BOTTOM SECTION: SUMMARY & PAYMENT --}}
    {{-- ========================================== --}}
    <div class="h-44 mt-4 flex gap-4 flex-shrink-0">

        {{-- Left: Functions & Warnings --}}
        <div class="flex-1 bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex flex-col justify-between">
            <div id="allergy-warn" class="hidden bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-600 flex items-center gap-2 font-bold mb-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                คำเตือน: พบประวัติแพ้ยาในรายการสั่งซื้อ!
            </div>

            <div class="flex gap-2 mt-auto">
                <button onclick="clearCart()" class="px-6 py-3 rounded-lg bg-white hover:bg-red-50 text-slate-600 hover:text-red-500 font-medium border border-slate-300 hover:border-red-200 transition-colors flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    ยกเลิกบิล [F8]
                </button>
                <button onclick="holdBill()" class="px-6 py-3 rounded-lg bg-white hover:bg-slate-50 text-slate-600 font-medium border border-slate-300 transition-colors shadow-sm">พักบิล</button>
                <button onclick="showHeldBills()" class="px-6 py-3 rounded-lg bg-white hover:bg-slate-50 text-slate-600 font-medium border border-slate-300 transition-colors shadow-sm">เรียกบิลที่พัก</button>
                <button onclick="printLabel()" class="px-6 py-3 rounded-lg bg-white hover:bg-slate-50 text-slate-600 font-medium border border-slate-300 transition-colors shadow-sm">พิมพ์ฉลากยา</button>
            </div>
        </div>

        {{-- Right: Totals (เปลี่ยนเป็นธีมสว่าง ขอบเขียวอ่อน) --}}
        <div class="w-[400px] bg-white rounded-xl shadow-sm border-2 border-emerald-50 p-5 flex flex-col relative overflow-hidden">

            <div class="flex justify-between items-center mb-2">
                <span class="text-slate-500 font-medium">จำนวนรายการ</span>
                <span id="s-count" class="font-bold text-slate-700 text-lg">0</span>
            </div>
            <div class="flex justify-between items-center mb-4 pb-4 border-b border-slate-100">
                <span class="text-slate-500 font-medium">ส่วนลดรวม</span>
                <span class="font-bold text-emerald-500">฿ 0.00</span>
            </div>

            <div class="flex justify-between items-end mb-4">
                <span class="text-xl font-bold text-slate-700">ยอดสุทธิ</span>
                <span class="text-4xl font-extrabold text-emerald-600" id="tb-total">฿ 0.00</span>
            </div>

            <button id="pay-btn" disabled onclick="handlePay()" class="mt-auto w-full py-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 disabled:bg-slate-200 disabled:text-slate-400 text-white font-bold text-xl shadow-md transition-all flex items-center justify-center gap-2">
                รับชำระเงิน <span class="text-sm bg-black/10 px-2 py-1 rounded-md font-medium ml-2">F9</span>
            </button>
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
<div id="price-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96 shadow-lg">
        <h3 class="text-lg font-bold text-slate-800 mb-4">เปลี่ยนราคา</h3>
        <input type="number" id="price-input" class="w-full border border-slate-300 rounded px-3 py-2 mb-4 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" min="0" step="0.01" placeholder="ใส่ราคาใหม่">
        <div class="flex justify-end gap-2">
            <button id="price-cancel" class="px-4 py-2 bg-slate-200 text-slate-700 rounded hover:bg-slate-300 transition-colors">ยกเลิก</button>
            <button id="price-ok" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors">ตกลง</button>
        </div>
    </div>
</div>

<div id="discount-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96 shadow-lg">
        <h3 class="text-lg font-bold text-slate-800 mb-4">ใส่ส่วนลด</h3>
        <input type="number" id="discount-input" class="w-full border border-slate-300 rounded px-3 py-2 mb-4 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" min="0" step="0.01" placeholder="ใส่ส่วนลด">
        <div class="flex justify-end gap-2">
            <button id="discount-cancel" class="px-4 py-2 bg-slate-200 text-slate-700 rounded hover:bg-slate-300 transition-colors">ยกเลิก</button>
            <button id="discount-ok" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors">ตกลง</button>
        </div>
    </div>
</div>

<script>
// --- State ---
let cart = {}, allProducts = [], heldBills = JSON.parse(localStorage.getItem('held_bills') || '[]');

// --- Search & Popup Logic ---
let timer;
const searchInput = document.getElementById('search-input');
const searchDropdown = document.getElementById('search-dropdown');
const searchContainer = document.getElementById('search-results-container');

document.addEventListener('click', function(event) {
    if (!searchInput.contains(event.target) && !searchDropdown.contains(event.target)) {
        searchDropdown.classList.add('hidden');
    }
});

function searchDrugs(q) {
    clearTimeout(timer);
    if (!q.trim()) {
        searchDropdown.classList.add('hidden');
        return;
    }

    timer = setTimeout(async () => {
        try {
            const res = await fetch(`/pos/search?q=${encodeURIComponent(q)}`);
            allProducts = await res.json();
            renderSearchResults(allProducts);
        } catch (e) {
            console.error('Search failed:', e);
        }
    }, 200);
}

function renderSearchResults(products) {
    searchDropdown.classList.remove('hidden');

    if (!products.length) {
        searchContainer.innerHTML = '<div class="p-4 text-center text-slate-500">ไม่พบข้อมูลยา หรือ บาร์โค้ดนี้</div>';
        return;
    }

    searchContainer.innerHTML = products.map(p => {
        const stock = p.lots ? p.lots.reduce((s,l) => s+l.qty_on_hand, 0) : 0;
        const price = parseFloat(p.price_retail);
        const out = stock === 0;

        return `
        <div onclick="${!out ? `addToCart('${p.id}')` : ''}" 
             class="px-4 py-3 border-b border-slate-100 last:border-0 flex justify-between items-center ${out ? 'opacity-50 cursor-not-allowed bg-slate-50' : 'hover:bg-emerald-50 cursor-pointer'}">
            <div class="flex-1">
                <div class="font-bold text-slate-800 text-lg flex items-center gap-2">
                    ${p.trade_name}
                    ${out ? '<span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-md">สินค้าหมด</span>' : ''}
                </div>
                <div class="text-sm text-slate-500 mt-0.5">สต็อกคงเหลือ: <span class="${stock > 0 ? 'text-emerald-600 font-semibold' : 'text-red-500'}">${stock}</span> | รหัส: ${p.id}</div>
            </div>
            <div class="text-right">
                <div class="text-emerald-600 font-extrabold text-lg">฿${price.toLocaleString()}</div>
            </div>
        </div>`;
    }).join('');
}

// --- Cart Actions ---
function addToCart(id) {
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
    const currentPrice = cart[id].customPrice || parseFloat(p.price_retail);
    document.getElementById('price-input').value = currentPrice;
    document.getElementById('price-modal').classList.remove('hidden');
    document.getElementById('price-modal').classList.add('flex');
    window.currentPriceId = id;
};

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

    let grandTotal = 0;

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
            const effectivePrice = item.customPrice || price;
            const discount = item.discount || 0;
            const lineTotal = (effectivePrice * item.qty) - discount;
            grandTotal += lineTotal;

           return `
            <div class="w-full grid gap-2 px-4 py-3 border-b border-slate-100 items-center hover:bg-slate-50 transition-colors" 
                 style="grid-template-columns: 50px 3fr 1fr 80px 1fr 1fr 1fr 50px;">
                 
                <div class="text-center text-slate-500 font-medium">${index + 1}</div>
                
                <div>
                    <div class="font-bold text-slate-800 text-base line-clamp-1">${p.trade_name}</div>
                </div>
                
                <div class="text-center text-slate-500 font-medium">${p.unit ? p.unit.name : '-'}</div>
                
                <div class="flex justify-center">
                    <div class="flex items-center bg-white border border-slate-300 rounded-lg overflow-hidden shadow-sm focus-within:ring-2 focus-within:ring-emerald-500 w-max max-w-[120px]">
                        <button type="button" data-action="changeQty" data-id="${p.id}" data-delta="-1" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors font-bold text-lg">-</button>
                        <input type="number" min="1" value="${item.qty}" onchange="setQty('${p.id}', this.value)" class="w-8 text-center font-bold text-slate-800 border-x border-slate-200 h-8 outline-none bg-white flex-1">
                        <button type="button" data-action="changeQty" data-id="${p.id}" data-delta="1" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors font-bold text-lg">+</button>
                    </div>
                </div>
                
                <div class="text-center">
                    <div onclick="changePrice('${p.id}')" class="cursor-pointer text-slate-700 font-medium hover:bg-slate-100 px-2 py-1 rounded transition-colors inline-block" title="คลิกเพื่อแก้ราคา">฿${effectivePrice.toLocaleString('th', {minimumFractionDigits: 2})}</div>
                </div>
                
                <div class="text-center">
                    <div onclick="changeDiscount('${p.id}')" class="cursor-pointer text-slate-500 font-medium hover:bg-slate-100 px-2 py-1 rounded transition-colors inline-block" title="คลิกเพื่อใส่ส่วนลด">${discount > 0 ? '-฿' + discount.toLocaleString('th', {minimumFractionDigits: 2}) : '-'}</div>
                </div>
                
                <div class="text-center font-bold text-emerald-600 text-lg">฿${lineTotal.toLocaleString('th', {minimumFractionDigits: 2})}</div>
                
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
    if (tbTotal) tbTotal.textContent = '฿ ' + grandTotal.toLocaleString('th', {minimumFractionDigits: 2});
    
    const sCount = document.getElementById('s-count');
    if (sCount) sCount.textContent = keys.length;

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
function changeCustomer() { alert('เปิดหน้าเลือกลูกค้า'); }
function handlePay() { if(!Object.keys(cart).length) return; alert('ไปหน้าต่างรับเงิน (คำนวณเงินทอน)'); }

// --- Keyboard Shortcuts ---
document.addEventListener('keydown', e => {
    if(e.key === 'F9') { e.preventDefault(); handlePay(); }
    if(e.key === 'F8') { e.preventDefault(); clearCart(); }
    if(e.key === 'F2') { e.preventDefault(); searchInput.focus(); }
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
