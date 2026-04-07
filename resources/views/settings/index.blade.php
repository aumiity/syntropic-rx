@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
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

    @php $activeTab = session('active_tab', 'shop'); @endphp

    <!-- Tab Bar -->
    <div class="mb-6 flex gap-2 border-b border-gray-200">
        <button type="button" data-tab="shop"        class="tab-btn min-h-11 px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 {{ $activeTab == 'shop'        ? 'text-emerald-600 border-emerald-600' : 'text-gray-600 border-transparent hover:text-gray-800' }}">ข้อมูลร้านค้า</button>
        <button type="button" data-tab="labels"      class="tab-btn min-h-11 px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 {{ $activeTab == 'labels'      ? 'text-emerald-600 border-emerald-600' : 'text-gray-600 border-transparent hover:text-gray-800' }}">ฉลากยา</button>
    </div>

    <!-- ==================== Tab: ข้อมูลร้านค้า ==================== -->
    <div id="tab-shop" class="tab-panel {{ $activeTab == 'shop' ? '' : 'hidden' }}">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <form action="{{ route('settings.shop.update') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="shop_name" class="block text-sm font-medium text-gray-700 mb-1">ชื่อร้าน</label>
                    <input type="text" name="shop_name" id="shop_name" maxlength="200" value="{{ old('shop_name', $setting->shop_name) }}"
                        class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                </div>

                <div>
                    <label for="shop_address" class="block text-sm font-medium text-gray-700 mb-1">ที่อยู่</label>
                    <textarea name="shop_address" id="shop_address" rows="3" maxlength="5000"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-emerald-400 resize-none">{{ old('shop_address', $setting->shop_address) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="shop_phone" class="block text-sm font-medium text-gray-700 mb-1">เบอร์โทร</label>
                        <input type="text" name="shop_phone" id="shop_phone" maxlength="50" value="{{ old('shop_phone', $setting->shop_phone) }}"
                            placeholder="เช่น 02-123-4567" class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label for="shop_license_no" class="block text-sm font-medium text-gray-700 mb-1">เลขใบอนุญาต</label>
                        <input type="text" name="shop_license_no" id="shop_license_no" maxlength="100" value="{{ old('shop_license_no', $setting->shop_license_no) }}"
                            placeholder="เช่น แว 123456" class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="shop_line_id" class="block text-sm font-medium text-gray-700 mb-1">ID LINE</label>
                        <input type="text" name="shop_line_id" id="shop_line_id" maxlength="100" value="{{ old('shop_line_id', $setting->shop_line_id) }}"
                            placeholder="เช่น @syntropicrx" class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <div>
                        <label for="shop_tax_id" class="block text-sm font-medium text-gray-700 mb-1">เลขประจำตัวผู้เสียภาษี</label>
                        <input type="text" name="shop_tax_id" id="shop_tax_id" maxlength="20" value="{{ old('shop_tax_id', $setting->shop_tax_id) }}"
                            placeholder="เช่น 1234567890123" class="w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100 mt-4">
                    <button type="button" onclick="window.location.reload()" class="px-4 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50">ยกเลิก</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium">บันทึกข้อมูลร้านค้า</button>
                </div>
            </form>
        </div>
    </div>


    <!-- ==================== Tab: ฉลากยา ==================== -->
    <div id="tab-labels" class="tab-panel {{ $activeTab == 'labels' ? '' : 'hidden' }}">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div id="label-settings-container">
                <div class="flex items-center justify-center h-32">
                    <span class="text-gray-400">กำลังโหลดการตั้งค่าฉลากยา...</span>
                </div>
                <div class="flex justify-end gap-3 mt-4">
                    <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium">บันทึก</button>
                </div>
            </div>
        </div>
    </div>

</div>


<script>
let currentLabelSettings = null;
let currentPreviewLabel = null;

function toBoolValue(value) {
    return value === true || value === 1 || value === '1' || value === 'on';
}

