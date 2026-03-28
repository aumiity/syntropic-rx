<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;

class PosController extends Controller
{
    public function index()
    {
        $customers = Customer::where('id', '!=', 1)->get();
        return view('pos.index', compact('customers'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $products = Product::where('is_disabled', false)
            ->where(function($q) use ($query) {
                $q->where('trade_name', 'like', "%{$query}%")
                  ->orWhere('barcode', $query)
                  ->orWhere('code', $query);
            })
            ->with(['lots' => function($q) {
                $q->where('qty_on_hand', '>', 0)
                  ->orderBy('expiry_date', 'asc');
            }])
            ->get();

        return response()->json($products);
    }

    public function productIndex(Request $request)
    {
        $q = trim($request->get('q', ''));

        $products = Product::when($q, function($query) use ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('trade_name', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
            });
        })->orderBy('id', 'desc')->paginate(20);

        return view('pos.products_index', compact('products', 'q'));
    }

    public function editProduct(Product $product)
    {
        return view('pos.edit_product', compact('product'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $data = $request->validate([
            'barcode' => 'required|string|max:50|unique:products,barcode,' . $product->id,
            'code' => 'nullable|string|max:50|unique:products,code,' . $product->id,
            'trade_name' => 'required|string|max:255',
            'name_for_print' => 'nullable|string|max:255',
            'price_retail' => 'required|numeric|min:0',
            'price_wholesale1' => 'nullable|numeric|min:0',
            'item_type' => 'nullable|string|in:drug,supply,equipment,service',
            'reorder_point' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'drug_type_id' => 'nullable|integer|exists:drug_types,id',
        ]);

        $data['item_type'] = $data['item_type'] ?? 'drug';
        $product->update($data);

        return redirect()->route('products.index')->with('success', 'อัพเดตข้อมูลสินค้าเรียบร้อยแล้ว');
    }

    public function createProduct()
    {
        return view('pos.create_product');
    }

    public function storeProduct(Request $request)
    {
        $data = $request->validate([
            'barcode' => 'required|string|max:50|unique:products,barcode',
            'code' => 'nullable|string|max:50|unique:products,code',
            'trade_name' => 'required|string|max:255',
            'name_for_print' => 'nullable|string|max:255',
            'price_retail' => 'required|numeric|min:0',
            'price_wholesale1' => 'nullable|numeric|min:0',
            'item_type' => 'nullable|string|in:drug,supply,equipment,service',
            'reorder_point' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'drug_type_id' => 'nullable|integer|exists:drug_types,id',
        ]);

        $data['item_type'] = $data['item_type'] ?? 'drug';

        Product::create(array_merge($data, [
            'is_disabled' => false,
            'is_hidden' => false,
        ]));

        return redirect()->route('pos.products.create')
            ->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว');
    }
}
