@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-slate-50 p-4">
    <div class="max-w-full mx-auto">
    <div class="mb-5">
        <h1 class="text-xl font-semibold text-gray-800">รายงาน</h1>
        <p class="text-sm text-gray-400 mt-0.5">รายงานข้อมูลของระบบทั้งหมด</p>
    </div>
        {{-- Reports Tab Bar --}}
        <div class="mb-4 border-b border-slate-200">
            <nav class="-mb-px flex gap-1">
                <a href="{{ route('reports.sales') }}"
                   class="px-5 py-2.5 border-b-2 text-sm font-medium transition-colors whitespace-nowrap
                   {{ request()->routeIs('reports.sales*') 
                      ? 'border-emerald-500 text-emerald-700 bg-emerald-50 rounded-t-lg' 
                      : 'border-transparent text-slate-500 hover:text-emerald-600 hover:border-emerald-200' }}">
                    ประวัติการขาย
                </a>
                <a href="{{ route('reports.purchases') }}"
                   class="px-5 py-2.5 border-b-2 text-sm font-medium transition-colors whitespace-nowrap
                   {{ request()->routeIs('reports.purchases*')
                      ? 'border-emerald-500 text-emerald-700 bg-emerald-50 rounded-t-lg'
                      : 'border-transparent text-slate-500 hover:text-emerald-600 hover:border-emerald-200' }}">
                    ประวัติการรับสินค้า
                </a>
                {{-- เพิ่ม tab อื่นๆ ที่นี่ในอนาคต --}}
            </nav>
        </div>

        {{-- Tab Content --}}
        @yield('report_content')

    </div>
</div>
@endsection