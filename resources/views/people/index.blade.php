@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 p-4">
    <div class="max-w-full mx-auto bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-5">
            <h1 class="text-2xl font-bold text-slate-900">บุคคล</h1>
            <button type="button"
                    onclick="openCreateModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-emerald-500 text-white hover:bg-emerald-600 transition-colors">
                <span class="text-lg leading-none">+</span>
                <span>เพิ่ม</span>
            </button>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        @php
            $tabs = [
                'customers' => 'ลูกค้า',
                'suppliers' => 'ผู้จำหน่าย',
                'staff' => 'พนักงาน',
            ];
        @endphp

        <div class="mb-4 border-b border-gray-200">
            <nav class="-mb-px flex gap-2 overflow-x-auto" aria-label="Tabs">
                @foreach($tabs as $key => $label)
                    <a href="{{ route('people.index', ['tab' => $key]) }}"
                       class="whitespace-nowrap px-4 py-2.5 border-b-2 text-sm font-medium transition-colors {{ $tab === $key ? 'border-emerald-500 text-emerald-700 bg-emerald-50 rounded-t-lg' : 'border-transparent text-slate-600 hover:text-emerald-700 hover:border-emerald-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>

        <form method="GET" action="{{ route('people.index') }}" class="mb-4 flex gap-2">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาชื่อหรือเบอร์โทร..."
                class="flex-1 h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
            <button type="submit" class="px-4 h-10 rounded-lg bg-emerald-500 text-white text-sm">ค้นหา</button>
            @if(request('q'))
                <a href="{{ route('people.index', ['tab' => $tab]) }}" class="px-4 h-10 rounded-lg border border-gray-200 text-sm text-gray-600 flex items-center">ล้าง</a>
            @endif
        </form>
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            @if($tab === 'customers')
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-slate-700">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">รหัส</th>
                            <th class="px-3 py-2 text-left font-semibold">ชื่อ-นามสกุล</th>
                            <th class="px-3 py-2 text-left font-semibold">เบอร์โทร</th>
                            <th class="px-3 py-2 text-left font-semibold">HN</th>
                            <th class="px-3 py-2 text-left font-semibold">วันเกิด</th>
                            <th class="px-3 py-2 text-left font-semibold">แจ้งเตือน</th>
                            <th class="px-3 py-2 text-left font-semibold">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($customers as $customer)
                            <tr class="bg-white">
                                <td class="px-3 py-2">{{ $customer->code ?: '-' }}</td>
                                <td class="px-3 py-2">{{ $customer->full_name }}</td>
                                <td class="px-3 py-2">{{ $customer->phone ?: '-' }}</td>
                                <td class="px-3 py-2">{{ $customer->hn ?: '-' }}</td>
                                <td class="px-3 py-2">{{ optional($customer->dob)->format('d/m/Y') ?: '-' }}</td>
                                <td class="px-3 py-2">
                                    @if($customer->is_alert)
                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700">มีแจ้งเตือน</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500">ปกติ</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                class="inline-flex items-center justify-center rounded-lg border border-gray-300 p-2 text-gray-700 hover:bg-gray-50"
                                                onclick="openEditCustomerModal(this)"
                                                data-id="{{ $customer->id }}"
                                                data-code="{{ $customer->code }}"
                                                data-full_name="{{ $customer->full_name }}"
                                                data-id_card="{{ $customer->id_card }}"
                                                data-hn="{{ $customer->hn }}"
                                                data-dob="{{ $customer->dob ? $customer->dob->format('Y-m-d') : '' }}"
                                                data-phone="{{ $customer->phone }}"
                                                data-address="{{ $customer->address }}"
                                                data-food_allergy="{{ $customer->food_allergy }}"
                                                data-other_allergy="{{ $customer->other_allergy }}"
                                                data-chronic_diseases="{{ $customer->chronic_diseases }}"
                                                data-is_alert="{{ $customer->is_alert ? '1' : '0' }}"
                                                data-alert_note="{{ $customer->alert_note }}"
                                                data-warning_note="{{ $customer->warning_note }}">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 013.182 3.182L8.25 18.463 3 21l2.537-5.25L16.862 3.487z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('people.customers.destroy', $customer) }}" onsubmit="return confirm('ยืนยันลบลูกค้า?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-lg border border-red-200 px-3 py-1.5 text-red-600 hover:bg-red-50">ลบ</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-6 text-center text-slate-500">ยังไม่มีข้อมูลลูกค้า</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @elseif($tab === 'suppliers')
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-slate-700">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">รหัส</th>
                            <th class="px-3 py-2 text-left font-semibold">ชื่อ</th>
                            <th class="px-3 py-2 text-left font-semibold">ผู้ติดต่อ</th>
                            <th class="px-3 py-2 text-left font-semibold">เบอร์โทร</th>
                            <th class="px-3 py-2 text-left font-semibold">สถานะ</th>
                            <th class="px-3 py-2 text-left font-semibold">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($suppliers as $supplier)
                            <tr class="bg-white">
                                <td class="px-3 py-2">{{ $supplier->code ?: '-' }}</td>
                                <td class="px-3 py-2">{{ $supplier->name }}</td>
                                <td class="px-3 py-2">{{ $supplier->contact_name ?: '-' }}</td>
                                <td class="px-3 py-2">{{ $supplier->phone ?: '-' }}</td>
                                <td class="px-3 py-2">
                                    @if($supplier->is_disabled)
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">ปิดใช้งาน</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">ใช้งาน</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                class="inline-flex items-center justify-center rounded-lg border border-gray-300 p-2 text-gray-700 hover:bg-gray-50"
                                                onclick="openEditSupplierModal(this)"
                                                data-id="{{ $supplier->id }}"
                                                data-code="{{ $supplier->code }}"
                                                data-name="{{ $supplier->name }}"
                                                data-tax_id="{{ $supplier->tax_id }}"
                                                data-phone="{{ $supplier->phone }}"
                                                data-address="{{ $supplier->address }}"
                                                data-contact_name="{{ $supplier->contact_name }}"
                                                data-is_disabled="{{ $supplier->is_disabled ? '1' : '0' }}">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 013.182 3.182L8.25 18.463 3 21l2.537-5.25L16.862 3.487z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('people.suppliers.destroy', $supplier) }}" onsubmit="return confirm('ยืนยันลบผู้จำหน่าย?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-lg border border-red-200 px-3 py-1.5 text-red-600 hover:bg-red-50">ลบ</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-6 text-center text-slate-500">ยังไม่มีข้อมูลผู้จำหน่าย</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-slate-700">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">ชื่อ</th>
                            <th class="px-3 py-2 text-left font-semibold">Email</th>
                            <th class="px-3 py-2 text-left font-semibold">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($staff as $user)
                            <tr class="bg-white">
                                <td class="px-3 py-2">{{ $user->name }}</td>
                                <td class="px-3 py-2">{{ $user->email }}</td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                class="inline-flex items-center justify-center rounded-lg border border-gray-300 p-2 text-gray-700 hover:bg-gray-50"
                                                onclick="openEditStaffModal(this)"
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}"
                                                data-email="{{ $user->email }}">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 013.182 3.182L8.25 18.463 3 21l2.537-5.25L16.862 3.487z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('people.staff.destroy', $user) }}" onsubmit="return confirm('ยืนยันลบพนักงาน?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-lg border border-red-200 px-3 py-1.5 text-red-600 hover:bg-red-50">ลบ</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-6 text-center text-slate-500">ยังไม่มีข้อมูลพนักงาน</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>

        <div class="mt-4">
            @if($tab === 'customers')
                {{ $customers->appends(['tab' => 'customers'])->links() }}
            @elseif($tab === 'suppliers')
                {{ $suppliers->appends(['tab' => 'suppliers'])->links() }}
            @else
                {{ $staff->appends(['tab' => 'staff'])->links() }}
            @endif
        </div>
    </div>
</div>

{{-- Create Customer Modal --}}
<div id="modal-create-customer" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4">
    <div class="w-full max-w-3xl rounded-xl bg-white p-5">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold">เพิ่มลูกค้า</h2>
            <button type="button" class="text-gray-500" onclick="closeModal('modal-create-customer')">✕</button>
        </div>
        <form method="POST" action="{{ route('people.customers.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <input type="text" name="code" value="{{ $nextCustomerCode }}" readonly class="rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-gray-500">
                <input type="text" name="full_name" placeholder="ชื่อ-นามสกุล" class="rounded-lg border border-gray-300 px-3 py-2" required>
                <input type="text" name="id_card" placeholder="เลขบัตรประชาชน" class="rounded-lg border border-gray-300 px-3 py-2">
                <input type="text" name="hn" placeholder="HN" class="rounded-lg border border-gray-300 px-3 py-2">
                <input type="date" name="dob" class="rounded-lg border border-gray-300 px-3 py-2">
                <input type="text" name="phone" placeholder="เบอร์โทร" class="rounded-lg border border-gray-300 px-3 py-2">
            </div>
            <textarea name="address" rows="2" placeholder="ที่อยู่" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
            <textarea name="food_allergy" rows="2" placeholder="แพ้อาหาร" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
            <textarea name="other_allergy" rows="2" placeholder="แพ้อื่นๆ" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
            <textarea name="chronic_diseases" rows="2" placeholder="โรคประจำตัว" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="is_alert" value="1" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                แจ้งเตือน
            </label>
            <textarea name="alert_note" rows="2" placeholder="หมายเหตุแจ้งเตือน" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
            <textarea name="warning_note" rows="2" placeholder="คำเตือนเพิ่มเติม" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" class="rounded-lg border border-gray-300 px-4 py-2" onclick="closeModal('modal-create-customer')">ยกเลิก</button>
                <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2 text-white hover:bg-emerald-600">บันทึก</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Customer Modal --}}
<div id="modal-edit-customer" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4">
    <div class="w-full max-w-3xl rounded-xl bg-white p-5">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold">แก้ไขลูกค้า</h2>
            <button type="button" class="text-gray-500" onclick="closeModal('modal-edit-customer')">✕</button>
        </div>
        <form id="form-edit-customer" method="POST" action="#" class="space-y-3">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <input id="edit-customer-code" type="text" placeholder="รหัส" disabled class="rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-gray-500">
                <input id="edit-customer-full_name" type="text" name="full_name" placeholder="ชื่อ-นามสกุล" class="rounded-lg border border-gray-300 px-3 py-2" required>
                <input id="edit-customer-id_card" type="text" name="id_card" placeholder="เลขบัตรประชาชน" class="rounded-lg border border-gray-300 px-3 py-2">
                <input id="edit-customer-hn" type="text" name="hn" placeholder="HN" class="rounded-lg border border-gray-300 px-3 py-2">
                <input id="edit-customer-dob" type="date" name="dob" class="rounded-lg border border-gray-300 px-3 py-2">
                <input id="edit-customer-phone" type="text" name="phone" placeholder="เบอร์โทร" class="rounded-lg border border-gray-300 px-3 py-2">
            </div>
            <textarea id="edit-customer-address" name="address" rows="2" placeholder="ที่อยู่" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
            <textarea id="edit-customer-food_allergy" name="food_allergy" rows="2" placeholder="แพ้อาหาร" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
            <textarea id="edit-customer-other_allergy" name="other_allergy" rows="2" placeholder="แพ้อื่นๆ" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
            <textarea id="edit-customer-chronic_diseases" name="chronic_diseases" rows="2" placeholder="โรคประจำตัว" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input id="edit-customer-is_alert" type="checkbox" name="is_alert" value="1" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                แจ้งเตือน
            </label>
            <textarea id="edit-customer-alert_note" name="alert_note" rows="2" placeholder="หมายเหตุแจ้งเตือน" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
            <textarea id="edit-customer-warning_note" name="warning_note" rows="2" placeholder="คำเตือนเพิ่มเติม" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" class="rounded-lg border border-gray-300 px-4 py-2" onclick="closeModal('modal-edit-customer')">ยกเลิก</button>
                <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2 text-white hover:bg-emerald-600">บันทึก</button>
            </div>
        </form>
    </div>
</div>

{{-- Create Supplier Modal --}}
<div id="modal-create-supplier" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4">
    <div class="w-full max-w-2xl rounded-xl bg-white p-5">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold">เพิ่มผู้จำหน่าย</h2>
            <button type="button" class="text-gray-500" onclick="closeModal('modal-create-supplier')">✕</button>
        </div>
        <form method="POST" action="{{ route('people.suppliers.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <input type="text" name="code" value="{{ $nextSupplierCode }}" readonly class="rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-gray-500">
                <input type="text" name="name" placeholder="ชื่อ" class="rounded-lg border border-gray-300 px-3 py-2" required>
                <input type="text" name="tax_id" placeholder="เลขผู้เสียภาษี" class="rounded-lg border border-gray-300 px-3 py-2">
                <input type="text" name="phone" placeholder="เบอร์โทร" class="rounded-lg border border-gray-300 px-3 py-2">
                <input type="text" name="contact_name" placeholder="ผู้ติดต่อ" class="rounded-lg border border-gray-300 px-3 py-2 md:col-span-2">
            </div>
            <textarea name="address" rows="3" placeholder="ที่อยู่" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" class="rounded-lg border border-gray-300 px-4 py-2" onclick="closeModal('modal-create-supplier')">ยกเลิก</button>
                <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2 text-white hover:bg-emerald-600">บันทึก</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Supplier Modal --}}
<div id="modal-edit-supplier" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4">
    <div class="w-full max-w-2xl rounded-xl bg-white p-5">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold">แก้ไขผู้จำหน่าย</h2>
            <button type="button" class="text-gray-500" onclick="closeModal('modal-edit-supplier')">✕</button>
        </div>
        <form id="form-edit-supplier" method="POST" action="#" class="space-y-3">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <input id="edit-supplier-code" type="text" placeholder="รหัส" disabled class="rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-gray-500">
                <input id="edit-supplier-name" type="text" name="name" placeholder="ชื่อ" class="rounded-lg border border-gray-300 px-3 py-2" required>
                <input id="edit-supplier-tax_id" type="text" name="tax_id" placeholder="เลขผู้เสียภาษี" class="rounded-lg border border-gray-300 px-3 py-2">
                <input id="edit-supplier-phone" type="text" name="phone" placeholder="เบอร์โทร" class="rounded-lg border border-gray-300 px-3 py-2">
                <input id="edit-supplier-contact_name" type="text" name="contact_name" placeholder="ผู้ติดต่อ" class="rounded-lg border border-gray-300 px-3 py-2 md:col-span-2">
            </div>
            <textarea id="edit-supplier-address" name="address" rows="3" placeholder="ที่อยู่" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input id="edit-supplier-is_disabled" type="checkbox" name="is_disabled" value="1" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                ปิดใช้งาน
            </label>
            <div class="flex justify-end gap-2">
                <button type="button" class="rounded-lg border border-gray-300 px-4 py-2" onclick="closeModal('modal-edit-supplier')">ยกเลิก</button>
                <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2 text-white hover:bg-emerald-600">บันทึก</button>
            </div>
        </form>
    </div>
</div>

{{-- Create Staff Modal --}}
<div id="modal-create-staff" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4">
    <div class="w-full max-w-xl rounded-xl bg-white p-5">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold">เพิ่มพนักงาน</h2>
            <button type="button" class="text-gray-500" onclick="closeModal('modal-create-staff')">✕</button>
        </div>
        <form method="POST" action="{{ route('people.staff.store') }}" class="space-y-3">
            @csrf
            <input type="text" name="name" placeholder="ชื่อ" class="w-full rounded-lg border border-gray-300 px-3 py-2" required>
            <input type="email" name="email" placeholder="Email" class="w-full rounded-lg border border-gray-300 px-3 py-2" required>
            <input type="password" name="password" placeholder="รหัสผ่าน (อย่างน้อย 8 ตัว)" class="w-full rounded-lg border border-gray-300 px-3 py-2" required>
            <div class="flex justify-end gap-2">
                <button type="button" class="rounded-lg border border-gray-300 px-4 py-2" onclick="closeModal('modal-create-staff')">ยกเลิก</button>
                <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2 text-white hover:bg-emerald-600">บันทึก</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Staff Modal --}}
<div id="modal-edit-staff" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4">
    <div class="w-full max-w-xl rounded-xl bg-white p-5">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold">แก้ไขพนักงาน</h2>
            <button type="button" class="text-gray-500" onclick="closeModal('modal-edit-staff')">✕</button>
        </div>
        <form id="form-edit-staff" method="POST" action="#" class="space-y-3">
            @csrf
            @method('PUT')
            <input id="edit-staff-name" type="text" name="name" placeholder="ชื่อ" class="w-full rounded-lg border border-gray-300 px-3 py-2" required>
            <input id="edit-staff-email" type="email" name="email" placeholder="Email" class="w-full rounded-lg border border-gray-300 px-3 py-2" required>
            <input type="password" name="password" placeholder="รหัสผ่านใหม่ (เว้นว่างได้)" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <div class="flex justify-end gap-2">
                <button type="button" class="rounded-lg border border-gray-300 px-4 py-2" onclick="closeModal('modal-edit-staff')">ยกเลิก</button>
                <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2 text-white hover:bg-emerald-600">บันทึก</button>
            </div>
        </form>
    </div>
</div>

<script>
const activeTab = @json($tab);

function showModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('hidden');
    el.classList.add('flex');
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('flex');
    el.classList.add('hidden');
}

