# Prompt — Silent save: keep save button, auto-save toggles, update preview in-place
> Date: 2026-04-05

---

## ⚠️ IMPORTANT
Read the actual file from disk before making any changes. Do NOT use memory or conversation history.

File to edit:
- `resources/views/pos/edit_product.blade.php`

Backup before editing:
- Save a copy as `resources/views/pos/edit_product.blade.php.bak.silentsave`

---

## Goal

1. Keep 💾 บันทึก button — when clicked, save silently (no page reload), then update preview in-place
2. Toggle fields (is_default, is_active, show_barcode) — save immediately and silently on change
3. Never call `loadLabels()` after save (except for new labels) — no full page reload

---

## Step 1 — Add `silentSaveLabel(labelId, formEl)` helper function

Add this function inside the DOMContentLoaded block, near `renderLabelsTable`:

```js
async function silentSaveLabel(labelId, formEl) {
    const formData = new FormData(formEl);
    formData.set('show_barcode', formEl.querySelector(`#show_barcode-${labelId}`)?.checked ? '1' : '0');
    formData.set('is_default',   formEl.querySelector(`#is_default-${labelId}`)?.checked   ? '1' : '0');
    formData.set('is_active',    formEl.querySelector(`#is_active-${labelId}`)?.checked    ? '1' : '0');

    const isNew = labelId === 'new';
    const url   = isNew ? labelApiBase : `${labelApiBase}/${labelId}`;
    if (!isNew) formData.append('_method', 'PUT');

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: formData,
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data.message || 'บันทึกไม่สำเร็จ');

        showToast('บันทึกแล้ว', 'success');

        // Update preview in-place
        const updatedLabel = data.label || {};
        const currentLang = document.querySelector(`.label-lang-btn[data-label-id="${labelId}"][style*="10b981"]`)?.dataset.lang || 'th';
        const previewDiv = document.querySelector(`.label-preview-${labelId}`);
        if (previewDiv && updatedLabel) {
            previewDiv.innerHTML = generateLabelPreviewHTML(updatedLabel, shopSettings, currentLang, false, labelSettings);
        }

        // If is_default was set, uncheck all other is_default toggles in UI
        if (formEl.querySelector(`#is_default-${labelId}`)?.checked) {
            document.querySelectorAll('.label-inline-form').forEach(otherForm => {
                const otherId = otherForm.dataset.labelId;
                if (otherId !== String(labelId)) {
                    const otherDefault = otherForm.querySelector(`#is_default-${otherId}`);
                    if (otherDefault) otherDefault.checked = false;
                }
            });
        }

        // If new label, reload to get real ID
        if (isNew) loadLabels();

    } catch (error) {
        showToast(error.message, 'error');
    }
}
```

---

## Step 2 — Change submit handler to use silentSaveLabel

Find the section in `renderLabelsTable` where `.label-inline-form` submit events are bound:

```js
container.querySelectorAll('.label-inline-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const labelId = form.dataset.labelId;
        const formData = new FormData(form);
        let url = labelApiBase;
        let method = 'POST';
        if (labelId !== 'new') {
            url = `${labelApiBase}/${labelId}`;
            formData.append('_method', 'PUT');
        }
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData,
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(data.message || 'บันทึกไม่สำเร็จ');
            showToast(data.message || 'บันทึกสำเร็จ', 'success');
            loadLabels();
        } catch (error) {
            showToast(error.message, 'error');
        }
    });
});
```

Replace with:

```js
container.querySelectorAll('.label-inline-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const labelId = form.dataset.labelId;
        await silentSaveLabel(labelId, form);
    });
});
```

---

## Step 3 — Bind toggle auto-save after populate loop

Find the section after the populate loop (labels.forEach that sets .value and .checked).
Add this block immediately after it:

```js
// Auto-save on toggle change
labels.forEach(label => {
    const id = label.id;
    const form = document.querySelector(`.label-inline-form[data-label-id="${id}"]`);
    if (!form) return;

    ['show_barcode', 'is_default', 'is_active'].forEach(fieldName => {
        const el = document.getElementById(`${fieldName}-${id}`);
        el?.addEventListener('change', () => silentSaveLabel(id, form));
    });
});
```

---

## Rules
- Do NOT remove 💾 บันทึก button — keep it in the form
- Do NOT call `loadLabels()` after save (except for new labels)
- Do NOT change `generateLabelPreviewHTML`, `printLabel`, delete button, or barcode logic
- Backup before editing
