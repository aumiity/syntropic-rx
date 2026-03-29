@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-slate-50 p-4">
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow-md p-5">
        <h1 class="text-2xl font-bold mb-4">แก้ไขผู้จำหน่าย</h1>

        @if($errors->any())
            <div class="mb-3 p-3 text-rose-800 bg-rose-100 border border-rose-300 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="space-y-3">
            @csrf
            @method('PUT')
            <label>รหัส
                <input type="text" name="code" value="{{ old('code', $supplier->code) }}" class="w-full mt-1 rounded border border-slate-300 p-2">
            </label>
            <label>ชื่อ
                <input type="text" name="name" value="{{ old('name', $supplier->name) }}" class="w-full mt-1 rounded border border-slate-300 p-2" required>
            </label>
            <label>Tax ID
                <input type="text" name="tax_id" value="{{ old('tax_id', $supplier->tax_id) }}" class="w-full mt-1 rounded border border-slate-300 p-2">
            </label>
            <label>โทรศัพท์
                <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" class="w-full mt-1 rounded border border-slate-300 p-2">
            </label>
            <label>ที่อยู่
                <textarea name="address" class="w-full mt-1 rounded border border-slate-300 p-2">{{ old('address', $supplier->address) }}</textarea>
            </label>
            <label>ชื่อผู้ติดต่อ
                <input type="text" name="contact_name" value="{{ old('contact_name', $supplier->contact_name) }}" class="w-full mt-1 rounded border border-slate-300 p-2">
            </label>
            <label class="inline-flex items-center gap-2 mt-2">
                <input type="checkbox" name="is_disabled" {{ old('is_disabled', $supplier->is_disabled) ? 'checked' : '' }}>
                ปิดใช้งาน
            </label>

            <div class="flex justify-end gap-2">
                <a href="{{ route('suppliers.index') }}" class="px-4 py-2 bg-slate-200 rounded hover:bg-slate-300">ยกเลิก</a>
                <button class="px-4 py-2 bg-emerald-500 text-white rounded hover:bg-emerald-600">บันทึก</button>
            </div>
        </form>
    </div>
</div>
@endsection