function clampFontSizeValue(value, fallback = 12) {
    const parsed = parseInt(value, 10);
    if (Number.isNaN(parsed)) return fallback;
    return Math.min(40, Math.max(6, parsed));
}

function clampMarginTopValue(value, fallback = 0) {
    const parsed = parseInt(value, 10);
    if (Number.isNaN(parsed)) return fallback;
    return Math.min(100, Math.max(-50, parsed));
}

function collectRowStylesFromFormData(formData, baseSettings) {
    const defaults = normalizeRowStyles(baseSettings?.row_styles);
    const result = {};

    ROW_STYLE_ORDER.forEach(({ key }) => {
        const fallback = defaults[key] || DEFAULT_ROW_STYLES[key];
        const alignRaw = formData.get(`row_styles[${key}][align]`) || fallback.align;
        result[key] = {
            fontSize: clampFontSizeValue(formData.get(`row_styles[${key}][fontSize]`), fallback.fontSize),
            bold: toBoolValue(formData.get(`row_styles[${key}][bold]`) ?? (fallback.bold ? '1' : '0')),
            italic: toBoolValue(formData.get(`row_styles[${key}][italic]`) ?? (fallback.italic ? '1' : '0')),
            underline: toBoolValue(formData.get(`row_styles[${key}][underline]`) ?? (fallback.underline ? '1' : '0')),
            align: ['left', 'center', 'right'].includes(alignRaw) ? alignRaw : fallback.align,
            marginTop: clampMarginTopValue(formData.get(`row_styles[${key}][marginTop]`), fallback.marginTop),
        };
    });

    return result;
}


// Ensure printSampleLabel is defined in global scope for event binding
function printSampleLabel() {
    const form = document.getElementById('label-settings-form') || document.querySelector('#label-settings-container form');
    if (!form || !currentLabelSettings || !currentPreviewLabel) {
        showToast('ยังโหลดข้อมูลฉลากไม่ครบ กรุณาลองใหม่อีกครั้ง', 'error');
        return;
    }

    const formData = new FormData(form);
    const s = {
        paper_width: parseInt(formData.get('paper_width') || currentLabelSettings.paper_width, 70),
        paper_height: parseInt(formData.get('paper_height') || currentLabelSettings.paper_height, 50),
        padding_top: parseInt(formData.get('padding_top') || currentLabelSettings.padding_top, 10),
        padding_right: parseInt(formData.get('padding_right') || currentLabelSettings.padding_right, 10),
        padding_bottom: parseInt(formData.get('padding_bottom') || currentLabelSettings.padding_bottom, 10),
        padding_left: parseInt(formData.get('padding_left') || currentLabelSettings.padding_left, 10),
        font_family: formData.get('font_family') || currentLabelSettings.font_family,
        line_spacing: parseFloat(formData.get('line_spacing') || currentLabelSettings.line_spacing),
        row_styles: collectRowStylesFromFormData(formData, currentLabelSettings),
    };

    // Build print HTML with print-specific layout (no preview frame/border/transform)
    const labelHtml = generateLabelPreview(currentPreviewLabel, s, s.paper_width * 3.78, { forPrint: true });

    const printWindow = window.open('', '', 'width=800,height=600');
    if (!printWindow) {
        showToast('เบราว์เซอร์บล็อกหน้าต่างพิมพ์ กรุณาอนุญาต pop-up แล้วลองใหม่', 'error');
        return;
    }

    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
            <style>
                @page {
                    size: ${s.paper_width}mm ${s.paper_height}mm;
                    margin: 0;
                }
                html, body {
                    width: ${s.paper_width}mm;
                    height: ${s.paper_height}mm;
                    margin: 0;
                    padding: 0;
                    overflow: hidden;
                }
                body {
                    font-family: '${s.font_family}', 'Google Sans', Arial, sans-serif;
                }
                .print-root {
                    width: ${s.paper_width}mm;
                    height: ${s.paper_height}mm;
                    overflow: hidden;
                    page-break-after: avoid;
                    break-after: avoid-page;
                }
            </style>
        </head>
        <body>
            <div class="print-root">${labelHtml}</div>
            <script>
                window.addEventListener('load', () => setTimeout(() => window.print(), 500));
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}
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


