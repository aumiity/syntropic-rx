<?php

namespace App\Http\Controllers;

use App\Models\LabelSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class LabelSettingController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => LabelSetting::current(),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        if ($request->input('font_family') === 'GoogleSans') {
            $request->merge(['font_family' => 'Google Sans']);
        }

        $rowStyleKeys = [
            'shop_name',
            'date',
            'address',
            'product_name',
            'dosage_line1',
            'dosage_line2',
            'indication',
            'advice',
        ];

        $rules = [
            'paper_width' => 'required|integer|min:50|max:200',
            'paper_height' => 'required|integer|min:50|max:200',
            'padding_top' => 'required|integer|min:0|max:20',
            'padding_right' => 'required|integer|min:0|max:20',
            'padding_bottom' => 'required|integer|min:0|max:20',
            'padding_left' => 'required|integer|min:0|max:20',
            'font_family' => 'required|string|in:Google Sans,Tahoma,Sarabun,Arial,Courier New',
            'bold_shop' => 'required|boolean',
            'bold_product' => 'required|boolean',
            'bold_dosage' => 'required|boolean',
            'row_styles' => 'required|array',
            'row_styles.divider.marginTop' => 'required|integer|min:-50|max:100',
            'row_styles.barcode.marginTop' => 'required|integer|min:-50|max:100',
        ];

        foreach ($rowStyleKeys as $key) {
            $rules["row_styles.{$key}.fontSize"] = 'required|integer|min:6|max:40';
            $rules["row_styles.{$key}.bold"] = 'required|boolean';
            $rules["row_styles.{$key}.italic"] = 'required|boolean';
            $rules["row_styles.{$key}.underline"] = 'required|boolean';
            $rules["row_styles.{$key}.align"] = 'required|in:left,center,right';
            $rules["row_styles.{$key}.marginTop"] = 'required|integer|min:-50|max:100';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ข้อมูลการตั้งค่าฉลากไม่ถูกต้อง',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $validated = $validator->validated();
            $labelSetting = LabelSetting::current();
            $labelSetting->update($validated);

            return response()->json([
                'success' => true,
                'data' => $labelSetting->fresh(),
                'message' => 'บันทึกการตั้งค่าฉลากเรียบร้อยแล้ว',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการบันทึกการตั้งค่าฉลาก',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