function openCreateModal() {
    if (activeTab === 'customers') {
        showModal('modal-create-customer');
    } else if (activeTab === 'suppliers') {
        showModal('modal-create-supplier');
    } else {
        showModal('modal-create-staff');
    }
}

function openEditCustomerModal(button) {
    const data = button.dataset;
    document.getElementById('form-edit-customer').action = `/people/customers/${data.id}`;
    document.getElementById('edit-customer-code').value = data.code || '';
    document.getElementById('edit-customer-full_name').value = data.full_name || '';
    document.getElementById('edit-customer-id_card').value = data.id_card || '';
    document.getElementById('edit-customer-hn').value = data.hn || '';
    document.getElementById('edit-customer-dob').value = data.dob || '';
    document.getElementById('edit-customer-phone').value = data.phone || '';
    document.getElementById('edit-customer-address').value = data.address || '';
    document.getElementById('edit-customer-food_allergy').value = data.food_allergy || '';
    document.getElementById('edit-customer-other_allergy').value = data.other_allergy || '';
    document.getElementById('edit-customer-chronic_diseases').value = data.chronic_diseases || '';
    document.getElementById('edit-customer-is_alert').checked = data.is_alert === '1';
    document.getElementById('edit-customer-alert_note').value = data.alert_note || '';
    document.getElementById('edit-customer-warning_note').value = data.warning_note || '';
    showModal('modal-edit-customer');
}