const shopPreviewData = {
    shop_name: @json($setting->shop_name),
    shop_address: @json($setting->shop_address),
    shop_phone: @json($setting->shop_phone),
};

const ROW_STYLE_ORDER = [
    { key: 'shop_name', label: 'ชื่อร้าน' },
    { key: 'date', label: 'วันที่' },
    { key: 'address', label: 'ที่อยู่ / เบอร์โทร' },
    { key: 'divider', label: 'แถบคั่น (divider)', marginOnly: true },
    { key: 'product_name', label: 'ชื่อสินค้า' },
    { key: 'dosage_line1', label: 'วิธีใช้ บรรทัด 1' },
    { key: 'dosage_line2', label: 'วิธีใช้ บรรทัด 2' },
    { key: 'indication', label: 'สรรพคุณ' },
    { key: 'advice', label: 'คำแนะนำ' },
    { key: 'barcode', label: 'บาร์โค้ด (barcode)', marginOnly: true },
];


// Label Settings
document.addEventListener('DOMContentLoaded', function() {
    const labelSettingsContainer = document.getElementById('label-settings-container');
    if (!labelSettingsContainer) return;

    // Load label settings when labels tab is clicked
    document.querySelector('[data-tab="labels"]')?.addEventListener('click', loadLabelSettings);

    // Also load if labels tab is active on page load
    if (document.getElementById('tab-labels')?.classList.contains('hidden') === false) {
        loadLabelSettings();
    }
});

async function loadLabelSettings() {
    const container = document.getElementById('label-settings-container');
    if (!container) return;

    try {
        const response = await fetch('/api/label-settings');
        const result = await response.json();
        
        if (result.success) {
            renderLabelSettingsForm(result.data);
        } else {
            container.innerHTML = '<div class="text-red-500 p-4">โหลดการตั้งค่าฉลากไม่สำเร็จ</div>';
        }
    } catch (error) {
        container.innerHTML = '<div class="text-red-500 p-4">เกิดข้อผิดพลาดในการโหลดการตั้งค่าฉลาก</div>';
    }
}

