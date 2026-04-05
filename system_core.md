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
| `prompt_N_gpt.md` | Prompt สำหรับ GPT-4.1 (งานง่าย / UI / ตรงไปตรงมา) |
| `prompt_N_sonnet.md` | Prompt สำหรับ Sonnet (งานซับซ้อน / logic หนัก) |
| `agent_log.md` | Archive of all completed tasks |

### Workflow Rules
- **prompt files** — Claude writes prompt with date/time → Copilot/GPT-4.1/Sonnet reads and writes code
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
- Claude reads actual files only when necessary (uploaded by user, or path is uncertain)
- Prompts written in **English** for better AI comprehension
- Write prompts as: **context + problem + goal** — let Copilot/Deepseek figure out implementation
- Every prompt must include the **correct file path from project root** (verified from filesystem, not guessed)
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
- Workflow files created: system_core.md, prompt.md, agent_log.md

### Product Edit Page
- Label tab: modal add/edit, autocomplete search, toggles
- Label card: left preview + right action buttons
- Language switcher: TH / MM / ZH
- Print label popup window

### Settings Page
- Shop info tab: name, address, phone, license, LINE ID, tax ID
- Label Settings tab (ฉลากยา): ✅ completed today

### Label Settings Tab — completed 2025-04-05
- Migration + Model: `label_settings` table with `row_styles` JSON column
- API: GET + POST `/api/label-settings`
- Real-time preview (right side updates instantly)
- Sticky preview — floats while scrolling form
- Per-row typography controls (font size, B/I/U, align, margin-top)
- Row order: shop_name, date, address, divider, product_name, dosage_line1, dosage_line2, indication, advice, barcode
- divider + barcode: marginTop only (no font/B/I/U/align)
- Default paper size: 70×50 mm
- Font family: Google Sans (default), Tahoma, Sarabun, Arial, Courier New
- Removed "ระยะห่าง" section (replaced by per-row marginTop)
- Address + phone on same line: `{address} ({phone})`
- Only ONE divider line (between address and product name)
- Barcode moved to right of product name row
- Dosage split into 2 lines:
  - Line 1: ทานครั้งละ {dosage} วันละ {frequency}
  - Line 2: {meal_relation} {label_time}


---

## ⏳ Pending Tasks

| # | Task | Model | Status |
|---|---|---|---|
| 1 | Fix label preview in product edit page (generateLabelPreviewHTML + printLabel) | 🟡 GPT-4.1 | ⬜ Pending |
| 2 | Tab ข้อมูลอื่นๆ in product edit | 🟡 GPT-4.1 | ⬜ Pending |
| 3 | POS: bill recording + FEFO stock deduction | 🔴 Sonnet | ⬜ Pending |
| 4 | Customer page CRUD | 🟡 GPT-4.1 | ⬜ Pending |
| 5 | Regulatory reports ขย.9-13 | 🔴 Sonnet | ⬜ Pending |

---

## 🔧 Key Technical Notes

- `bootstrap/app.php` must have `api: __DIR__.'/../routes/api.php'` in withRouting
- PowerShell: use `[System.IO.File]::WriteAllText` with `UTF8Encoding($false)` — never `Out-File`
- Run `npm run build` after every CSS change
- Label modal must be **outside** main form tag
- `markDirty()` must guard: `label-modal` and `labels-list-container`
- Tab switching skips autosave for `tab-labels`

---

## 🏷️ Label Template Spec (default 70×50 mm)

**Product name = `name_for_print` fallback `trade_name` — NOT `label_name`**

```
┌──────────────────────────────────────────┐
│ shop_name (bold)          date dd/mm/YYYY │
│ shop_address (shop_phone)                 │
├──────────────────────────────────────────┤
│ name_for_print          [EAN-13 BARCODE] │
│ ทานครั้งละ {dosage} วันละ {frequency}    │
│ {meal_relation} {label_time}              │
│ สรรพคุณ: {indication} (if any)           │
│ {advice} (if any)                         │
└──────────────────────────────────────────┘
```

### row_styles keys (in order):
shop_name, date, address, divider, product_name,
dosage_line1, dosage_line2, indication, advice, barcode

- Full controls: fontSize, bold, italic, underline, align, marginTop
- marginTop only: divider, barcode
