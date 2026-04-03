@extends('layouts.app')

@section('content')
<div class="p-6 w-full mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">ตั้งค่าร้าน</h1>
        <p class="text-sm text-gray-500 mt-1">จัดการข้อมูลพื้นฐานของร้านค้าและการติดต่อ</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <form action="{{ route('settings.shop.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- ชื่อร้าน -->
            <div>
                <label for="shop_name" class="block text-sm font-medium text-gray-700 mb-2">ชื่อร้าน</label>
                <input 
                    type="text"
                    id="shop_name"
                    name="shop_name"
                    maxlength="200"
                    value="{{ old('shop_name', $setting->shop_name) }}"
                    placeholder="เช่น ร้านยา สัตร์โทษ"
                    class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400"
                >
                @error('shop_name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- ที่อยู่ -->
            <div>
                <label for="shop_address" class="block text-sm font-medium text-gray-700 mb-2">ที่อยู่</label>
                <textarea 
                    id="shop_address"
                    name="shop_address"
                    rows="3"
                    maxlength="5000"
                    placeholder="เช่น 123 ถนนดินแดง แขวงดินแดง เขตดินแดง กรุงเทพฯ"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 resize-none"
                >{{ old('shop_address', $setting->shop_address) }}</textarea>
                @error('shop_address')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- เบอร์โทรศัพท์ -->
            <div>
                <label for="shop_phone" class="block text-sm font-medium text-gray-700 mb-2">เบอร์โทรศัพท์</label>
                <input 
                    type="tel"
                    id="shop_phone"
                    name="shop_phone"
                    maxlength="50"
                    value="{{ old('shop_phone', $setting->shop_phone) }}"
                    placeholder="เช่น 02-123-4567"
                    class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400"
                >
                @error('shop_phone')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- เลขใบอนุญาต -->
            <div>
                <label for="shop_license_no" class="block text-sm font-medium text-gray-700 mb-2">เลขใบอนุญาต</label>
                <input 
                    type="text"
                    id="shop_license_no"
                    name="shop_license_no"
                    maxlength="100"
                    value="{{ old('shop_license_no', $setting->shop_license_no) }}"
                    placeholder="เช่น แว 123456"
                    class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400"
                >
                @error('shop_license_no')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Line ID -->
            <div>
                <label for="shop_line_id" class="block text-sm font-medium text-gray-700 mb-2">Line ID</label>
                <input 
                    type="text"
                    id="shop_line_id"
                    name="shop_line_id"
                    maxlength="100"
                    value="{{ old('shop_line_id', $setting->shop_line_id) }}"
                    placeholder="เช่น @syntropicrx"
                    class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400"
                >
                @error('shop_line_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- เลขประจำตัวผู้เสียภาษี -->
            <div>
                <label for="shop_tax_id" class="block text-sm font-medium text-gray-700 mb-2">เลขประจำตัวผู้เสียภาษี</label>
                <input 
                    type="text"
                    id="shop_tax_id"
                    name="shop_tax_id"
                    maxlength="20"
                    value="{{ old('shop_tax_id', $setting->shop_tax_id) }}"
                    placeholder="เช่น 1234567890123"
                    class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400"
                >
                @error('shop_tax_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button 
                    type="button" 
                    onclick="window.history.back()"
                    class="px-6 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 font-medium transition">
                    ยกเลิก
                </button>
                <button 
                    type="submit"
                    class="px-6 py-2.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    บันทึกการตั้งค่า
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
