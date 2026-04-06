# Feature: หน้า บุคคล (People) — ลูกค้า + ผู้จำหน่าย + พนักงาน

## Model
🟡 GPT-4.1

## Overview
สร้างหน้า `/people` ที่รวม 3 sub-sections ไว้ใน layout เดียว โดยใช้ tab switching (ไม่ต้องเปลี่ยน URL):
- **ลูกค้า** (Customers)
- **ผู้จำหน่าย** (Suppliers) — ย้ายมาจาก `/suppliers` เดิม
- **พนักงาน** (Staff/Users)

---

## Files to Create / Edit

### Backup before editing:
- `routes/web.php`
- `resources/views/suppliers/index.blade.php` (ถ้ามีอยู่)

---

## 1. Migration — `people` table (ถ้ายังไม่มี customers table)

ตรวจสอบว่ามี `customers` table ใน DB แล้วหรือยัง (Model `Customer` มีอยู่แล้วที่ `app/Models/Customer.php`) ถ้ายังไม่มี migration ให้สร้าง:

```
php artisan make:migration create_customers_table
```

Fields สำหรับ `customers`:
```php
$table->id();
$table->string('code', 20)->nullable()->unique();
$table->string('full_name', 200);
$table->string('id_card', 20)->nullable();
$table->string('hn', 20)->nullable();
$table->date('dob')->nullable();
$table->string('phone', 30)->nullable();
$table->text('address')->nullable();
$table->text('food_allergy')->nullable();
$table->text('other_allergy')->nullable();
$table->text('chronic_diseases')->nullable();
$table->boolean('is_alert')->default(false);
$table->text('alert_note')->nullable();
$table->text('warning_note')->nullable();
$table->boolean('is_hidden')->default(false);
$table->timestamps();
```

---

## 2. Controller — `PeopleController.php`

Create: `app/Http/Controllers/PeopleController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;

class PeopleController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'customers');

        $customers = Customer::orderBy('full_name')->paginate(20, ['*'], 'cpage');
        $suppliers = Supplier::orderBy('name')->paginate(20, ['*'], 'spage');
        $staff     = User::orderBy('name')->paginate(20, ['*'], 'upage');

        return view('people.index', compact('tab', 'customers', 'suppliers', 'staff'));
    }

    // --- Customer CRUD ---
    public function storeCustomer(Request $request)
    {
        $data = $request->validate([
            'code'             => 'nullable|string|max:20|unique:customers,code',
            'full_name'        => 'required|string|max:200',
            'id_card'          => 'nullable|string|max:20',
            'hn'               => 'nullable|string|max:20',
            'dob'              => 'nullable|date',
            'phone'            => 'nullable|string|max:30',
            'address'          => 'nullable|string',
            'food_allergy'     => 'nullable|string',
            'other_allergy'    => 'nullable|string',
            'chronic_diseases' => 'nullable|string',
            'is_alert'         => 'boolean',
            'alert_note'       => 'nullable|string',
            'warning_note'     => 'nullable|string',
        ]);
        $data['is_alert'] = $request->boolean('is_alert');
        Customer::create($data);
        return redirect()->route('people.index', ['tab' => 'customers'])->with('success', 'เพิ่มลูกค้าเรียบร้อยแล้ว');
    }

    public function updateCustomer(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'code'             => 'nullable|string|max:20|unique:customers,code,' . $customer->id,
            'full_name'        => 'required|string|max:200',
            'id_card'          => 'nullable|string|max:20',
            'hn'               => 'nullable|string|max:20',
            'dob'              => 'nullable|date',
            'phone'            => 'nullable|string|max:30',
            'address'          => 'nullable|string',
            'food_allergy'     => 'nullable|string',
            'other_allergy'    => 'nullable|string',
            'chronic_diseases' => 'nullable|string',
            'is_alert'         => 'boolean',
            'alert_note'       => 'nullable|string',
            'warning_note'     => 'nullable|string',
        ]);
        $data['is_alert'] = $request->boolean('is_alert');
        $customer->update($data);
        return redirect()->route('people.index', ['tab' => 'customers'])->with('success', 'อัพเดตลูกค้าเรียบร้อยแล้ว');
    }

    public function destroyCustomer(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('people.index', ['tab' => 'customers'])->with('success', 'ลบลูกค้าเรียบร้อยแล้ว');
    }

    // --- Supplier CRUD (moved from SupplierController) ---
    public function storeSupplier(Request $request)
    {
        $data = $request->validate([
            'code'         => 'nullable|string|max:20|unique:suppliers,code',
            'name'         => 'required|string|max:200',
            'tax_id'       => 'nullable|string|max:20',
            'phone'        => 'nullable|string|max:30',
            'address'      => 'nullable|string',
            'contact_name' => 'nullable|string|max:100',
        ]);
        $data['is_disabled'] = false;
        Supplier::create($data);
        return redirect()->route('people.index', ['tab' => 'suppliers'])->with('success', 'เพิ่มผู้จำหน่ายเรียบร้อยแล้ว');
    }

    public function updateSupplier(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'code'         => 'nullable|string|max:20|unique:suppliers,code,' . $supplier->id,
            'name'         => 'required|string|max:200',
            'tax_id'       => 'nullable|string|max:20',
            'phone'        => 'nullable|string|max:30',
            'address'      => 'nullable|string',
            'contact_name' => 'nullable|string|max:100',
            'is_disabled'  => 'boolean',
        ]);
        $data['is_disabled'] = $request->boolean('is_disabled');
        $supplier->update($data);
        return redirect()->route('people.index', ['tab' => 'suppliers'])->with('success', 'อัพเดตผู้จำหน่ายเรียบร้อยแล้ว');
    }

    public function destroySupplier(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('people.index', ['tab' => 'suppliers'])->with('success', 'ลบผู้จำหน่ายเรียบร้อยแล้ว');
    }

    // --- Staff CRUD ---
    public function storeStaff(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:200',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);
        $data['password'] = bcrypt($data['password']);
        User::create($data);
        return redirect()->route('people.index', ['tab' => 'staff'])->with('success', 'เพิ่มพนักงานเรียบร้อยแล้ว');
    }

    public function updateStaff(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:200',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }
        $user->update($data);
        return redirect()->route('people.index', ['tab' => 'staff'])->with('success', 'อัพเดตพนักงานเรียบร้อยแล้ว');
    }

    public function destroyStaff(User $user)
    {
        $user->delete();
        return redirect()->route('people.index', ['tab' => 'staff'])->with('success', 'ลบพนักงานเรียบร้อยแล้ว');
    }
}
```

