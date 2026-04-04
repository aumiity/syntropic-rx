# Prompt for Copilot / Deepseek
> Date: 2025-04-04 | Time: 23:20

---

## ⚠️ IMPORTANT — Read Before Doing Anything

**Read the actual file from disk first. Do NOT use memory or conversation history.**

File to edit: `resources/views/settings/index.blade.php`

1. Open and read the current file first
2. Make changes based on what is actually in the file right now
3. Do NOT revert or overwrite changes that already exist

---

## Task: Reorder Label Settings UI + Fix Dosage Layout

Two things to do:
1. Reorder sections inside the **"ฉลากยา" tab**
2. Fix dosage instruction rows in `generateLabelPreview()`

---

## Part A: Fix Dosage Instruction Layout in `generateLabelPreview()`

Current (wrong) — all on one line:
```
ทานครั้งละ {dosage} {meal_relation} วันละ {frequency} | {label_time}
```

Correct — split into **2 separate rows**, each with its own `row_styles`:

**Row: dosage_line1**
```
ทานครั้งละ {dosage}  วันละ {frequency}
```
- `dosage` (amount) and `frequency` (times per day) on the **same line**

**Row: dosage_line2**
```
{meal_relation}  {label_time}
```
- `meal_relation` (ก่อน/หลังอาหาร) comes **first**
- followed by `label_time` (เช้า-กลางวัน-เย็น) on the **same line**
- separate them with 2 spaces or a pipe: `หลังอาหาร  เช้า - กลางวัน - เย็น`

So the full dosage block renders as:
```
ทานครั้งละ 1 เม็ด  วันละ 3 ครั้ง
หลังอาหาร  เช้า - กลางวัน - เย็น
```

Update `row_styles` to split dosage into 2 independent rows:
```json
{
  "shop_name":    { "fontSize": 13, "bold": true,  "italic": false, "underline": false, "align": "left",   "marginTop": 0 },
  "date":         { "fontSize": 10, "bold": false, "italic": false, "underline": false, "align": "right",  "marginTop": 0 },
  "address":      { "fontSize": 10, "bold": false, "italic": false, "underline": false, "align": "left",   "marginTop": 2 },
  "product_name": { "fontSize": 14, "bold": true,  "italic": false, "underline": false, "align": "left",   "marginTop": 6 },
  "dosage_line1": { "fontSize": 16, "bold": true,  "italic": false, "underline": false, "align": "left",   "marginTop": 4 },
  "dosage_line2": { "fontSize": 16, "bold": true,  "italic": false, "underline": false, "align": "left",   "marginTop": 2 },
  "indication":   { "fontSize": 10, "bold": false, "italic": false, "underline": false, "align": "left",   "marginTop": 4 },
  "advice":       { "fontSize": 10, "bold": false, "italic": false, "underline": false, "align": "left",   "marginTop": 2 }
}
```

---

## Part B: Reorder Label Settings UI Sections

New section order inside "ฉลากยา" tab (top to bottom):

### 1. ตั้งค่าแต่ละแถว (Row Typography) — TOP

For each row, one control group:
```
[Row label]  [size: slider + number px]  [B][I][U]  [← ≡ →]  [↕ margin-top: slider + number px]
```

Rows to show (in order):
1. ชื่อร้าน (shop_name)
2. วันที่ (date)
3. ที่อยู่ / เบอร์โทร (address)
4. ชื่อสินค้า (product_name)
5. วิธีใช้ บรรทัด 1 — ทานครั้งละ + วันละ (dosage_line1)
6. วิธีใช้ บรรทัด 2 — ก่อน/หลังอาหาร + เวลา (dosage_line2)
7. สรรพคุณ (indication)
8. คำแนะนำ (advice)

---

### 2. Font — after row typography

- Font Family selector: Tahoma, Sarabun, Arial, Courier New

---

### 3. ตั้งค่าหน้ากระดาษ (Paper Settings) — BOTTOM

- Paper width (mm)
- Paper height (mm)
- Padding: top / right / bottom / left (mm)

---

## Helper function (unchanged)

```js
function buildRowStyle(s) {
    return `
        font-size: ${s.fontSize}px;
        font-weight: ${s.bold ? 'bold' : 'normal'};
        font-style: ${s.italic ? 'italic' : 'normal'};
        text-decoration: ${s.underline ? 'underline' : 'none'};
        text-align: ${s.align};
        margin-top: ${s.marginTop}px;
    `;
}
```

---

## Rules
- Read the actual file before making any changes
- Backup before editing
- No Tailwind inside `generateLabelPreview()` — inline styles only
- All changes trigger `updateLabelPreview()` immediately
- Keep all other tabs untouched (shop, categories, units, drugtypes)
- Run `npm run build` after CSS changes
- API must always return JSON
