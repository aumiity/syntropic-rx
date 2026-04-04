# Prompt for Copilot / Deepseek
> Date: 2025-04-05 | Time: 01:00

---

## ⚠️ IMPORTANT — Read Before Doing Anything

**Read the actual files from disk first. Do NOT use memory or conversation history.**

Files to edit:
- `resources/views/settings/index.blade.php`
- `app/Http/Controllers/LabelSettingController.php`
- `app/Models/LabelSetting.php`

---

## Fix 1: Validation Error

### Error:
```
The row styles.dosage.font size field is required. (and 4 more errors)
```

Valid row_styles keys are now exactly these 9 keys:
`shop_name`, `date`, `address`, `product_name`,
`dosage_line1`, `dosage_line2`, `indication`, `advice`, `divider`, `barcode`

Each key (except divider and barcode) requires:
```php
'row_styles.{key}.fontSize'   => 'required|integer|min:6|max:40',
'row_styles.{key}.bold'       => 'required|boolean',
'row_styles.{key}.italic'     => 'required|boolean',
'row_styles.{key}.underline'  => 'required|boolean',
'row_styles.{key}.align'      => 'required|in:left,center,right',
'row_styles.{key}.marginTop'  => 'required|integer|min:-50|max:100',
```

divider and barcode only need:
```php
'row_styles.divider.marginTop' => 'required|integer|min:-50|max:100',
'row_styles.barcode.marginTop' => 'required|integer|min:-50|max:100',
```


---

## Fix 2: Update Default Values in LabelSetting::current()

```php
$defaults = [
    'shop_name'    => ['fontSize'=>13,'bold'=>true, 'italic'=>false,'underline'=>false,'align'=>'left',  'marginTop'=>0],
    'date'         => ['fontSize'=>10,'bold'=>false,'italic'=>false,'underline'=>false,'align'=>'right', 'marginTop'=>0],
    'address'      => ['fontSize'=>10,'bold'=>false,'italic'=>false,'underline'=>false,'align'=>'left',  'marginTop'=>2],
    'product_name' => ['fontSize'=>14,'bold'=>true, 'italic'=>false,'underline'=>false,'align'=>'left',  'marginTop'=>6],
    'dosage_line1' => ['fontSize'=>16,'bold'=>true, 'italic'=>false,'underline'=>false,'align'=>'left',  'marginTop'=>4],
    'dosage_line2' => ['fontSize'=>16,'bold'=>true, 'italic'=>false,'underline'=>false,'align'=>'left',  'marginTop'=>2],
    'indication'   => ['fontSize'=>10,'bold'=>false,'italic'=>false,'underline'=>false,'align'=>'left',  'marginTop'=>4],
    'advice'       => ['fontSize'=>10,'bold'=>false,'italic'=>false,'underline'=>false,'align'=>'left',  'marginTop'=>2],
    'divider'      => ['fontSize'=>10,'bold'=>false,'italic'=>false,'underline'=>false,'align'=>'left',  'marginTop'=>4],
    'barcode'      => ['fontSize'=>10,'bold'=>false,'italic'=>false,'underline'=>false,'align'=>'left',  'marginTop'=>4],
];
```

Also update default paper size:
```php
'paper_width'  => 70,   // changed from 100
'paper_height' => 50,   // changed from 75
```

---

## Fix 3: Sticky Preview

In the "ฉลากยา" tab, the layout is 2 columns: left = preview, right = form.

Make the LEFT preview column sticky so it stays visible while user scrolls the form:

```html
<!-- Tailwind version -->
<div class="sticky top-5 self-start">
    <!-- preview content here -->
</div>
```

The parent grid/flex container must use `items-start` (NOT `items-stretch`).

---

## Fix 4: Barcode — Add marginTop Control

In `generateLabelPreview()`, the barcode section currently has no marginTop.

Add `barcode` to row_styles and apply it in the preview:

```js
// Barcode row — apply marginTop from row_styles
labelContent += `
    <div style="margin-top:${rowStyles.barcode.marginTop}px; text-align:right;">
        <div style="display:inline-block; font-size:10px; letter-spacing:0.5px;">[████ BARCODE ████]</div>
    </div>
`;
```

Add barcode control to UI (marginTop slider only, no font/B/I/U/align needed):
```
บาร์โค้ด (barcode)
[↕ margin-top: slider -50 to 100] [number]px
```

Place this control after the `advice` row in the Row Typography section.


---

## Fix 5: Add Google Sans Font

In `generateLabelPreview()` and the font family selector, add Google Sans.

In the blade file `<head>` section (or in the label settings section), add:
```html
<link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
```

In the font family `<select>`:
```html
<option value="Google Sans">Google Sans</option>
<option value="Tahoma">Tahoma</option>
<option value="Sarabun">Sarabun</option>
<option value="Arial">Arial</option>
<option value="Courier New">Courier New</option>
```

Make `Google Sans` the first option and set it as the new default font_family in `$defaults`.

In `generateLabelPreview()`, use with fallback:
```js
font-family: '${settings.font_family}', 'Google Sans', Arial, sans-serif;
```

---

## Fix 6: Remove "ระยะห่าง" Section

In the settings form, remove the entire "ระยะห่าง" section which contains:
- Line spacing slider
- Section gap slider

These are no longer needed since each row has its own marginTop control.

Do NOT remove padding controls in "ตั้งค่าหน้ากระดาษ" — keep those.

---

## Summary of UI Row Order (after all fixes)

Row Typography section shows these controls in order:
1. ชื่อร้าน (shop_name) — full controls
2. วันที่ (date) — full controls
3. ที่อยู่ / เบอร์โทร (address) — full controls
4. แถบคั่น (divider) — marginTop only
5. ชื่อสินค้า (product_name) — full controls
6. วิธีใช้ บรรทัด 1 (dosage_line1) — full controls
7. วิธีใช้ บรรทัด 2 (dosage_line2) — full controls
8. สรรพคุณ (indication) — full controls
9. คำแนะนำ (advice) — full controls
10. บาร์โค้ด (barcode) — marginTop only

---

## Rules
- Read actual files before making any changes
- Backup before editing
- No Tailwind inside `generateLabelPreview()` — inline styles only
- All changes trigger `updateLabelPreview()` immediately
- Keep other tabs untouched (shop, categories, units, drugtypes)
- Run `npm run build` after CSS changes
- API must always return JSON
