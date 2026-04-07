# Fix: Add missing updateBillMeta() to PosController

## Model
🟡 GPT-4.1

## File to Edit
`app/Http/Controllers/PosController.php`

## Backup First
`PosController.php.bk_YYYYMMDD`

---

## Context

The route `PATCH /purchase/bill` already exists in `routes/web.php` pointing to `PosController@updateBillMeta`, but the method does not exist in the controller yet. The edit bill modal form submits to this route and currently fails silently.

---

## Fix: Add updateBillMeta() method

Read the actual file at `app/Http/Controllers/PosController.php`.

Find the `receiveStockHistory()` method's closing brace `}`, then add the following new method immediately after it:

```php
    public function updateBillMeta(Request $request)
    {
        $data = $request->validate([
            'invoice_no'          => 'required|string',
            'supplier_invoice_no' => 'nullable|string|max:100',
            'supplier_id'         => 'required|exists:suppliers,id',
            'receive_date'        => 'required|date',
            'payment_type'        => 'required|in:cash,credit',
            'due_date'            => 'nullable|date|required_if:payment_type,credit',
            'is_paid'             => 'nullable|boolean',
            'paid_date'           => 'nullable|date',
        ]);

        $isPaid   = $request->boolean('is_paid');
        $paidDate = $isPaid ? ($data['paid_date'] ?? null) : null;
        $dueDate  = $data['payment_type'] === 'credit' ? ($data['due_date'] ?? null) : null;

        $update = [
            'supplier_id' => $data['supplier_id'],
            'created_at'  => \Carbon\Carbon::parse($data['receive_date'])->startOfDay(),
            'updated_at'  => now(),
        ];

        if (Schema::hasColumn('product_lots', 'supplier_invoice_no')) {
            $update['supplier_invoice_no'] = $data['supplier_invoice_no'] ?? null;
        }
        if (Schema::hasColumn('product_lots', 'payment_type')) {
            $update['payment_type'] = $data['payment_type'];
        }
        if (Schema::hasColumn('product_lots', 'due_date')) {
            $update['due_date'] = $dueDate;
        }
        if (Schema::hasColumn('product_lots', 'is_paid')) {
            $update['is_paid'] = $isPaid;
        }
        if (Schema::hasColumn('product_lots', 'paid_date')) {
            $update['paid_date'] = $paidDate;
        }

        DB::table('product_lots')
            ->where('invoice_no', $data['invoice_no'])
            ->update($update);

        return redirect()
            ->route('pos.stock.receive.history', ['invoice_no' => $data['invoice_no']])
            ->with('success', 'แก้ไขรายละเอียดบิลเรียบร้อยแล้ว');
    }
```

---

## Notes
- Do NOT change any other methods
- Do NOT run `npm run build`
- Save as UTF-8 without BOM
- Test: open bill → click แก้ไข → change ผู้จำหน่าย → save → should redirect back to bill with success message
