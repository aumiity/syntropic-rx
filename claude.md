# Syntropic Rx — System Core
> อัปเดตล่าสุด: 2026-04-09

---

## 🏗️ โปรเจคคืออะไร
Laravel WebApp บริหารจัดการร้านยา ทดแทนระบบเก่า Hygeia

| Item | Detail |
|---|---|
| Stack | PHP 8.3 + Laravel + Blade + Tailwind CSS + Vite + MySQL |
| GitHub | `aumiity/syntropic-rx` |
| Local Dev (Windows) | `http://syntropic-rx.test` (Laragon + Apache) |
| Path (Windows) | `C:\laragon\www\syntropic-rx` |
| Path (Mac) | `/Users/CYUT/Documents/GitHub/syntropic-rx` |
| DB | `100.94.208.11` (Tailscale) |

---

## 👥 Team & Roles

| Role | Responsibility |
|---|---|
| Claude (Sonnet) | Project Manager — system design, write prompts, update docs |
| Copilot / GPT-4.1 / Deepseek | Write all actual code |
| CYUT | Decision maker, runner, reviewer |

---

## 📁 Workflow Files

| File | Purpose |
|---|---|
| `claude.md` | Project blueprint + daily updates (this file) |
| `agent_log.md` | Archive of all completed tasks |

### Workflow Rules (Updated 2026-04-09)
- Claude must NEVER edit or write any files directly
- All prompts are written directly in chat — do NOT use prompt files
- Always specify model tag above every prompt: 🟡 GPT-4.1 or 🔴 Sonnet
- CYUT pastes relevant file contents directly in chat — Claude does NOT open files independently (saves tokens)
- Git workflow: pull → add → commit → push
- Always backup files before editing
- Run `npm run build` after every CSS change

---

## 🗄️ Database Schema

### Core Tables
- `products` — (trade_name, name_for_print, generic_name, barcode, price_retail, price_wholesale1/2, is_disabled)
- `product_units` — (unit_name, qty_per_base, price_retail, is_base_unit, is_for_sale, is_for_purchase, is_disabled)
- `product_lots` — (lot_number, expiry_date, cost_price, qty_on_hand, supplier_id, supplier_invoice_no, payment_type, due_date, is_paid, paid_date, is_cancelled, cancelled_at, cancel_note)
- `product_labels` — (label_name, dosage_id, frequency_id, meal_relation_id, label_time_id, advice_id, indication_th/mm/zh, show_barcode, is_default, is_active)
- `label_settings` — (paper_width, paper_height, padding_*, font_family, row_styles JSON)
- `settings` — (shop_name, shop_address, shop_phone, license_no, line_id, tax_id)
- `customers` — (code, full_name, id_card, hn, dob, phone, address, food_allergy, other_allergy, chronic_diseases, is_alert, alert_note, warning_note, is_hidden)
- `suppliers` — (code, name, tax_id, phone, address, contact_name, is_disabled)
- `users` — (name, email, password)
- `stock_movements` — (product_id, lot_id, movement_type, ref_type, ref_id, qty_change, qty_before, qty_after, unit_cost, note, created_by)

### Label Lookup Tables
- `label_dosages`, `label_frequencies`, `label_meal_relations`, `label_times`, `label_advices`

### DB Notes
- `item_units` table → dropped
- `product_lots` — added: is_cancelled, cancelled_at, cancel_note (migration: 2026_04_07_000001)
- Old lots without invoice_no were patched via SQL: UPDATE product_lots SET invoice_no = CONCAT('PO-', DATE_FORMAT(created_at,'%Y%m%d'), '-', LPAD(id,4,'0')) WHERE invoice_no IS NULL

---

## ✅ Completed Work

### MCP & Workflow Setup
- Claude Desktop connected via MCP Filesystem (Mac + Windows)

### Settings Page ✅
- Shop info tab + Label Settings tab
- `generateLabelPreview()` เป็น global function ใน `layouts/app.blade.php`

### Product Edit Page — Label Tab ✅
- Inline form (div แทน form เพราะ nested form HTML spec)
- Silent save → reload หน้าหลัง 0.8s
- `initializeAllLabelAutocompletes(container)` รับ container เป็น parameter
- Autocomplete dropdown ปิดด้วย blur event (delay 150ms)
- `silentSaveLabel` collect fields ด้วย `querySelectorAll('[name]')` แทน FormData

### Sidebar ✅
- เปลี่ยน `/customers` "ลูกค้า" → `/people` "บุคคล"

