<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;

Route::get('/', [PosController::class, 'index'])->name('pos.index');
Route::get('/pos', [PosController::class, 'index'])->name('pos');
Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search');

// Product menu route (จาก sidebar)
Route::get('/products', [PosController::class, 'productIndex'])->name('products.index');
Route::get('/products/{product}/edit', [PosController::class, 'editProduct'])->name('products.edit');
Route::put('/products/{product}', [PosController::class, 'updateProduct'])->name('products.update');
Route::redirect('/product', '/products');

// Product management (simple add/search workflow)
Route::get('/pos/products/create', [PosController::class, 'createProduct'])->name('pos.products.create');
Route::post('/pos/products', [PosController::class, 'storeProduct'])->name('pos.products.store');

// Stock receive (รับยาเข้าสต๊อค)
Route::get('/purchase', [PosController::class, 'receiveStockForm'])->name('pos.stock.receive');
Route::post('/purchase', [PosController::class, 'receiveStock'])->name('pos.stock.receive.store');
Route::get('/purchase/history', [PosController::class, 'receiveStockHistory'])->name('pos.stock.receive.history');

Route::get('/pos/stock/receive', function () {
    return redirect()->route('pos.stock.receive');
});

// Supplier management
use App\Http\Controllers\SupplierController;

Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
