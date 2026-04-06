# Syntropic Rx — System Core
> อัปเดตล่าสุด: 2026-04-07

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
| `system_core.md` | Project blueprint + daily updates (this file) |
| `prompt_1.md` / `prompt_2.md` / `prompt_3.md` | Prompt files หมุนเวียน — เขียนทับได้เลย |
| `agent_log.md` | Archive of all completed tasks |

### Workflow Rules
- **prompt files** — Claude writes prompt → coder reads and writes code
- **Claude ห้ามแก้ไฟล์ source เอง** — ทุกการแก้ไขต้องเขียนเป็น prompt ให้ coder ทำเท่านั้น ไม่ว่าจะเล็กแค่ไหน
- **agent_log.md** — After each task is done, Claude logs it with timestamp
- **system_core.md** — Updated at end of each day
- Git workflow: `pull → add → commit → push`
- Always backup files before editing
- Run `npm run build` after every CSS change

### Prompt File Naming Convention
- ใช้ไฟล์ `prompt_1.md`, `prompt_2.md`, `prompt_3.md` หมุนเวียน — ไม่ต้องสร้างใหม่
- เมื่องานเสร็จให้เขียนทับไฟล์เดิมได้เลย
- Claude บอก model ในแชทเท่านั้น ไม่ใส่ชื่อ model ในชื่อไฟล์
- ถ้างานเดียว step เดียว ใช้ `prompt_1.md` ไฟล์เดียว
- ถ้าหลาย step ใช้ `prompt_1.md`, `prompt_2.md`, `prompt_3.md` ตามลำดับ

### Prompt Writing Rules
- Claude **MUST check project file structure** via MCP Filesystem before writing any prompt — never assume file paths
- Prompts written in **English** for better AI comprehension
- Write prompts as: **context + problem + goal**
- Every prompt must include the **correct file path** (verified from filesystem, not guessed)
- Every prompt must instruct the coder to: read actual file from disk, backup before editing

---

## 🗄️ Database Schema

### Core Tables
- `products` — (trade_name, name_for_print, generic_name, barcode, price_retail, price_wholesale1/2, is_disabled)
- `product_units` — (unit_name, qty_per_base, price_retail, is_for_sale, is_for_purchase)
- `product_lots` — (lot_number, expiry_date, cost_price, qty_on_hand, supplier_id, supplier_invoice_no, payment_type, due_date, is_paid, paid_date)
- `product_labels` — (label_name, dosage_id, frequency_id, meal_relation_id, label_time_id, advice_id, indication_th/mm/zh, show_barcode, is_default, is_active)
- `label_settings` — (paper_width, paper_height, padding_*, font_family, row_styles JSON)
- `settings` — (shop_name, shop_address, shop_phone, license_no, line_id, tax_id)
- `customers` — (code, full_name, id_card, hn, dob, phone, address, food_allergy, other_allergy, chronic_diseases, is_alert, alert_note, warning_note, is_hidden)
- `suppliers` — (code, name, tax_id, phone, address, contact_name, is_disabled)
- `users` — (name, email, password)

### Label Lookup Tables
- `label_dosages`, `label_frequencies`, `label_meal_relations`, `label_times`, `label_advices`

### DB Notes
- `item_units` table → dropped
- `is_base_unit` column → removed from `product_units`
- `product_lots` — เพิ่ม payment fields (migration pending ถ้ายังไม่ได้รัน)

---

## ✅ Completed Work

### MCP & Workflow Setup
- Claude Desktop connected via MCP Filesystem (Mac + Windows)
- Workflow files: system_core.md, prompt_1.md, agent_log.md

### Settings Page
- Shop info tab + Label Settings tab ✅
- `generateLabelPreview()` เป็น global function ใน `layouts/app.blade.php`

### Product Edit Page — Label Tab ✅
- Inline form (div แทน form เพราะ nested form HTML spec)
- Silent save → reload หน้าหลัง 0.8s
- label inline form ใช้ `<div class="label-inline-form">` ไม่ใช่ `<form>`
- `initializeAllLabelAutocompletes(container)` รับ container เป็น parameter
- Autocomplete dropdown ปิดด้วย blur event (delay 150ms)
- `silentSaveLabel` collect fields ด้วย `querySelectorAll('[name]')` แทน FormData

### Sidebar
- เปลี่ยน `/customers` "ลูกค้า" → `/people` "บุคคล"

### Purchase Page (receive_stock.blade.php) — Redesign ✅
- 2 tab: ประวัติการรับสินค้า | รับสินค้า
- ลบปุ่ม กลับ POS, ผู้จำหน่าย, สินค้า ออก
- ลบ link "ดูหน้าประวัติเต็ม" ออก (ประวัติอยู่ใน tab เดียวกัน)
- ผู้จำหน่าย → autocomplete client-side
- วันที่สั่งซื้อ → date เท่านั้น (ไม่มีเวลา)
- เงินสด → auto check "ชำระเงินแล้ว"
- ตัดคอลัมน์ราคาขายออก
- ราคารวม → กรอกได้ + คำนวณย้อนกลับเป็นต้นทุน/หน่วย
- วันผลิต/หมดอายุ → กรอก ddmmyy แปลงเป็น dd/mm/20yy อัตโนมัติ
- prompt เขียนแล้ว รอ GPT-4.1 execute

---

## ⏳ Pending Tasks

| # | Task | Model | Status |
|---|---|---|---|
| 1 | หน้าบุคคล (People) — ลูกค้า + ผู้จำหน่าย + พนักงาน | 🟡 GPT-4.1 | ⬜ Pending |
| 2 | Purchase page redesign (prompt เขียนแล้ว) | 🟡 GPT-4.1 | ⬜ Pending |
| 3 | Tab ข้อมูลอื่นๆ in product edit | 🟡 GPT-4.1 | ⬜ Pending |
| 4 | POS: bill recording + FEFO stock deduction | 🔴 Sonnet | ⬜ Pending |
| 5 | Regulatory reports ขย.9-13 | 🔴 Sonnet | ⬜ Pending |

---

## 🔧 Key Technical Notes

- `bootstrap/app.php` must have `api: __DIR__.'/../routes/api.php'` in withRouting
- PowerShell: use `[System.IO.File]::WriteAllText` with `UTF8Encoding($false)` — never `Out-File`
- Run `npm run build` after every CSS change
- Label card: ใช้ `<div class="label-inline-form">` ไม่ใช่ `<form>` (nested form ไม่ได้ใน HTML)
- `initializeAllLabelAutocompletes(container)` — ต้องส่ง container เข้าไปเป็น parameter
- Autocomplete dropdown ปิดด้วย blur event delay 150ms (ไม่ใช่ global click)
- `silentSaveLabel` ใช้ `querySelectorAll('[name]')` collect fields (ไม่ใช่ FormData เพราะ div)
- POS-related views อยู่ใน `resources/views/pos/`
- People page: `/people` route, `PeopleController`, `resources/views/people/index.blade.php`

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
