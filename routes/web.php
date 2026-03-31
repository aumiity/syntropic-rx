// Product units management
Route::post('/products/{product}/units', [\App\Http\Controllers\PosController::class, 'storeProductUnit'])->name('products.units.store');
Route::delete('/products/{product}/units/{unit}', [\App\Http\Controllers\PosController::class, 'destroyProductUnit'])->name('products.units.destroy');
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SettingsController;

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

// Settings Routes
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

// Product Categories
Route::post('/settings/categories', [SettingsController::class, 'storeCategory'])->name('settings.categories.store');
Route::put('/settings/categories/{category}', [SettingsController::class, 'updateCategory'])->name('settings.categories.update');
Route::patch('/settings/categories/{category}/toggle', [SettingsController::class, 'toggleCategory'])->name('settings.categories.toggle');

// Item Units
Route::post('/settings/units', [SettingsController::class, 'storeUnit'])->name('settings.units.store');
Route::put('/settings/units/{unit}', [SettingsController::class, 'updateUnit'])->name('settings.units.update');
Route::delete('/settings/units/{unit}', [SettingsController::class, 'deleteUnit'])->name('settings.units.delete');

// Drug Types
Route::post('/settings/drug-types', [SettingsController::class, 'storeDrugType'])->name('settings.drugtypes.store');
Route::put('/settings/drug-types/{type}', [SettingsController::class, 'updateDrugType'])->name('settings.drugtypes.update');
Route::patch('/settings/drug-types/{type}/toggle', [SettingsController::class, 'toggleDrugType'])->name('settings.drugtypes.toggle');