function renderLabelSettingsForm(settings) {
    const container = document.getElementById('label-settings-container');
    if (!container) return;

    const rowStyles = normalizeRowStyles(settings.row_styles);

    // Preview label data
    const previewLabel = {
        shop_name: shopPreviewData.shop_name || '',
        shop_address: shopPreviewData.shop_address || '',
        shop_phone: shopPreviewData.shop_phone || '',
        product_name: 'Example 500 mg',
        dosage: 'ทานครั้งละ 1 เม็ด',
        meal_relation: 'หลังอาหาร',
        frequency: 'วันละ 3 ครั้ง',
        label_time: 'เช้า-กลางวัน-เย็น',
        indication: 'ต้านเชื้อแบคทีเรีย',
        advice: 'รับประทานให้ครบ',
    };

    currentLabelSettings = { ...settings, row_styles: rowStyles };
    currentPreviewLabel = previewLabel;

    const html = `
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            <!-- Left: Preview -->
            <div class="sticky top-5 self-start">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Preview ฉลากยา</h3>
                <div class="border-2 border-gray-300 rounded-lg p-4 bg-white">
                    <div id="label-preview" class="flex items-center justify-center min-h-[400px]">
                        <!-- Preview will be rendered here -->
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500">
                    <p>ตัวอย่างฉลากยาแสดงผลตามการตั้งค่าข้างขวา (อัปเดตทันที)</p>
                </div>
                <div class="mt-2 flex justify-end">
                    <button type="button" id="btn-print-sample"
                        class="px-4 py-2 rounded-lg border border-purple-400 text-purple-600 hover:bg-purple-50 text-sm font-medium">
                        🖨 พิมพ์ฉลากตัวอย่าง
                    </button>
                </div>
            </div>

            <!-- Right: Form -->
            <div>
                <h3 class="text-sm font-semibold text-gray-800 mb-4">ตั้งค่า template ฉลากยา</h3>
                <form id="label-settings-form" class="space-y-6">
                    <input type="hidden" name="bold_shop" value="${settings.bold_shop ? 1 : 0}">
                    <input type="hidden" name="bold_product" value="${settings.bold_product ? 1 : 0}">
                    <input type="hidden" name="bold_dosage" value="${settings.bold_dosage ? 1 : 0}">
                    <input type="hidden" name="line_spacing" value="${settings.line_spacing}">
                    <input type="hidden" name="section_gap" value="${settings.section_gap}">

                    <!-- Group 3: Row Controls -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-xs font-semibold text-gray-700 mb-1">ตั้งค่าแต่ละแถว</h4>
                        <p class="text-xs text-gray-500 mb-3">แต่ละบรรทัดเลื่อนขึ้น/ลงได้แบบอิสระทันทีจากตัวเลื่อน margin-top</p>
                        <div class="space-y-4">
                            ${ROW_STYLE_ORDER.map((row) => {
                                const style = rowStyles[row.key];

                                if (row.marginOnly) {
                                    return `
                                <div class="border border-gray-100 rounded-lg p-3 bg-gray-50" data-row-key="${row.key}">
                                    <div class="text-sm font-medium text-gray-700 mb-2">${row.label}</div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">margin-top: <span class="text-gray-400">เลื่อนขึ้น(ค่าลบ) / ลง(ค่าบวก) แบบอิสระ</span></label>
                                        <div class="flex items-center gap-3">
                                            <input type="range" min="-50" max="100" value="${style.marginTop}" class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer row-margin-range" name="row_styles[${row.key}][marginTop]" data-row-key="${row.key}">
                                            <input type="number" min="-50" max="100" value="${style.marginTop}" class="w-20 h-9 rounded-lg border border-gray-300 px-2 text-sm focus:outline-none focus:border-emerald-400 row-margin-number" data-row-key="${row.key}">
                                            <span class="text-xs text-gray-600 whitespace-nowrap">px</span>
                                        </div>
                                    </div>
                                </div>`;
                                }

                                return `
                                <div class="border border-gray-100 rounded-lg p-3 bg-gray-50" data-row-key="${row.key}">
                                    <div class="text-sm font-medium text-gray-700 mb-2">${row.label}</div>
                                    
                                    <!-- Row 1: B/I/U and Alignment -->
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="flex items-center gap-1">
                                            <input type="hidden" name="row_styles[${row.key}][bold]" value="${style.bold ? 1 : 0}" data-row-key="${row.key}" data-prop="bold">
                                            <input type="hidden" name="row_styles[${row.key}][italic]" value="${style.italic ? 1 : 0}" data-row-key="${row.key}" data-prop="italic">
                                            <input type="hidden" name="row_styles[${row.key}][underline]" value="${style.underline ? 1 : 0}" data-row-key="${row.key}" data-prop="underline">
                                            <button type="button" class="row-style-btn px-2.5 py-1.5 text-xs font-semibold rounded border" data-row-key="${row.key}" data-prop="bold">B</button>
                                            <button type="button" class="row-style-btn px-2.5 py-1.5 text-xs font-semibold rounded border" data-row-key="${row.key}" data-prop="italic">I</button>
                                            <button type="button" class="row-style-btn px-2.5 py-1.5 text-xs font-semibold rounded border" data-row-key="${row.key}" data-prop="underline">U</button>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <input type="hidden" name="row_styles[${row.key}][align]" value="${style.align}" data-row-key="${row.key}" data-prop="align">
                                            <button type="button" class="row-align-btn px-2.5 py-1.5 text-xs rounded border" data-row-key="${row.key}" data-align="left">←</button>
                                            <button type="button" class="row-align-btn px-2.5 py-1.5 text-xs rounded border" data-row-key="${row.key}" data-align="center">≡</button>
                                            <button type="button" class="row-align-btn px-2.5 py-1.5 text-xs rounded border" data-row-key="${row.key}" data-align="right">→</button>
                                        </div>
                                    </div>

                                    <!-- Row 2: Font Size -->
                                    <div class="mb-3">
                                        <label class="block text-xs text-gray-600 mb-1">Font size:</label>
                                        <div class="flex items-center gap-3">
                                            <input type="range" min="6" max="40" value="${style.fontSize}" class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer row-font-range" name="row_styles[${row.key}][fontSize]" data-row-key="${row.key}">
                                            <input type="number" min="6" max="40" value="${style.fontSize}" class="w-20 h-9 rounded-lg border border-gray-300 px-2 text-sm focus:outline-none focus:border-emerald-400 row-font-number" data-row-key="${row.key}">
                                            <span class="text-xs text-gray-600 whitespace-nowrap">px</span>
                                        </div>
                                    </div>

                                    <!-- Row 3: Margin Top -->
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Margin top: <span class="text-gray-400">เลื่อนบรรทัดขึ้น(ลบ) / ลง(บวก) แบบอิสระ</span></label>
                                        <div class="flex items-center gap-3">
                                            <input type="range" min="-50" max="100" value="${style.marginTop}" class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer row-margin-range" name="row_styles[${row.key}][marginTop]" data-row-key="${row.key}">
                                            <input type="number" min="-50" max="100" value="${style.marginTop}" class="w-20 h-9 rounded-lg border border-gray-300 px-2 text-sm focus:outline-none focus:border-emerald-400 row-margin-number" data-row-key="${row.key}">
                                            <span class="text-xs text-gray-600 whitespace-nowrap">px</span>
                                        </div>
                                    </div>
                                </div>`;
                            }).join('')}
                        </div>
                    </div>

                    <!-- Group 2: Font -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-xs font-semibold text-gray-700 mb-3">Font</h4>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-600 mb-1">Font Family</label>
                            <select name="font_family" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                                <option value="Google Sans" ${['Google Sans', 'GoogleSans'].includes(settings.font_family) ? 'selected' : ''}>Google Sans</option>
                                <option value="Tahoma" ${settings.font_family === 'Tahoma' ? 'selected' : ''}>Tahoma</option>
                                <option value="Sarabun" ${settings.font_family === 'Sarabun' ? 'selected' : ''}>Sarabun</option>
                                <option value="Arial" ${settings.font_family === 'Arial' ? 'selected' : ''}>Arial</option>
                                <option value="Courier New" ${settings.font_family === 'Courier New' ? 'selected' : ''}>Courier New</option>
                            </select>
                        </div>
                    </div>

                    <!-- Group 1: Paper Size -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-xs font-semibold text-gray-700 mb-3">ตั้งค่าหน้ากระดาษ (mm)</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">กว้าง</label>
                                <input type="number" name="paper_width" value="${settings.paper_width}" min="50" max="200" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">สูง</label>
                                <input type="number" name="paper_height" value="${settings.paper_height}" min="50" max="200" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                            </div>
                        </div>
                        <div class="mt-3 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Padding บน</label>
                                <input type="number" name="padding_top" value="${settings.padding_top}" min="0" max="20" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Padding ขวา</label>
                                <input type="number" name="padding_right" value="${settings.padding_right}" min="0" max="20" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Padding ล่าง</label>
                                <input type="number" name="padding_bottom" value="${settings.padding_bottom}" min="0" max="20" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Padding ซ้าย</label>
                                <input type="number" name="padding_left" value="${settings.padding_left}" min="0" max="20" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:border-emerald-400">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 border-t border-gray-200">
                        <button type="submit" class="w-full h-11 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium">
                            บันทึกการตั้งค่า
                        </button>
                    </div>
                </form>
            </div>

        </div>
    `;

    container.innerHTML = html;

    // Move print sample button event binding here (since button is now in preview column)
    setTimeout(() => {
    // Duplicate event binding removed
    }, 0);

    // Initialize event listeners
    initializeLabelSettingsForm({ ...settings, row_styles: rowStyles }, previewLabel);
}

