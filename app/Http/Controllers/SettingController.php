<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        $setting = Setting::get();
        return view('shop-settings.index', ['setting' => $setting]);
    }

    /**
     * Update the settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'shop_name' => 'nullable|string|max:200',
            'shop_address' => 'nullable|string|max:5000',
            'shop_phone' => 'nullable|string|max:50',
            'shop_license_no' => 'nullable|string|max:100',
            'shop_line_id' => 'nullable|string|max:100',
            'shop_tax_id' => 'nullable|string|max:20',
        ]);

        $setting = Setting::get();
        $setting->update($validated);

        return redirect()->back()->with('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
    }
}
