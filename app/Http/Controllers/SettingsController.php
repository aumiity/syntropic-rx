<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ItemUnit;
use App\Models\DrugType;
use App\Models\Product;

class SettingsController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::orderBy('sort_order')->get();
        $itemUnits  = ItemUnit::orderBy('name')->get();
        $drugTypes  = DrugType::orderBy('name_th')->get();

        return view('settings.index', compact('categories', 'itemUnits', 'drugTypes'));
    }

    // --- Product Categories ---
    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'code'       => 'nullable|string|max:20|unique:product_categories,code',
            'name'       => 'required|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        ProductCategory::create($data);
        return back()->with('success', 'เพิ่มประเภทสินค้าเรียบร้อยแล้ว')->with('active_tab', 'categories');
    }

    public function updateCategory(Request $request, ProductCategory $category)
    {
        $data = $request->validate([
            'code'       => 'nullable|string|max:20|unique:product_categories,code,' . $category->id,
            'name'       => 'required|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);
        $category->update($data);
        return back()->with('success', 'แก้ไขประเภทสินค้าเรียบร้อยแล้ว')->with('active_tab', 'categories');
    }

    public function toggleCategory(ProductCategory $category)
    {
        $category->update(['is_disabled' => !$category->is_disabled]);
        return back()->with('active_tab', 'categories');
    }

    // --- Item Units ---
    public function storeUnit(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:50',
            'multiply' => 'nullable|numeric|min:0.0001',
        ]);
        $data['multiply'] = $data['multiply'] ?? 1;
        ItemUnit::create($data);
        return back()->with('success', 'เพิ่มหน่วยนับเรียบร้อยแล้ว')->with('active_tab', 'units');
    }

    public function updateUnit(Request $request, ItemUnit $unit)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:50',
            'multiply' => 'nullable|numeric|min:0.0001',
        ]);
        $unit->update($data);
        return back()->with('success', 'แก้ไขหน่วยนับเรียบร้อยแล้ว')->with('active_tab', 'units');
    }

    public function deleteUnit(ItemUnit $unit)
    {
        $inUse = Product::where('unit_id', $unit->id)->exists();
        if ($inUse) {
            return back()->with('error', 'ไม่สามารถลบได้ มีสินค้าใช้หน่วยนี้อยู่')->with('active_tab', 'units');
        }
        $unit->delete();
        return back()->with('success', 'ลบหน่วยนับเรียบร้อยแล้ว')->with('active_tab', 'units');
    }

    // --- Drug Types ---
    public function storeDrugType(Request $request)
    {
        $data = $request->validate([
            'code'            => 'required|string|max:20|unique:drug_types,code',
            'name_th'         => 'required|string|max:100',
            'khor_yor_report' => 'nullable|integer',
        ]);
        DrugType::create($data);
        return back()->with('success', 'เพิ่มประเภทยาเรียบร้อยแล้ว')->with('active_tab', 'drugtypes');
    }

    public function updateDrugType(Request $request, DrugType $type)
    {
        $data = $request->validate([
            'code'            => 'required|string|max:20|unique:drug_types,code,' . $type->id,
            'name_th'         => 'required|string|max:100',
            'khor_yor_report' => 'nullable|integer',
        ]);
        $type->update($data);
        return back()->with('success', 'แก้ไขประเภทยาเรียบร้อยแล้ว')->with('active_tab', 'drugtypes');
    }

    public function toggleDrugType(DrugType $type)
    {
        $type->update(['is_disabled' => !$type->is_disabled]);
        return back()->with('active_tab', 'drugtypes');
    }
}