function initializeLabelSettingsForm(settings, previewLabel) {
    const form = document.getElementById('label-settings-form');
    if (!form) return;

    const toBool = (value) => value === true || value === 1 || value === '1' || value === 'on';

    const clampFontSize = (value, fallback = 12) => {
        const parsed = parseInt(value, 10);
        if (Number.isNaN(parsed)) return fallback;
        return Math.min(40, Math.max(6, parsed));
    };

    const clampMarginTop = (value, fallback = 0) => {
        const parsed = parseInt(value, 10);
        if (Number.isNaN(parsed)) return fallback;
        return Math.min(100, Math.max(-50, parsed));
    };

    const collectRowStylesFromForm = (formData) => {
        const defaults = normalizeRowStyles(settings.row_styles);
        const result = {};

        ROW_STYLE_ORDER.forEach(({ key }) => {
            const fallback = defaults[key] || DEFAULT_ROW_STYLES[key];
            const alignRaw = formData.get(`row_styles[${key}][align]`) || fallback.align;
            result[key] = {
                fontSize: clampFontSize(formData.get(`row_styles[${key}][fontSize]`), fallback.fontSize),
                bold: toBool(formData.get(`row_styles[${key}][bold]`) ?? (fallback.bold ? '1' : '0')),
                italic: toBool(formData.get(`row_styles[${key}][italic]`) ?? (fallback.italic ? '1' : '0')),
                underline: toBool(formData.get(`row_styles[${key}][underline]`) ?? (fallback.underline ? '1' : '0')),
                align: ['left', 'center', 'right'].includes(alignRaw) ? alignRaw : fallback.align,
                marginTop: clampMarginTop(formData.get(`row_styles[${key}][marginTop]`), fallback.marginTop),
            };
        });

        return result;
    };

    const applyRowButtonState = (rowKey) => {
        ['bold', 'italic', 'underline'].forEach((prop) => {
            const hidden = form.querySelector(`input[data-row-key="${rowKey}"][data-prop="${prop}"]`);
            const active = toBool(hidden?.value);
            form.querySelectorAll(`button.row-style-btn[data-row-key="${rowKey}"][data-prop="${prop}"]`).forEach((btn) => {
                btn.classList.toggle('bg-emerald-500', active);
                btn.classList.toggle('text-white', active);
                btn.classList.toggle('border-emerald-500', active);
                btn.classList.toggle('bg-white', !active);
                btn.classList.toggle('text-gray-700', !active);
                btn.classList.toggle('border-gray-300', !active);
            });
        });

        const alignHidden = form.querySelector(`input[data-row-key="${rowKey}"][data-prop="align"]`);
        const align = alignHidden?.value || 'left';
        form.querySelectorAll(`button.row-align-btn[data-row-key="${rowKey}"]`).forEach((btn) => {
            const active = btn.dataset.align === align;
            btn.classList.toggle('bg-emerald-500', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('border-emerald-500', active);
            btn.classList.toggle('bg-white', !active);
            btn.classList.toggle('text-gray-700', !active);
            btn.classList.toggle('border-gray-300', !active);
        });
    };

    const bindRowControls = () => {
        ROW_STYLE_ORDER.forEach(({ key }) => {
            // Font size sync between range and number inputs
            const fontSizeRange = form.querySelector(`input.row-font-range[data-row-key="${key}"]`);
            const fontSizeNumber = form.querySelector(`input.row-font-number[data-row-key="${key}"]`);
            
            if (fontSizeRange && fontSizeNumber) {
                const syncFontSize = () => {
                    fontSizeNumber.value = fontSizeRange.value;
                };
                
                fontSizeRange.addEventListener('input', () => {
                    syncFontSize();
                    updateLabelPreview();
                });
                
                fontSizeNumber.addEventListener('input', () => {
                    let value = parseInt(fontSizeNumber.value, 10);
                    if (isNaN(value)) value = 6;
                    value = Math.max(6, Math.min(40, value));
                    fontSizeRange.value = value;
                    fontSizeNumber.value = value;
                    updateLabelPreview();
                });
                
                syncFontSize();
            }

            // Margin top sync between range and number inputs
            const marginRange = form.querySelector(`input.row-margin-range[data-row-key="${key}"]`);
            const marginNumber = form.querySelector(`input.row-margin-number[data-row-key="${key}"]`);
            
            if (marginRange && marginNumber) {
                const syncMargin = () => {
                    marginNumber.value = marginRange.value;
                };
                
                marginRange.addEventListener('input', () => {
                    syncMargin();
                    updateLabelPreview();
                });
                
                marginNumber.addEventListener('input', () => {
                    let value = parseInt(marginNumber.value, 10);
                    if (isNaN(value)) value = 0;
                    value = Math.max(-50, Math.min(100, value));
                    marginRange.value = value;
                    marginNumber.value = value;
                    updateLabelPreview();
                });
                
                syncMargin();
            }

            // Style buttons (B/I/U)
            form.querySelectorAll(`button.row-style-btn[data-row-key="${key}"]`).forEach((btn) => {
                btn.addEventListener('click', () => {
                    const prop = btn.dataset.prop;
                    if (!prop) return;
                    const hidden = form.querySelector(`input[data-row-key="${key}"][data-prop="${prop}"]`);
                    if (!hidden) return;
                    hidden.value = toBool(hidden.value) ? '0' : '1';
                    applyRowButtonState(key);
                    updateLabelPreview();
                });
            });

            // Alignment buttons (← ≡ →)
            form.querySelectorAll(`button.row-align-btn[data-row-key="${key}"]`).forEach((btn) => {
                btn.addEventListener('click', () => {
                    const hidden = form.querySelector(`input[data-row-key="${key}"][data-prop="align"]`);
                    if (!hidden || !btn.dataset.align) return;
                    hidden.value = btn.dataset.align;
                    applyRowButtonState(key);
                    updateLabelPreview();
                });
            });

            applyRowButtonState(key);
        });
    };

    // Update range value displays
    const updateRangeValue = (inputName, displayId) => {
        const input = form.querySelector(`[name="${inputName}"]`);
        const display = document.getElementById(displayId);
        if (input && display) {
            const update = () => {
                if (inputName === 'line_spacing') {
                    display.textContent = parseFloat(input.value).toFixed(1);
                } else {
                    display.textContent = input.value + 'px';
                }
                updateLabelPreview();
            };
            input.addEventListener('input', update);
            update(); // Initial update
        }
    };

    // Set up range inputs
    updateRangeValue('line_spacing', 'line_spacing_value');
    updateRangeValue('section_gap', 'section_gap_value');
    bindRowControls();

    // Update preview on any form change
    form.querySelectorAll('input, select').forEach(element => {
        if (element.type !== 'range') {
            element.addEventListener('input', updateLabelPreview);
            element.addEventListener('change', updateLabelPreview);
        }
    });

    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {
            paper_width: parseInt(formData.get('paper_width') || settings.paper_width, 10),
            paper_height: parseInt(formData.get('paper_height') || settings.paper_height, 10),
            padding_top: parseInt(formData.get('padding_top') || settings.padding_top, 10),
            padding_right: parseInt(formData.get('padding_right') || settings.padding_right, 10),
            padding_bottom: parseInt(formData.get('padding_bottom') || settings.padding_bottom, 10),
            padding_left: parseInt(formData.get('padding_left') || settings.padding_left, 10),
            font_family: formData.get('font_family') || settings.font_family,
            bold_shop: toBool(formData.get('bold_shop')),
            bold_product: toBool(formData.get('bold_product')),
            bold_dosage: toBool(formData.get('bold_dosage')),
            line_spacing: parseFloat(formData.get('line_spacing') || settings.line_spacing),
            section_gap: parseInt(formData.get('section_gap') || settings.section_gap, 10),
            row_styles: collectRowStylesFromForm(formData),
        };

        try {
            const response = await fetch('/api/label-settings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            if (result.success) {
                showToast('บันทึกการตั้งค่าฉลากเรียบร้อยแล้ว', 'success');
            } else {
                showToast('บันทึกไม่สำเร็จ: ' + (result.message || 'เกิดข้อผิดพลาด'), 'error');
            }
        } catch (error) {
            showToast('เกิดข้อผิดพลาดในการบันทึก: ' + error.message, 'error');
        }
    });

    // Initial preview render
    updateLabelPreview();
    window.addEventListener('resize', updateLabelPreview);
    document.getElementById('btn-print-sample')?.addEventListener('click', printSampleLabel);

    function updateLabelPreview() {
        const formData = new FormData(form);

        const previewSettings = {
            paper_width: parseInt(formData.get('paper_width') || settings.paper_width, 10),
            paper_height: parseInt(formData.get('paper_height') || settings.paper_height, 10),
            padding_top: parseInt(formData.get('padding_top') || settings.padding_top, 10),
            padding_right: parseInt(formData.get('padding_right') || settings.padding_right, 10),
            padding_bottom: parseInt(formData.get('padding_bottom') || settings.padding_bottom, 10),
            padding_left: parseInt(formData.get('padding_left') || settings.padding_left, 10),
            font_family: formData.get('font_family') || settings.font_family,
            bold_shop: toBool(formData.get('bold_shop')),
            bold_product: toBool(formData.get('bold_product')),
            bold_dosage: toBool(formData.get('bold_dosage')),
            line_spacing: parseFloat(formData.get('line_spacing') || settings.line_spacing),
            section_gap: parseInt(formData.get('section_gap') || settings.section_gap, 10),
            row_styles: collectRowStylesFromForm(formData),
        };

        const previewContainer = document.getElementById('label-preview');
        if (previewContainer) {
            const availableWidth = Math.max((previewContainer.clientWidth || 400) - 8, 240);
            previewContainer.innerHTML = generateLabelPreview(previewLabel, previewSettings, availableWidth);
        }
    }

    function printSampleLabel() {
        const formData = new FormData(form);
        const s = {
            paper_width:    parseInt(formData.get('paper_width')    || settings.paper_width,  10),
            paper_height:   parseInt(formData.get('paper_height')   || settings.paper_height, 10),
            padding_top:    parseInt(formData.get('padding_top')    || settings.padding_top,  10),
            padding_right:  parseInt(formData.get('padding_right')  || settings.padding_right,10),
            padding_bottom: parseInt(formData.get('padding_bottom') || settings.padding_bottom,10),
            padding_left:   parseInt(formData.get('padding_left')   || settings.padding_left, 10),
            font_family:    formData.get('font_family') || settings.font_family,
            line_spacing:   parseFloat(formData.get('line_spacing') || settings.line_spacing),
            section_gap:    parseInt(formData.get('section_gap')    || settings.section_gap,  10),
            row_styles:     collectRowStylesFromForm(formData),
        };

        const labelHtml = generateLabelPreview(previewLabel, s, s.paper_width * 3.78, { forPrint: true });

        const printWindow = window.open('', '', 'width=800,height=600');
        if (!printWindow) {
            alert('เบราว์เซอร์บล็อกหน้าต่างพิมพ์ กรุณาอนุญาต pop-up แล้วลองใหม่');
            return;
        }

        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
                <style>
                    @page {
                        size: ${s.paper_width}mm ${s.paper_height}mm;
                        margin: 0;
                    }
                    html, body {
                        width: ${s.paper_width}mm;
                        height: ${s.paper_height}mm;
                        margin: 0;
                        padding: 0;
                        overflow: hidden;
                        font-family: '${s.font_family}', 'Google Sans', Arial, sans-serif;
                    }
                </style>
            </head>
            <body>
                ${labelHtml}
                <script>
                    window.addEventListener('load', () => setTimeout(() => window.print(), 500));
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }

}

// Toast notification helper (if not already defined)
if (typeof showToast === 'undefined') {
    window.showToast = function(message, type = 'info') {
        // Simple alert fallback
        alert(`[${type.toUpperCase()}] ${message}`);
    };
}
</script>
@endsection
