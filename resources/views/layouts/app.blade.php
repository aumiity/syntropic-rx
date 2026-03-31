<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syntropic Rx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <div class="w-20 bg-emerald-700 flex flex-col items-center py-4 gap-1 flex-shrink-0">

        {{-- Logo --}}
        <div class="text-white font-bold text-xs text-center mb-4 leading-tight">
            <div class="text-lg">Rx</div>
            <div class="text-emerald-300">Syntropic</div>
        </div>

        {{-- Menu Items --}}
        @php
        $menus = [
            ['url' => '/pos',      'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'label' => 'การขาย'],
            ['url' => '/purchase', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'label' => 'รับสินค้า'],
            ['url' => '/products', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'label' => 'สินค้า'],
            ['url' => '/customers','icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'ลูกค้า'],
            ['url' => '/reports',  'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'label' => 'รายงาน'],
            ['url' => '/settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'ตั้งค่า'],
        ];
        @endphp

        @foreach($menus as $menu)
        <a href="{{ $menu['url'] }}"
           class="w-16 h-16 rounded-xl flex flex-col items-center justify-center gap-1 text-emerald-200 hover:bg-emerald-600 hover:text-white transition-colors
           {{ request()->is(ltrim($menu['url'],'/') . '*') ? 'bg-emerald-600 text-white' : '' }}">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $menu['icon'] }}"/>
            </svg>
            <span class="text-[10px] font-medium">{{ $menu['label'] }}</span>
        </a>
        @endforeach

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- User --}}
        <div class="w-16 h-16 rounded-xl flex flex-col items-center justify-center gap-1 text-emerald-300">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="text-[10px]">Admin</span>
        </div>

    </div>

    {{-- Content --}}
    <div class="flex-1 flex flex-col overflow-auto">
        @yield('content')
    {{-- Toast Blade blocks --}}
    @if(session('success'))
        <div id="toast-data" data-type="success"
             data-message="{{ session('success') }}"></div>
    @endif
    @if(session('error'))
        <div id="toast-data" data-type="error"
             data-message="{{ session('error') }}"></div>
    @endif

    <div id="toast-container" class="fixed top-5 left-1/2 -translate-x-1/2 z-50
         flex flex-col gap-2 items-center pointer-events-none"></div>
    </div>

</div>


<script>
function showToast(message, type = 'success') {
    const colors = {
        success: 'bg-emerald-500 text-white',
        error:   'bg-red-500 text-white',
        warning: 'bg-amber-400 text-gray-900',
    };
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `pointer-events-auto min-h-[48px] px-5 py-3 rounded-xl
        shadow-lg text-sm font-medium flex items-center gap-3
        transition-all duration-300 opacity-0 translate-y-[-10px]
        ${colors[type] || colors.success}`;
    toast.innerHTML = `<span>${message}</span>
        <button onclick="this.parentElement.remove()"
                class="ml-2 font-bold text-lg leading-none opacity-70
                       hover:opacity-100">×</button>`;
    container.appendChild(toast);
    requestAnimationFrame(() => {
        toast.classList.remove('opacity-0', 'translate-y-[-10px]');
        toast.classList.add('opacity-100', 'translate-y-0');
    });
    setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Auto-show toast from session
document.addEventListener('DOMContentLoaded', () => {
    const data = document.getElementById('toast-data');
    if (data) {
        showToast(data.dataset.message, data.dataset.type);
    }
});
</script>

<script>
// ฟังก์ชัน show/hide error ต่อ field
function showFieldError(field, message) {
    field.classList.remove('border-gray-300');
    field.classList.add('border-red-400');
    let err = field.parentElement.querySelector('.field-error');
    if (!err) {
        err = document.createElement('p');
        err.className = 'field-error text-red-500 text-xs mt-1';
        field.parentElement.appendChild(err);
    }
    err.textContent = message;
}

function clearFieldError(field) {
    field.classList.remove('border-red-400');
    field.classList.add('border-gray-300');
    const err = field.parentElement.querySelector('.field-error');
    if (err) err.remove();
}

// Real-time validation: blur + input events
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-required="true"]').forEach(field => {
        field.addEventListener('blur', () => {
            if (!field.value || field.value.trim() === '' || field.value === '0') {
                showFieldError(field, field.dataset.errorMsg || 'กรุณากรอกข้อมูล');
            }
        });
        field.addEventListener('input', () => {
            if (field.value && field.value.trim() !== '') {
                clearFieldError(field);
            }
        });
        field.addEventListener('change', () => {
            if (field.value && field.value.trim() !== '') {
                clearFieldError(field);
            }
        });
    });
});
</script>

</body>
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach(form => {
        // Skip forms that opt-out
        if (form.dataset.noValidate) return;

        form.addEventListener('submit', (e) => {
            const requiredFields = form.querySelectorAll('[data-required="true"]');
            let hasError = false;
            let firstErrorField = null;

            requiredFields.forEach(field => {
                if (!field.value || field.value.trim() === '') {
                    showFieldError(field, field.dataset.errorMsg || 'กรุณากรอกข้อมูล');
                    hasError = true;
                    if (!firstErrorField) firstErrorField = field;
                }
            });

            if (hasError) {
                e.preventDefault();
                showToast('กรุณากรอกข้อมูลให้ครบถ้วน', 'warning');

                // Auto switch tab if field is inside hidden tab
                if (firstErrorField) {
                    const tabPanel = firstErrorField.closest('.tab-panel');
                    if (tabPanel) {
                        const tabId = tabPanel.id;
                        const tabBtn = document.querySelector(
                            `[data-tab="${tabId}"]`
                        );
                        if (tabBtn) tabBtn.click();
                    }
                    setTimeout(() => firstErrorField.focus(), 300);
                }
            }
        });
    });
});
</script>
</html>