### Purchase Page — Bill History + Edit + Cancel ✅
- Tab history: bill list with filter (date, supplier_invoice_no, supplier) + sort all columns
- "ดูรายละเอียด" → /purchase/history?invoice_no=X → single bill card + items table
- Edit bill modal: edits supplier, supplier_invoice_no, receive_date, payment_type, due_date, is_paid, paid_date — invoice_no is NOT editable
- Cancel bill: reverses qty_on_hand per lot, inserts stock_movements (purchase_return, ref_type=bill_cancel), marks is_cancelled
- receiveStockForm() uses MIN/MAX aggregates + GROUP BY invoice_no, suppliers.name for MySQL strict mode (only_full_group_by)
- updateBillMeta(): invoice_no is nullable in validation, uses whereNull() for old bills

### Purchase Page — Receive Stock Form ✅
- Product autocomplete search: GET /api/products/search?q= → returns id, trade_name, barcode, units[]
- Unit dropdown per row: default = base unit (is_base_unit=1), options = is_for_purchase=1 or is_base_unit=1
- searchProducts() uses whereIn to avoid N+1, groups units by product_id
- Real-time line total: qty × cost_price - discount = line_total (readonly)
- Grand total: sum of all line_total, updates real-time
- Date input: ddmmyy → dd/mm/yyyy on blur (blur uses document + useCapture:true)
- Merge lot: if lot_number already exists → weighted average cost = (old_qty × old_cost + new_qty × new_cost) / (old_qty + new_qty)

---

## ⏳ Pending Tasks

| # | Task | Model | Status |
|---|---|---|---|
| 1 | Run migration add_cancel_fields_to_product_lots | CYUT | ⬜ Pending |
| 2 | หน้าบุคคล (People) — ลูกค้า + ผู้จำหน่าย + พนักงาน | 🟡 GPT-4.1 | ⬜ Pending |
| 3 | Tab ข้อมูลอื่นๆ in product edit | 🟡 GPT-4.1 | ⬜ Pending |
| 4 | POS: bill recording + FEFO stock deduction | 🔴 Sonnet | ⬜ Pending |
| 5 | Regulatory reports ขย.9-13 | 🔴 Sonnet | ⬜ Pending |

---

## 🔧 Key Technical Notes

- Purchase history: GROUP BY invoice_no, suppliers.name — use MIN/MAX for all other SELECT fields (MySQL only_full_group_by)
- updateBillMeta(): invoice_no nullable in validation, uses whereNull() for old bills
- Cancel bill: movement_type = 'purchase_return', ref_type = 'bill_cancel'
- Prompts are written directly in chat — no prompt files used anymore
- /api/products/search route must NOT have middleware('auth') — ระบบนี้ไม่มี login route

---

## 🐛 Common Copilot Bugs

- `@push('scripts')` วางผิดที่ — ต้องครอบ `<script>...</script>` ทั้งก้อน ไม่ใช่วางนอก
- `blur` event delegation ต้องใช้ `document` ไม่ใช่ tbody + ต้องใส่ `useCapture: true`
- `parseDDMMYY` ต้องใช้ `getFullYear/getMonth/getDate` ไม่ใช่ UTC version (timezone offset ทำให้วันเลื่อน)
- Function ที่ใช้ใน event listener ต้องอยู่ global scope หรือ scope เดียวกัน
- Copilot มักวาง code ผิด namespace และลืม `use` imports
- Modal ใน main form ทำให้ field name ชน — ต้องวาง modal นอก `<form>` เสมอ

---

## 🏷️ Label Template Spec (default 80×50 mm)

**Product name = `name_for_print` fallback `trade_name` — NOT `label_name`**

```
┌──────────────────────────────────────────┐
│ shop_name (left)          date (right)   │
│ shop_address (shop_phone)                │
├──────────────────────────────────────────┤
│ name_for_print          [EAN-13 BARCODE] │
│ ทานครั้งละ {dosage} วันละ {frequency}    │
│ {meal_relation} {label_time}             │
│ ข้อบ่งใช้: {indication} (if any)        │
│ หมายเหตุ: {advice} (if any)             │
└──────────────────────────────────────────┘
```

### row_styles keys (in order):
shop_name, date, address, divider, product_name,
dosage_line1, dosage_line2, indication, advice, barcode

- Full controls: fontSize, bold, italic, underline, align, marginTop
- marginTop uses `position:relative; top:` (not margin-top)