# Fix: Purchase Page (receive_stock.blade.php) — ปรับตามข้อกำหนดใหม่

## Model
🟡 GPT-4.1

## File to Edit
`resources/views/pos/receive_stock.blade.php`

## Backup First
`receive_stock.blade.php.bk_20260406b`

---

## รายการแก้ไข

### 1. ลบ link "ดูหน้าประวัติเต็ม" และ route ที่เกี่ยวข้อง
ใน tab-history ลบปุ่มนี้ทิ้ง:
```
<a href="{{ route('pos.stock.receive.history') }}" ...>ดูหน้าประวัติเต็ม</a>
```
และลบปุ่ม "ดูรายละเอียด" ในแต่ละแถวของตาราง (column จัดการ) ทิ้งด้วย รวมถึงลบ column จัดการออกจากตารางประวัติด้วย

### 2. ลบปุ่ม header ที่ไม่จำเป็น
ในส่วน header ของหน้า ลบปุ่มเหล่านี้ออก:
- "กลับ POS"
- "ผู้จำหน่าย"
- "รายการสินค้า"

ลบ `<div class="flex flex-wrap items-center gap-2">` ทั้ง block ออก

### 3. ผู้จำหน่าย — เปลี่ยนจาก select เป็น autocomplete

แทนที่ `<select name="supplier_id">` ด้วย autocomplete แบบเดียวกับ label dosage ในหน้า edit_product:

```html
<div class="relative">
    <input type="hidden" name="supplier_id" id="supplier_id_hidden" value="{{ old('supplier_id') }}">
    <input type="text" id="supplier_search" autocomplete="off"
        placeholder="พิมพ์ค้นหาผู้จำหน่าย..."
        class="h-11 w-full rounded-lg border border-gray-300 px-3 pr-10 text-sm focus:border-emerald-400 focus:outline-none">
    <button type="button" id="clear_supplier" class="absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 rounded hover:bg-gray-100 text-gray-600 hidden">×</button>
    <div id="supplier_dropdown" class="hidden absolute z-30 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-56 overflow-auto"></div>
</div>
```

ใน JS — เพิ่ม supplier autocomplete:
- ข้อมูลมาจาก `const supplierList = @json($suppliers->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values());`
- filter แบบ client-side (ไม่ต้อง fetch API)
- เมื่อพิมพ์ขึ้น dropdown แสดงชื่อผู้จำหน่ายที่ตรง
- เมื่อเลือกแล้ว set `supplier_id_hidden.value` และ `supplier_search.value`
- ปุ่ม clear ล้างค่าทั้งสอง
- ปิด dropdown เมื่อคลิกที่อื่น (blur timeout 150ms)

### 4. วันที่สั่งซื้อ — เปลี่ยนจาก datetime-local เป็น date

```html
<input id="receive_date" type="date" name="receive_date"
    value="{{ old('receive_date', now()->format('Y-m-d')) }}"
    class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-400 focus:outline-none">
```

อัปเดต JS ที่คำนวณ due_date shortcut ให้ใช้ date แทน datetime:
```javascript
const baseDate = receiveDateInput?.value ? new Date(receiveDateInput.value) : new Date();
```
ยังใช้ได้เหมือนเดิม ไม่ต้องเปลี่ยน

### 5. การชำระเงินเงินสด — auto check "ชำระเงินแล้ว"

ใน JS function `togglePaymentFields()` เพิ่ม logic:
```javascript
function togglePaymentFields() {
    const isCredit = paymentType?.value === 'credit';
    dueDateWrapper?.classList.toggle('hidden', !isCredit);
    if (!isCredit && dueDateInput) {
        dueDateInput.value = '';
    }
    // Auto-check is_paid when cash
    if (isPaidCheckbox) {
        isPaidCheckbox.checked = !isCredit;
        togglePaidDateField();
    }
}
```

### 6. ตัดคอลัมน์ "ราคาขาย" ออกจากตาราง

ลบทั้งหมดที่เกี่ยวกับ sell_price ออก:
- `<th>ราคาขาย</th>` ใน thead
- `<td>...<input name="sell_price[]" class="sell-price-input">...</td>` ใน tbody
- `sell_price[]` input ใน `createRowHtml()`
- `row.querySelector('.sell-price-input')` ใน `applyProductSelection()`
- `name="sell_price[]"` ทุกที่

### 7. ราคารวม — กรอกได้และคำนวณย้อนกลับเป็นราคาทุน/หน่วย

