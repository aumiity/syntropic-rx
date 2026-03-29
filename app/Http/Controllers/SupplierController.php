<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->paginate(20);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'nullable|string|max:20|unique:suppliers,code',
            'name' => 'required|string|max:200',
            'tax_id' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'contact_name' => 'nullable|string|max:100',
            'is_disabled' => 'boolean',
        ]);

        $data['is_disabled'] = $request->has('is_disabled');
        Supplier::create($data);

        return redirect()->route('suppliers.index')->with('success', 'เพิ่มผู้จำหน่ายเรียบร้อยแล้ว');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'code' => 'nullable|string|max:20|unique:suppliers,code,' . $supplier->id,
            'name' => 'required|string|max:200',
            'tax_id' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'contact_name' => 'nullable|string|max:100',
            'is_disabled' => 'boolean',
        ]);

        $data['is_disabled'] = $request->has('is_disabled');
        $supplier->update($data);

        return redirect()->route('suppliers.index')->with('success', 'อัพเดตผู้จำหน่ายเรียบร้อยแล้ว');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'ลบผู้จำหน่ายเรียบร้อยแล้ว');
    }
}