function openEditSupplierModal(button) {
    const data = button.dataset;
    document.getElementById('form-edit-supplier').action = `/people/suppliers/${data.id}`;
    document.getElementById('edit-supplier-code').value = data.code || '';
    document.getElementById('edit-supplier-name').value = data.name || '';
    document.getElementById('edit-supplier-tax_id').value = data.tax_id || '';
    document.getElementById('edit-supplier-phone').value = data.phone || '';
    document.getElementById('edit-supplier-address').value = data.address || '';
    document.getElementById('edit-supplier-contact_name').value = data.contact_name || '';
    document.getElementById('edit-supplier-is_disabled').checked = data.is_disabled === '1';
    showModal('modal-edit-supplier');
}

function openEditStaffModal(button) {
    const data = button.dataset;
    document.getElementById('form-edit-staff').action = `/people/staff/${data.id}`;
    document.getElementById('edit-staff-name').value = data.name || '';
    document.getElementById('edit-staff-email').value = data.email || '';
    showModal('modal-edit-staff');
}

window.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeModal('modal-create-customer');
        closeModal('modal-edit-customer');
        closeModal('modal-create-supplier');
        closeModal('modal-edit-supplier');
        closeModal('modal-create-staff');
        closeModal('modal-edit-staff');
    }
});
</script>
@endsection
