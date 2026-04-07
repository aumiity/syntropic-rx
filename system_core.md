# Syntropic Rx — System Core
> อัปเดตล่าสุด: 2026-04-08

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

### Workflow Rules (Updated 2026-04-08)
- Claude can READ source files via MCP but must NEVER edit or write any files
- All prompts are written directly in chat — do NOT use prompt_1.md or any prompt files
- Always specify model tag in every prompt: 🟡 GPT-4.1 or 🔴 Sonnet
- Claude asks for file contents in chat before writing prompts — never opens files independently
- Git workflow: pull → add → commit → push
- Always backup files before editing
- Run `npm run build` after every CSS change

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
- `product_lots` — added: is_cancelled, cancelled_at, cancel_note (migration: 2026_04_07_000001 — run php artisan migrate if not yet done)
- Old lots without invoice_no were patched via SQL: UPDATE product_lots SET invoice_no = CONCAT('PO-', DATE_FORMAT(created_at,'%Y%m%d'), '-', LPAD(id,4,'0')) WHERE invoice_no IS NULL

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

### Purchase Page — Bill History + Edit + Cancel ✅
- Tab history: bill list with filter (date, supplier_invoice_no, supplier) + sort all columns
- Columns: วันที่รับ, เลขที่เอกสาร, เลขที่บิล, ผู้จำหน่าย, รายการ, การชำระเงิน, สถานะ, มูลค่ารวม, จัดการ
- "ดูรายละเอียด" → /purchase/history?invoice_no=X → single bill card + items table
- Bill card: shows all fields with N/A fallback for old/missing data
- Edit bill modal: edits supplier, supplier_invoice_no, receive_date, payment_type, due_date, is_paid, paid_date — invoice_no is NOT editable
- Cancel bill: reverses qty_on_hand per lot, inserts stock_movements (purchase_return, ref_type=bill_cancel), marks is_cancelled
- receiveStockForm() uses MIN/MAX aggregates + GROUP BY invoice_no, suppliers.name for MySQL strict mode (only_full_group_by)
- updateBillMeta(): invoice_no is nullable in validation, uses whereNull() for old bills

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
// ...existing code...

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
