@extends('layouts.app')

@section('content')
<div class="p-6 w-full">

    <div class="mb-5">
        <h1 class="text-xl font-semibold text-gray-800">ตั้งค่าระบบ</h1>
        <p class="text-sm text-gray-400 mt-0.5">จัดการข้อมูลพื้นฐานของระบบ</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    @php $activeTab = session('active_tab', 'categories'); @endphp

    <!-- Tab Bar -->
    <div class="mb-6 flex gap-2 border-b border-gray-200">
        <button type="button" data-tab="categories" class="tab-btn min-h-11 px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 {{ $activeTab == 'categories' ? 'text-emerald-600 border-emerald-600' : 'text-gray-600 border-transparent hover:text-gray-800' }}">ประเภทสินค้า</button>
        <button type="button" data-tab="units"       class="tab-btn min-h-11 px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 {{ $activeTab == 'units'       ? 'text-emerald-600 border-emerald-600' : 'text-gray-600 border-transparent hover:text-gray-800' }}">หน่วยนับ</button>
        <button type="button" data-tab="drugtypes"   class="tab-btn min-h-11 px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 {{ $activeTab == 'drugtypes'   ? 'text-emerald-600 border-emerald-600' : 'text-gray-600 border-transparent hover:text-gray-800' }}">ประเภทยาตามกฎหมาย</button>
    </div>

    <!-- ==================== Tab: ประเภทสินค้า ==================== -->
    <div id="tab-categories" class="tab-panel {{ $activeTab == 'categories' ? '' : 'hidden' }}">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <form action="{{ route('settings.categories.store') }}" method="POST" class="flex gap-3 mb-5 pb-5 border-b border-gray-100">
                @csrf
                <input type="text" name="code" placeholder="รหัส (เช่น DRUG)" maxlength="20" class="w-32 h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                <input type="text" name="name" placeholder="ชื่อประเภทสินค้า *" required class="flex-1 h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                <input type="number" name="sort_order" placeholder="ลำดับ" min="0" class="w-20 h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                <button type="submit" class="h-10 px-5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium">+ เพิ่ม</button>
            </form>
            <table class="w-full text-sm">
                <thead><tr class="border-b border-gray-100 text-xs text-gray-500 uppercase">
                    <th class="py-2 text-left w-24">รหัส</th>
                    <th class="py-2 text-left">ชื่อประเภท</th>
                    <th class="py-2 text-center w-16">ลำดับ</th>
                    <th class="py-2 text-center w-24">สถานะ</th>
                    <th class="py-2 w-20"></th>
                </tr></thead>
                <tbody>
                @forelse($categories as $cat)
                <tr class="border-b border-gray-50 hover:bg-gray-50 {{ $cat->is_disabled ? 'opacity-40' : '' }}">
                    <td class="py-2.5 font-mono text-xs text-gray-500">{{ $cat->code }}</td>
                    <td class="py-2.5 text-gray-800 font-medium">{{ $cat->name }}</td>
                    <td class="py-2.5 text-center text-gray-400">{{ $cat->sort_order }}</td>
                    <td class="py-2.5 text-center">
                        <form action="{{ route('settings.categories.toggle', $cat) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs px-2 py-1 rounded-full {{ $cat->is_disabled ? 'bg-gray-100 text-gray-500' : 'bg-emerald-50 text-emerald-600' }}">
                                {{ $cat->is_disabled ? 'ปิด' : 'เปิด' }}
                            </button>
                        </form>
                    </td>
                    <td class="py-2.5 text-right">
                        <button type="button"
                            onclick="openEditModal('categories', {{ $cat->id }}, '{{ addslashes($cat->code) }}', '{{ addslashes($cat->name) }}', {{ $cat->sort_order }})"
                            class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600">แก้ไข</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-6 text-center text-gray-400 text-sm">ยังไม่มีข้อมูล</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==================== Tab: หน่วยนับ ==================== -->
    <div id="tab-units" class="tab-panel {{ $activeTab == 'units' ? '' : 'hidden' }}">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="mb-5 pb-5 border-b border-gray-100 text-sm text-gray-600">
                หน่วยนับถูกจัดการในระดับสินค้าแล้ว (จากตาราง product_units) กรุณาเพิ่ม/แก้ไขหน่วยที่หน้าแก้ไขสินค้า
            </div>
            <table class="w-full text-sm">
                <thead><tr class="border-b border-gray-100 text-xs text-gray-500 uppercase">
                    <th class="py-2 text-left">ชื่อหน่วย</th>
                    <th class="py-2 text-right w-28">จำนวนที่ใช้งาน</th>
                </tr></thead>
                <tbody>
                @forelse($itemUnits as $unit)
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="py-2.5 text-gray-800 font-medium">{{ $unit->unit_name }}</td>
                    <td class="py-2.5 text-right text-gray-500">{{ $unit->usage_count }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="py-6 text-center text-gray-400 text-sm">ยังไม่มีข้อมูล</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==================== Tab: ประเภทยาตามกฎหมาย ==================== -->
    <div id="tab-drugtypes" class="tab-panel {{ $activeTab == 'drugtypes' ? '' : 'hidden' }}">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <form action="{{ route('settings.drugtypes.store') }}" method="POST" class="flex gap-3 mb-5 pb-5 border-b border-gray-100">
                @csrf
                <input type="text" name="code" placeholder="รหัส *" required maxlength="20" class="w-32 h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                <input type="text" name="name_th" placeholder="ชื่อประเภทยา *" required class="flex-1 h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                <input type="number" name="khor_yor_report" placeholder="ขย.ที่" min="1" class="w-20 h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                <button type="submit" class="h-10 px-5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium">+ เพิ่ม</button>
            </form>
            <table class="w-full text-sm">
                <thead><tr class="border-b border-gray-100 text-xs text-gray-500 uppercase">
                    <th class="py-2 text-left w-28">รหัส</th>
                    <th class="py-2 text-left">ชื่อประเภทยา</th>
                    <th class="py-2 text-center w-20">ขย.ที่</th>
                    <th class="py-2 text-center w-24">สถานะ</th>
                    <th class="py-2 w-20"></th>
                </tr></thead>
                <tbody>
                @forelse($drugTypes as $type)
                <tr class="border-b border-gray-50 hover:bg-gray-50 {{ $type->is_disabled ? 'opacity-40' : '' }}">
                    <td class="py-2.5 font-mono text-xs text-gray-500">{{ $type->code }}</td>
                    <td class="py-2.5 text-gray-800 font-medium">{{ $type->name_th }}</td>
                    <td class="py-2.5 text-center text-gray-400">{{ $type->khor_yor_report ?? '-' }}</td>
                    <td class="py-2.5 text-center">
                        <form action="{{ route('settings.drugtypes.toggle', $type) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs px-2 py-1 rounded-full {{ $type->is_disabled ? 'bg-gray-100 text-gray-500' : 'bg-emerald-50 text-emerald-600' }}">
                                {{ $type->is_disabled ? 'ปิด' : 'เปิด' }}
                            </button>
                        </form>
                    </td>
                    <td class="py-2.5 text-right">
                        <button type="button"
                            onclick="openEditModal('drugtypes', {{ $type->id }}, '{{ addslashes($type->code) }}', '{{ addslashes($type->name_th) }}', {{ $type->khor_yor_report ?? 0 }})"
                            class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600">แก้ไข</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-6 text-center text-gray-400 text-sm">ยังไม่มีข้อมูล</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ==================== Edit Modal ==================== -->
<div id="edit-modal" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl">
        <h2 class="text-sm font-semibold text-gray-800 mb-4" id="modal-title">แก้ไขข้อมูล</h2>
        <form id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <div id="edit-fields" class="space-y-3 mb-5"></div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50">ยกเลิก</button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium">บันทึก</button>
            </div>
        </form>
    </div>
</div>

<script>
// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('text-emerald-600', 'border-emerald-600');
            b.classList.add('text-gray-600', 'border-transparent');
        });
        document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
        this.classList.add('text-emerald-600', 'border-emerald-600');
        this.classList.remove('text-gray-600', 'border-transparent');
    });
});

