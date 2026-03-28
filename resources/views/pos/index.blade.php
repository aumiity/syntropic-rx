@extends('layouts.app')

@section('content')

<div class="flex flex-col h-screen bg-slate-50 font-sans text-slate-800 overflow-hidden px-4 py-4">

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

        {{-- Table Header (เปลี่ยนสีให้สว่าง สะอาดตา) --}}
        <div class="bg-slate-100 text-slate-600 text-sm font-bold grid grid-cols-12 gap-2 px-4 py-3 border-b border-slate-200">
            <div class="col-span-1 text-center">ลำดับ</div>
            <div class="col-span-5">รายการสินค้า (Product)</div>
            <div class="col-span-2 text-center">จำนวน (Qty)</div>
            <div class="col-span-1 text-right">ราคา/หน่วย</div>
            <div class="col-span-1 text-right">ส่วนลด</div>
            <div class="col-span-1 text-right">รวมเงิน</div>
            <div class="col-span-1 text-center">ลบ</div>
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
                <button class="px-6 py-3 rounded-lg bg-white hover:bg-slate-50 text-slate-600 font-medium border border-slate-300 transition-colors shadow-sm">พักบิล</button>
                <button class="px-6 py-3 rounded-lg bg-white hover:bg-slate-50 text-slate-600 font-medium border border-slate-300 transition-colors shadow-sm">พิมพ์ใบสั่งยา</button>
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

<script>
// --- State ---
let cart = {}, allProducts = [];

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
        <div onclick="${!out ? `addToCart(${p.id})` : ''}"
             class="px-4 py-3 hover:bg-emerald-50 cursor-pointer border-b border-slate-100 last:border-0 flex justify-between items-center ${out ? 'opacity-50 cursor-not-allowed bg-slate-50' : ''}">
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
    const p = allProducts.find(p => p.id == id);
    if (!p) return;

    if(cart[id]) {
        cart[id].qty += 1;
    } else {
        cart[id] = {product: p, qty: 1};
    }

    searchInput.value = '';
    searchDropdown.classList.add('hidden');
    searchInput.focus();

    updateAll();
}

function changeQty(id, d) {
    if (!cart[id]) return;
    cart[id].qty += d;
    if (cart[id].qty <= 0) delete cart[id];
    updateAll();
}

function removeItem(id) {
    delete cart[id];
    updateAll();
}

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
function updateAll() {
    const list = document.getElementById('cart-list');
    const emptyState = document.getElementById('empty-cart');
    const keys = Object.keys(cart);

    let grandTotal = 0;

    if (!keys.length) {
        list.innerHTML = '';
        list.appendChild(emptyState);
        emptyState.classList.remove('hidden');
        emptyState.classList.add('flex');
    } else {
        emptyState.classList.add('hidden');
        emptyState.classList.remove('flex');

        list.innerHTML = keys.map((id, index) => {
            const item = cart[id];
            const p = item.product;
            const price = parseFloat(p.price_retail);
            const lineTotal = price * item.qty;
            grandTotal += lineTotal;

            return `
            <div class="grid grid-cols-12 gap-2 px-4 py-3 border-b border-slate-100 items-center hover:bg-slate-50 transition-colors">
                <div class="col-span-1 text-center text-slate-500 font-medium">${index + 1}</div>
                <div class="col-span-5">
                    <div class="font-bold text-slate-800 text-base">${p.trade_name}</div>
                    <div class="text-xs text-slate-400">Barcode: ${p.barcode || '-'}</div>
                </div>
                <div class="col-span-2 flex justify-center">
                    <div class="flex items-center bg-white border border-slate-300 rounded-lg overflow-hidden shadow-sm">
                        <button onclick="changeQty(${p.id}, -1)" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">−</button>
                        <input type="text" value="${item.qty}" class="w-10 text-center font-bold text-slate-800 border-x border-slate-200 h-8 outline-none" readonly>
                        <button onclick="changeQty(${p.id}, 1)" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">+</button>
                    </div>
                </div>
                <div class="col-span-1 text-right text-slate-700 font-medium">฿${price.toLocaleString()}</div>
                <div class="col-span-1 text-right text-slate-400">-</div>
                <div class="col-span-1 text-right font-bold text-emerald-600 text-lg">฿${lineTotal.toLocaleString()}</div>
                <div class="col-span-1 flex justify-center">
                    <button onclick="removeItem(${p.id})" class="w-8 h-8 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </div>`;
        }).join('');
    }

    document.getElementById('tb-total').textContent = '฿ ' + grandTotal.toLocaleString('th', {minimumFractionDigits: 2});
    document.getElementById('s-count').textContent = keys.length;

    const payBtn = document.getElementById('pay-btn');
    payBtn.disabled = !keys.length;
}

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
    const now = new Date();
    const t = now.toLocaleTimeString('th-TH', { hour12: false });
    document.getElementById('hygeia-time').textContent = t;
}
setInterval(updateHygeiaTime, 1000);
updateHygeiaTime();
</script>

@endsection
