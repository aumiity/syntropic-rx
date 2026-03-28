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