// Modal config
const routeMap = {
    categories: '/settings/categories/',
    drugtypes:  '/settings/drug-types/',
};

const titleMap = {
    categories: 'แก้ไขประเภทสินค้า',
    drugtypes:  'แก้ไขประเภทยาตามกฎหมาย',
};

const inputClass = 'w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400';

const fieldMap = {
    categories: (code, name, sort) => `
        <div><label class="block text-xs text-gray-500 mb-1">รหัส</label>
        <input type="text" name="code" value="${code}" maxlength="20" class="${inputClass}"></div>
        <div><label class="block text-xs text-gray-500 mb-1">ชื่อประเภทสินค้า *</label>
        <input type="text" name="name" value="${name}" required class="${inputClass}"></div>
        <div><label class="block text-xs text-gray-500 mb-1">ลำดับ</label>
        <input type="number" name="sort_order" value="${sort}" min="0" class="${inputClass}"></div>`,

    drugtypes: (code, name, sort) => `
        <div><label class="block text-xs text-gray-500 mb-1">รหัส *</label>
        <input type="text" name="code" value="${code}" required maxlength="20" class="${inputClass}"></div>
        <div><label class="block text-xs text-gray-500 mb-1">ชื่อประเภทยา *</label>
        <input type="text" name="name_th" value="${name}" required class="${inputClass}"></div>
        <div><label class="block text-xs text-gray-500 mb-1">ขย.ที่ (ถ้ามี)</label>
        <input type="number" name="khor_yor_report" value="${sort || ''}" min="1" class="${inputClass}"></div>`,
};

function openEditModal(type, id, code, name, sort) {
    document.getElementById('modal-title').textContent = titleMap[type];
    document.getElementById('edit-form').action = routeMap[type] + id;
    document.getElementById('edit-fields').innerHTML = fieldMap[type](code, name, sort);
    document.getElementById('edit-modal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}

// Close modal on backdrop click
document.getElementById('edit-modal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
@endsection