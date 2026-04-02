<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductUnitController extends Controller
{
    public function store(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'unit_name' => 'required|string|max:50',
            'qty_per_base' => 'required|numeric|min:0.0001',
            'barcode' => 'nullable|string|max:50|unique:product_units,barcode',
            'price_retail' => 'nullable|numeric|min:0',
            'is_for_sale' => 'nullable|boolean',
            'is_for_purchase' => 'nullable|boolean',
        ]);

        $data['is_for_sale'] = $request->boolean('is_for_sale');
        $data['is_for_purchase'] = $request->boolean('is_for_purchase');
        $data['is_disabled'] = false;
        $data['price_retail'] = $data['price_retail'] ?? 0;
        $data['price_wholesale1'] = $data['price_retail'];
        $data['price_wholesale2'] = $data['price_retail'];

        $unit = $product->productUnits()->create($data);

        return response()->json([
            'message' => 'เพิ่มหน่วยสินค้าเรียบร้อยแล้ว',
            'data' => $unit,
        ]);
    }

    public function update(Request $request, ProductUnit $unit): JsonResponse
    {
        $data = $request->validate([
            'unit_name' => 'required|string|max:50',
            'qty_per_base' => 'required|numeric|min:0.0001',
            'barcode' => 'nullable|string|max:50|unique:product_units,barcode,' . $unit->id,
            'price_retail' => 'nullable|numeric|min:0',
            'is_for_sale' => 'nullable|boolean',
            'is_for_purchase' => 'nullable|boolean',
        ]);

        $data['is_for_sale'] = $request->boolean('is_for_sale');
        $data['is_for_purchase'] = $request->boolean('is_for_purchase');
        $data['price_retail'] = $data['price_retail'] ?? 0;
        $data['price_wholesale1'] = $data['price_retail'];
        $data['price_wholesale2'] = $data['price_retail'];

        $unit->update($data);

        return response()->json([
            'message' => 'แก้ไขหน่วยสินค้าเรียบร้อยแล้ว',
            'data' => $unit,
        ]);
    }

    public function destroy(ProductUnit $unit): JsonResponse
    {
        $unit->delete();

        return response()->json([
            'message' => 'ลบหน่วยสินค้าเรียบร้อยแล้ว',
        ]);
    }

    public function toggleUnitDisabled(Product $product, ProductUnit $unit): JsonResponse
    {
        if ((int) $unit->product_id !== (int) $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบหน่วยสินค้าที่ต้องการ',
            ], 404);
        }

        $unit->is_disabled = !$unit->is_disabled;
        $unit->save();

        return response()->json([
            'success' => true,
            'is_disabled' => $unit->is_disabled,
        ]);
    }
}
