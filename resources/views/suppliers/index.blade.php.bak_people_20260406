@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-slate-50 p-4">
    <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-md p-5">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">ผู้จำหน่าย</h1>
            <a href="{{ route('suppliers.create') }}" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600">เพิ่มผู้จำหน่าย</a>
        </div>

        @if(session('success'))
            <div class="mb-3 p-3 text-green-800 bg-green-100 border border-green-300 rounded">{{ session('success') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full border border-slate-200">
                <thead class="bg-slate-100 text-left text-sm text-slate-700">
                    <tr>
                        <th class="px-3 py-2">ชื่อ</th>
                        <th class="px-3 py-2">รหัส</th>
                        <th class="px-3 py-2">เบอร์โทร</th>
                        <th class="px-3 py-2">สถานะ</th>
                        <th class="px-3 py-2">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $supplier)
                        <tr class="border-t border-slate-200">
                            <td class="px-3 py-2">{{ $supplier->name }}</td>
                            <td class="px-3 py-2">{{ $supplier->code }}</td>
                            <td class="px-3 py-2">{{ $supplier->phone }}</td>
                            <td class="px-3 py-2">{{ $supplier->is_disabled ? 'ปิดใช้งาน' : 'ใช้งาน' }}</td>
                            <td class="px-3 py-2">
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="text-blue-600 hover:text-blue-800 mr-2">แก้ไข</a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('ยืนยันลบผู้จำหน่าย?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800">ลบ</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $suppliers->links() }}</div>
    </div>
</div>
@endsection
