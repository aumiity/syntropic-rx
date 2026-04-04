# Syntropic Rx — System Core
> อัปเดตล่าสุด: 2025-04-04

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
| `prompt.md` | Latest prompt for Copilot/Deepseek (always one prompt, old one deleted) |
| `agent_log.md` | Archive of all completed tasks |

### Workflow Rules
- **prompt.md** — Claude writes prompt with date/time → Copilot/Deepseek reads and writes code
- **agent_log.md** — After each task is done, Claude logs it with timestamp
- **system_core.md** — Updated at end of each day
- Git workflow: `pull → add → commit → push`
- Always backup files before editing
- Run `npm run build` after every CSS change

### Prompt Writing Rules (Important)
- Claude does **NOT read actual files before writing prompts** — saves tokens
- Prompts are written in **English** for better AI comprehension and accuracy
- Write prompts as: **context + problem + goal** — let Copilot/Deepseek figure out the implementation
- Claude already knows project context from system_core.md and conversation history

---

## 🗄️ Database Schema

### Core Tables
- `products` — (trade_name, name_for_print, generic_name, barcode, price_retail, price_wholesale1/2, is_disabled)
- `product_units` — (unit_name, qty_per_base, price_retail, is_for_sale, is_for_purchase)
- `product_lots` — (lot_number, expiry_date, cost_price, qty_on_hand)
- `product_labels` — (label_name, dosage_id, frequency_id, meal_relation_id, label_time_id, advice_id, indication_th/mm/zh, show_barcode, is_default, is_active)
- `label_settings` — (paper_width, paper_height, padding_*, font_family, font_size_*, bold_*, line_spacing, section_gap)

### Label Lookup Tables
- `label_dosages`, `label_frequencies`, `label_meal_relations`, `label_times`, `label_advices`
- `settings` — (shop_name, shop_address, shop_phone, license_no, line_id, tax_id)

### DB Notes
- `item_units` table → dropped
- `is_base_unit` column → removed from `product_units`

---

## ✅ Completed Work

- Product labels tab: modal add/edit, autocomplete search, toggles, label card with preview + language switcher (TH/MM/ZH), print popup
- Settings page: shop info tab (name, address, phone, license, LINE ID, tax ID)
- MCP Filesystem connected to Claude Desktop
- Workflow files created: system_core.md, prompt.md, agent_log.md

---

## ⏳ Pending Tasks

| # | Task | Model | Status |
|---|---|---|---|
| 1 | Fix generateLabelPreviewHTML + printLabel to match new template | 🟡 GPT-4.1 | 🔄 In Progress |
| 2 | Label Settings page (font, paper size, spacing, real-time preview) | 🟡 GPT-4.1 | 🔄 In Progress |
| 3 | Tab ข้อมูลอื่นๆ | 🟡 GPT-4.1 | ⬜ Pending |
| 4 | POS: bill recording + FEFO stock deduction | 🔴 Sonnet | ⬜ Pending |
| 5 | Customer page CRUD | 🟡 GPT-4.1 | ⬜ Pending |
| 6 | Regulatory reports ขย.9-13 | 🔴 Sonnet | ⬜ Pending |

---

## 🔧 Key Technical Notes

- `bootstrap/app.php` must have `api: __DIR__.'/../routes/api.php'` in withRouting
- PowerShell: use `[System.IO.File]::WriteAllText` with `UTF8Encoding($false)` — never `Out-File` (causes BOM)
- Run `npm run build` after every CSS change
- Label modal must be **outside** main form tag
- `markDirty()` must guard: `label-modal` and `labels-list-container`
- Tab switching skips autosave for `tab-labels`

---

## 🏷️ Label Template Spec (100×75 mm)

**Product name on label = `name_for_print` (fallback → `trade_name`) — NOT `label_name`**

```
┌─────────────────────────────────────────────┐
│ shop_name (bold)             date dd/mm/YYYY │
│ shop_address (if any)                        │
│ Tel. shop_phone (if any)                     │
├─────────────────────────────────────────────┤
│ name_for_print || trade_name (bold, 14px)    │
├─────────────────────────────────────────────┤
│ Dosage instructions (bold, 16px)             │
│ Take {dosage} {meal_relation}                │
│ {frequency} times/day | {label_time}         │
├─────────────────────────────────────────────┤
│ Indication: {indication} (if any)            │
│ {advice} (if any)                            │
├─────────────────────────────────────────────┤
│ ║║║ EAN-13 barcode (if show_barcode=true) ║║║ │
└─────────────────────────────────────────────┘
```