เปลี่ยน `line-total-input` จาก readonly เป็น editable:
```html
<input type="number" name="line_total[]" step="0.01" min="0"
    class="line-total-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-emerald-700 focus:border-emerald-400 focus:outline-none">
```

ใน JS — เพิ่ม event listener สำหรับ line-total-input:
```javascript
if (event.target.classList.contains('line-total-input')) {
    // คำนวณย้อนกลับ: cost = (lineTotal + discount) / qty
    const lineTotal = parseNumber(event.target.value);
    const qty = parseNumber(row.querySelector('.qty-input')?.value) || 1;
    const discount = parseNumber(row.querySelector('.discount-input')?.value);
    const costPerUnit = (lineTotal + discount) / qty;
    const costInput = row.querySelector('.cost-input');
    if (costInput) costInput.value = costPerUnit.toFixed(2);
    updateGrandTotal();
    return;
}
```

และใน `updateRowTotal()` — ต้องไม่ overwrite line-total ถ้า user กำลัง edit มัน:
แก้ให้ update line-total เฉพาะเมื่อ trigger มาจาก qty/cost/discount:
```javascript
function updateRowTotal(row, skipTotalUpdate = false) {
    if (skipTotalUpdate) return;
    const qty = parseNumber(row.querySelector('.qty-input')?.value);
    const cost = parseNumber(row.querySelector('.cost-input')?.value);
    const discount = parseNumber(row.querySelector('.discount-input')?.value);
    const lineTotal = Math.max((qty * cost) - discount, 0);
    const lineTotalInput = row.querySelector('.line-total-input');
    if (lineTotalInput) lineTotalInput.value = lineTotal.toFixed(2);
    updateGrandTotal();
}
```

### 8. วันที่ผลิต และ วันหมดอายุ — กรอก ddmmyy แปลงอัตโนมัติ

เปลี่ยน input type จาก `date` เป็น `text` สำหรับทั้ง manufactured_date และ expiry_date:

```html
<input type="text" name="expiry_date[]"
    placeholder="ddmmyy เช่น 311226"
    class="expiry-date-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-400 focus:outline-none">
<input type="text" name="manufactured_date[]"
    placeholder="ddmmyy เช่น 010124"
    class="manufactured-date-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-400 focus:outline-none">
```

ใน JS — เพิ่ม function และ event listener:
```javascript
function parseDDMMYY(value) {
    // รับ ddmmyy (6 digits) แปลงเป็น dd/mm/20yy แล้ว set เป็น yyyy-mm-dd สำหรับ backend
    const clean = value.replace(/\D/g, '');
    if (clean.length !== 6) return null;
    const dd = clean.slice(0, 2);
    const mm = clean.slice(2, 4);
    const yy = clean.slice(4, 6);
    const yyyy = '20' + yy;
    // validate
    const date = new Date(`${yyyy}-${mm}-${dd}`);
    if (isNaN(date.getTime())) return null;
    return { display: `${dd}/${mm}/${yyyy}`, iso: `${yyyy}-${mm}-${dd}` };
}
```

เพิ่ม event listener ใน body input handler:
```javascript
if (event.target.classList.contains('expiry-date-input') || event.target.classList.contains('manufactured-date-input')) {
    const input = event.target;
    const clean = input.value.replace(/\D/g, '');
    if (clean.length === 6) {
        const parsed = parseDDMMYY(clean);
        if (parsed) {
            input.value = parsed.display; // แสดง dd/mm/yyyy
            input.dataset.isoValue = parsed.iso; // เก็บ iso ไว้สำหรับ submit
            input.classList.remove('border-red-400');
            input.classList.add('border-emerald-400');
        } else {
            input.classList.add('border-red-400');
        }
    }
}
```

ก่อน submit form — แปลง display value กลับเป็น iso:
```javascript
document.getElementById('stock-receive-form')?.addEventListener('submit', function() {
    document.querySelectorAll('.expiry-date-input, .manufactured-date-input').forEach(input => {
        if (input.dataset.isoValue) {
            input.value = input.dataset.isoValue;
        }
    });
});
```

อัปเดต `createRowHtml()` ให้ใช้ input text แทน input date ด้วย

---

## Notes
- ไม่ต้องรัน `npm run build`
- บันทึก UTF-8 ไม่มี BOM
- ทดสอบ: กรอก 311226 → enter → แสดง 31/12/2026
- ทดสอบ: เลือก เงินสด → checkbox ชำระแล้ว auto check
- ทดสอบ: กรอกราคารวม → cost/unit คำนวณย้อนกลับ