---

## 3. Routes — เพิ่มใน `routes/web.php`

```php
use App\Http\Controllers\PeopleController;

// People (ลูกค้า + ผู้จำหน่าย + พนักงาน)
Route::get('/people', [PeopleController::class, 'index'])->name('people.index');

Route::post('/people/customers', [PeopleController::class, 'storeCustomer'])->name('people.customers.store');
Route::put('/people/customers/{customer}', [PeopleController::class, 'updateCustomer'])->name('people.customers.update');
Route::delete('/people/customers/{customer}', [PeopleController::class, 'destroyCustomer'])->name('people.customers.destroy');

Route::post('/people/suppliers', [PeopleController::class, 'storeSupplier'])->name('people.suppliers.store');
Route::put('/people/suppliers/{supplier}', [PeopleController::class, 'updateSupplier'])->name('people.suppliers.update');
Route::delete('/people/suppliers/{supplier}', [PeopleController::class, 'destroySupplier'])->name('people.suppliers.destroy');

Route::post('/people/staff', [PeopleController::class, 'storeStaff'])->name('people.staff.store');
Route::put('/people/staff/{user}', [PeopleController::class, 'updateStaff'])->name('people.staff.update');
Route::delete('/people/staff/{user}', [PeopleController::class, 'destroyStaff'])->name('people.staff.destroy');
```

---

## 4. View — `resources/views/people/index.blade.php`

สร้างไฟล์ใหม่ใช้ layout เดียวกับหน้าอื่นๆ (`@extends('layouts.app')`)

โครงสร้าง:
- Header: "บุคคล" + ปุ่ม "+ เพิ่ม" (แสดง modal ตาม tab ที่ active)
- Tab bar: ลูกค้า | ผู้จำหน่าย | พนักงาน (switch ด้วย `?tab=` หรือ JS)
- แต่ละ tab มีตารางแสดงรายการ + ปุ่มแก้ไข/ลบ
- Modal สำหรับ เพิ่ม/แก้ไข แต่ละประเภท

### Tab: ลูกค้า — columns:
`รหัส | ชื่อ-นามสกุล | เบอร์โทร | HN | วันเกิด | แจ้งเตือน | จัดการ`

### Tab: ผู้จำหน่าย — columns:
`รหัส | ชื่อ | ผู้ติดต่อ | เบอร์โทร | สถานะ | จัดการ`

### Tab: พนักงาน — columns:
`ชื่อ | Email | จัดการ`

### Style guidelines:
- ใช้ Tailwind CSS เหมือนหน้าอื่นๆ ในโปรเจค (rounded-xl, border-gray-200, emerald buttons)
- ปุ่ม เพิ่ม: `bg-emerald-500`
- ปุ่ม แก้ไข: border icon
- ปุ่ม ลบ: `border-red-200 text-red-600`
- Modal: `fixed inset-0 bg-black/40 z-50`

---

## 5. อัพเดต Sidebar

ตรวจหาไฟล์ sidebar/nav ใน `resources/views/layouts/` แล้วเพิ่ม link ไปยัง `/people` (ชื่อ "บุคคล") แทนที่หรือเพิ่มจาก link ผู้จำหน่ายเดิม

---

## Notes
- ไม่ต้องลบ `/suppliers` routes เดิม เผื่อมี link เก่าอยู่
- `User` model มี fillable `name`, `email`, `password` อยู่แล้ว
- `Customer` model มี fillable ครบแล้วใน `app/Models/Customer.php`
- `Supplier` model มี fillable ครบแล้วใน `app/Models/Supplier.php`
- After creating files, verify UTF-8 without BOM
- ตรวจสอบว่า `customers` table มีอยู่ใน DB แล้ว ถ้ายังไม่มีให้รัน migration ก่อน
