@extends('layouts.app')

@section('content')

<div class="h-screen bg-slate-50 py-4">
    <div class="max-w-7xl mx-auto h-full bg-white rounded-xl shadow-md overflow-hidden flex flex-col">

        <div class="px-5 py-4 border-b border-slate-200 flex justify-between items-center gap-3">
            <div>
                <h1 class="text-2xl font-bold">รับยาเข้าสต๊อค</h1>
                <p class="text-slate-500">รองรับรายการรับสินค้า 10-30 รายการ ในใบเดียว</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pos.stock.receive.history') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">ประวัติรับสินค้า</a>
                <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300">กลับ POS</a>
                <a href="{{ route('suppliers.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300">ผู้จำหน่าย</a>
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300">รายการสินค้า</a>
            </div>
        </div>

        <div class="p-5 overflow-y-auto flex-1">

            @if(session('success'))
                <div class="mb-3 p-3 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="mb-3 p-3 bg-rose-100 border border-rose-300 text-rose-800 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pos.stock.receive.store') }}" method="POST" class="space-y-4" id="stock-receive-form">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">เลขที่เอกสาร</label>
                        <input type="text" name="invoice_no" value="{{ old('invoice_no') }}" class="w-full rounded border border-slate-300 px-3 py-2" placeholder="เช่น PO123456">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">วันที่รับสินค้า</label>
                        <input type="datetime-local" name="receive_date" value="{{ old('receive_date', now()->format('Y-m-d\TH:i')) }}" class="w-full rounded border border-slate-300 px-3 py-2" data-required="true" data-error-msg="กรุณาเลือกวันที่รับสินค้า">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">ผู้จำหน่าย</label>
                                        <select name="supplier_id" class="w-full rounded border border-slate-300 px-3 py-2" data-required="true" data-error-msg="กรุณาเลือกสินค้า">
                            <option value="">-- เลือกผู้จำหน่าย --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-lg font-semibold">รายการรับยา</h2>
                    <button type="button" id="add-row" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">เพิ่มแถว</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-slate-200" id="receive-table">
                        <thead class="bg-slate-100 text-slate-700 text-sm">
                            <tr>
                                <th class="px-2 py-2">#</th>
                                <th class="px-2 py-2">สินค้า</th>
                                <th class="px-2 py-2">Lot</th>
                                <th class="px-2 py-2">วันที่ผลิต</th>
                                <th class="px-2 py-2">หมดอายุ</th>
                                <th class="px-2 py-2">ราคาทุน</th>
                                <th class="px-2 py-2">ราคาขาย</th>
                                <th class="px-2 py-2">จำนวน</th>
                                <th class="px-2 py-2">หมายเหตุ</th>
                                <th class="px-2 py-2">-</th>
                            </tr>
                        </thead>
                        <tbody id="receive-tbody">
                            @php $rowCount = old('product_id', []) ? count(old('product_id')) : 1; @endphp
                            @for($i = 0; $i < $rowCount; $i++)
                                <tr data-row-index="{{ $i }}">
                                    <td class="px-2 py-1 text-center">{{ $i + 1 }}</td>
                                    <td class="px-2 py-1 min-w-[240px]">
                                        <select type="number" name="product_id[]" class="w-full rounded border border-slate-300 px-2 py-1 text-sm" required>
                                            <option value="">- เลือกสินค้า -</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->price_retail }}" {{ old('product_id.' . $i) == $product->id ? 'selected' : '' }}>{{ $product->trade_name }} ({{ $product->barcode ?? $product->code }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-2 py-1"><input type="text" name="lot_number[]" value="{{ old('lot_number.' . $i) }}" class="w-full rounded border border-slate-300 px-2 py-1 text-sm" data-required="true" data-error-msg="กรุณากรอกเลข Lot"></td>
                                    <td class="px-2 py-1"><input type="date" name="manufactured_date[]" value="{{ old('manufactured_date.' . $i) }}" class="w-full rounded border border-slate-300 px-2 py-1 text-sm"></td>
                                    <td class="px-2 py-1"><input type="date" name="expiry_date[]" value="{{ old('expiry_date.' . $i) }}" class="w-full rounded border border-slate-300 px-2 py-1 text-sm" data-required="true" data-error-msg="กรุณากรอกวันหมดอายุ"></td>
                                    <td class="px-2 py-1"><input type="number" name="cost_price[]" value="{{ old('cost_price.' . $i, '0.00') }}" step="0.01" min="0" class="w-full rounded border border-slate-300 px-2 py-1 text-sm" required></td>
                                    <td class="px-2 py-1"><input type="number" name="sell_price[]" value="{{ old('sell_price.' . $i, '0.00') }}" step="0.01" min="0" class="w-full rounded border border-slate-300 px-2 py-1 text-sm" required></td>
                                    <td class="px-2 py-1"><input type="number" name="qty_received[]" value="{{ old('qty_received.' . $i, 1) }}" step="1" min="1" class="w-full rounded border border-slate-300 px-2 py-1 text-sm" data-required="true" data-error-msg="กรุณากรอกจำนวน"></td>
                                    <td class="px-2 py-1"><input type="text" name="note[]" value="{{ old('note.' . $i) }}" class="w-full rounded border border-slate-300 px-2 py-1 text-sm"></td>
                                    <td class="px-2 py-1 text-center">
                                        <button type="button" class="remove-row text-red-600 hover:text-red-800">x</button>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end mt-3 gap-2">
                    <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded">บันทึกการรับเข้า</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    const productData = @json($products->map(fn($product) => ['id' => $product->id, 'label' => $product->trade_name . ' (' . ($product->barcode ?? $product->code) . ')', 'price' => $product->price_retail])->all());
    const tbody = document.getElementById('receive-tbody');
    const addRowBtn = document.getElementById('add-row');

    function getProductOptionsHtml() {
        return productData.map(p => `<option value="${p.id}" data-price="${p.price}">${p.label}</option>`).join('');
    }

    function updateRowNumbers() {
        Array.from(tbody.children).forEach((tr, index) => {
            tr.setAttribute('data-row-index', index);
            tr.children[0].innerText = index + 1;
        });
    }

    function createRow() {
        const rowIndex = tbody.children.length;
        const tr = document.createElement('tr');
        tr.setAttribute('data-row-index', rowIndex);
        tr.innerHTML = `
            <td class="px-2 py-1 text-center">${rowIndex + 1}</td>
            <td class="px-2 py-1 min-w-[240px]">
                <select name="product_id[]" class="w-full rounded border border-slate-300 px-2 py-1 text-sm" required>
                    <option value="">- เลือกสินค้า -</option>
                    ${getProductOptionsHtml()}
                </select>
            </td>
            <td class="px-2 py-1"><input type="text" name="lot_number[]" class="w-full rounded border border-slate-300 px-2 py-1 text-sm" required></td>
            <td class="px-2 py-1"><input type="date" name="manufactured_date[]" class="w-full rounded border border-slate-300 px-2 py-1 text-sm"></td>
            <td class="px-2 py-1"><input type="date" name="expiry_date[]" class="w-full rounded border border-slate-300 px-2 py-1 text-sm" required></td>
            <td class="px-2 py-1"><input type="number" name="cost_price[]" step="0.01" min="0" value="0.00" class="w-full rounded border border-slate-300 px-2 py-1 text-sm" required></td>
            <td class="px-2 py-1"><input type="number" name="sell_price[]" step="0.01" min="0" value="0.00" class="w-full rounded border border-slate-300 px-2 py-1 text-sm" required></td>
            <td class="px-2 py-1"><input type="number" name="qty_received[]" step="1" min="1" value="1" class="w-full rounded border border-slate-300 px-2 py-1 text-sm" required></td>
            <td class="px-2 py-1"><input type="text" name="note[]" class="w-full rounded border border-slate-300 px-2 py-1 text-sm"></td>
            <td class="px-2 py-1 text-center"><button type="button" class="remove-row text-red-600 hover:text-red-800">x</button></td>
        `;
        return tr;
    }

    addRowBtn.addEventListener('click', () => {
        tbody.appendChild(createRow());
        updateRowNumbers();
    });

    tbody.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-row')) {
            const tr = e.target.closest('tr');
            tr.remove();
            updateRowNumbers();
        }
    });

    tbody.addEventListener('change', (e) => {
        if (e.target.name === 'product_id[]') {
            const select = e.target;
            const tr = select.closest('tr');
            const sellPriceInput = tr.querySelector('input[name="sell_price[]"]');
            const selectedOption = select.options[select.selectedIndex];
            if (selectedOption && selectedOption.dataset.price) {
                sellPriceInput.value = selectedOption.dataset.price;
            }
        }
    });
</script>

@endsection
