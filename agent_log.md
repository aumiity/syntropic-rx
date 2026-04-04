---

## 2025-04-05

### ✅ MCP Filesystem Setup
- Connected Claude Desktop to repo via MCP Filesystem (Mac path + Windows path)
- Created workflow files: system_core.md, prompt.md, agent_log.md
- Established new workflow: Claude (manager) → prompt.md → Copilot/Deepseek (coder)

### ✅ Label Settings Page (ฉลากยา tab in /settings)
- Migration: `label_settings` table with `row_styles` JSON column
- Model: `LabelSetting` with `current()` static method + default values
- API: `GET /api/label-settings` and `POST /api/label-settings`
- UI: 2-column layout — left sticky preview, right settings form
- Real-time preview updates instantly on every change
- Per-row typography: fontSize slider, B/I/U toggles, align buttons, marginTop slider
- Rows: shop_name, date, address, divider, product_name, dosage_line1, dosage_line2, indication, advice, barcode
- divider + barcode: marginTop control only
- Default paper size changed to 70×50 mm
- Font: added Google Sans (default), Tahoma, Sarabun, Arial, Courier New
- Removed shared "ระยะห่าง" section (each row has independent marginTop)
- Layout: address + phone on same line as `{address} ({phone})`
- Single HR divider line between address and product name
- Barcode moved inline to right of product name
- Dosage split into 2 independent rows:
  - dosage_line1: ทานครั้งละ {dosage} วันละ {frequency}
  - dosage_line2: {meal_relation} {label_time}
- Fixed validation error: renamed `dosage` → `dosage_line1` + `dosage_line2` in backend
- Preview panel is sticky (position: sticky, top: 20px) — stays visible while scrolling

