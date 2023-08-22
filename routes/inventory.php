<?php
Route::get('/', [\App\Http\Controllers\Inventory\InventoryController::class, 'index'])->name('inventory.index');
Route::resource('brand', \App\Http\Controllers\Inventory\BrandController::class);
Route::resource('category', \App\Http\Controllers\Inventory\CategoriessController::class, ['as' => 'inventory']);
Route::resource('supplier', \App\Http\Controllers\Inventory\SuppliersController::class);
Route::resource('product', \App\Http\Controllers\Inventory\ProductsController::class);
Route::put('add_meta/{product}', [\App\Http\Controllers\Inventory\ProductsController::class, 'addMeta'])->name('product.add_meta');
