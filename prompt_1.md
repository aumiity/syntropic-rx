# Prompt — Swap advice and indication order in inline label form
> Date: 2026-04-05

---

## ⚠️ IMPORTANT
Read the actual file from disk before making any changes. Do NOT use memory or conversation history.

File to edit:
- `resources/views/pos/edit_product.blade.php`

Backup before editing:
- Save a copy as `resources/views/pos/edit_product.blade.php.bak.swapfields`

---

## Context

Inside `renderLabelsTable()`, the inline label form currently shows fields in this order:
1. คำแนะนำ (advice_id)
2. ข้อบ่งใช้ไทย / พม่า / จีน (indication_th/mm/zh)

Need to swap so the order becomes:
1. ข้อบ่งใช้ไทย / พม่า / จีน (indication_th/mm/zh)
2. คำแนะนำ (advice_id)

---

## Task — Swap these two blocks in the inline form HTML template

Find this block (advice section comes BEFORE indication):

```
<div>
    <label for="advice_id_search-${label.id}" ...>คำแนะนำ</label>
    <input type="hidden" name="advice_id" id="advice_id_hidden-${label.id}">
    <div class="relative">
        <input type="text" id="advice_id_search-${label.id}" ...>
        <button type="button" id="clear-advice_id-${label.id}" ...>×</button>
        <div id="advice_id_dropdown-${label.id}" ...></div>
    </div>
</div>
<div class="grid grid-cols-3 gap-4">
    <div>
        <label for="indication_th-${label.id}" ...>ข้อบ่งใช้ไทย</label>
        <textarea id="indication_th-${label.id}" ...></textarea>
    </div>
    <div>
        <label for="indication_mm-${label.id}" ...>ข้อบ่งใช้พม่า</label>
        <textarea id="indication_mm-${label.id}" ...></textarea>
    </div>
    <div>
        <label for="indication_zh-${label.id}" ...>ข้อบ่งใช้จีน</label>
        <textarea id="indication_zh-${label.id}" ...></textarea>
    </div>
</div>
```

Swap so indication grid comes BEFORE advice:

```
<div class="grid grid-cols-3 gap-4">
    <div>
        <label for="indication_th-${label.id}" ...>ข้อบ่งใช้ไทย</label>
        <textarea id="indication_th-${label.id}" ...></textarea>
    </div>
    <div>
        <label for="indication_mm-${label.id}" ...>ข้อบ่งใช้พม่า</label>
        <textarea id="indication_mm-${label.id}" ...></textarea>
    </div>
    <div>
        <label for="indication_zh-${label.id}" ...>ข้อบ่งใช้จีน</label>
        <textarea id="indication_zh-${label.id}" ...></textarea>
    </div>
</div>
<div>
    <label for="advice_id_search-${label.id}" ...>คำแนะนำ</label>
    <input type="hidden" name="advice_id" id="advice_id_hidden-${label.id}">
    <div class="relative">
        <input type="text" id="advice_id_search-${label.id}" ...>
        <button type="button" id="clear-advice_id-${label.id}" ...>×</button>
        <div id="advice_id_dropdown-${label.id}" ...></div>
    </div>
</div>
```

---

## Rules
- Do NOT change any field IDs, names, classes, or attributes
- Do NOT change any JS logic
- Only swap the order of these two blocks in the HTML template string
- Backup before editing
