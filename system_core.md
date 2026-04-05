# Syntropic Rx — System Core
> อัปเดตล่าสุด: 2026-04-05

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
- `product_lots` — (lot_number, expiry_date, cost_price, qty_on_hand)
- `product_labels` — (label_name, dosage_id, frequency_id, meal_relation_id, label_time_id, advice_id, indication_th/mm/zh, show_barcode, is_default, is_active)
- `label_settings` — (paper_width, paper_height, padding_*, font_family, row_styles JSON)
- `settings` — (shop_name, shop_address, shop_phone, license_no, line_id, tax_id)

### Label Lookup Tables
- `label_dosages`, `label_frequencies`, `label_meal_relations`, `label_times`, `label_advices`

### DB Notes
- `item_units` table → dropped
- `is_base_unit` column → removed from `product_units`

---

## ✅ Completed Work

### MCP & Workflow Setup
- Claude Desktop connected via MCP Filesystem (Mac + Windows)
- Workflow files: system_core.md, prompt_1.md, agent_log.md

### Settings Page
- Shop info tab: name, address, phone, license, LINE ID, tax ID
- Label Settings tab (ฉลากยา): ✅ completed
  - Migration + Model: `label_settings` table with `row_styles` JSON column
  - API: GET + POST `/api/label-settings`
  - Real-time preview with scale-to-fit
  - Per-row typography controls (font size, B/I/U, align, margin-top)
  - `generateLabelPreview()` moved to `layouts/app.blade.php` as global shared function
  - ลบ tab ประเภทสินค้า, หน่วยนับ, ประเภทยาตามกฎหมาย ออก
  - เพิ่มปุ่ม 🖨 พิมพ์ฉลากตัวอย่าง (printSampleLabel via popup window)

### Product Edit Page — Label Tab
- ลบ popup modal ออก → เปลี่ยนเป็น inline form ใน label card แทน
- Layout: Left = preview + lang switcher, Right = inline edit form
- Field order: ชื่อฉลาก, ปริมาณ, ความถี่, รูปแบบ, เวลา, ข้อบ่งใช้ TH/MM/ZH, คำแนะนำ
- Auto-populate form fields after render
- is_default → unset อื่นๆ อัตโนมัติ (server + client)
- Route PUT `/products/{product}/labels/{label}` + `updateLabel()` in PosController
- Silent save: ปุ่มบันทึก save โดยไม่ reload หน้า, update preview in-place
- Toggle (is_default, is_active, show_barcode) → auto-save on change
- generateLabelPreview shared จาก app.blade.php ใช้ร่วมกันทั้ง 2 หน้า

### Label Preview Consistency
- `generateLabelPreview` ใน settings และ `generateLabelPreviewHTML` ใน edit_product ใช้ logic เดียวกัน
- row_styles ใช้ `position:relative; top:` แทน `margin-top`
- labelSettings ดึงจาก `/api/label-settings` → `.data`
- Print window ใช้ `@page { margin: 0 }` + Google Fonts

---

## ⏳ Pending Tasks

| # | Task | Model | Status |
|---|---|---|---|
| 1 | Double scrollbar in label tab | 🟡 GPT-4.1 | ⬜ Pending |
| 2 | Tab ข้อมูลอื่นๆ in product edit | 🟡 GPT-4.1 | ⬜ Pending |
| 3 | POS: bill recording + FEFO stock deduction | 🔴 Sonnet | ⬜ Pending |
| 4 | Customer page CRUD | 🟡 GPT-4.1 | ⬜ Pending |
| 5 | Regulatory reports ขย.9-13 | 🔴 Sonnet | ⬜ Pending |

---

## 🔧 Key Technical Notes

- `bootstrap/app.php` must have `api: __DIR__.'/../routes/api.php'` in withRouting
- PowerShell: use `[System.IO.File]::WriteAllText` with `UTF8Encoding($false)` — never `Out-File`
- Run `npm run build` after every CSS change
- `markDirty()` must guard: `labels-list-container`
- Tab switching skips autosave for `tab-labels`
- Label card inline form: unique IDs per label using `-${label.id}` suffix
- `silentSaveLabel(labelId, formEl)` — saves without reload, updates preview in-place
- `printViaIframe` not used — using popup window with `@page { margin: 0 }`

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
- marginTop only: divider, barcode
- marginTop uses `position:relative; top:` (not margin-top)
