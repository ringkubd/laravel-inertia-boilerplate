<?php
Route::get('/', [\App\Http\Controllers\Inventory\InventoryController::class, 'index'])->name('inventory.index');
Route::resource('brand', \App\Http\Controllers\Inventory\BrandController::class);
Route::resource('category', \App\Http\Controllers\Inventory\CategoriessController::class, ['as' => 'inventory']);
Route::resource('supplier', \App\Http\Controllers\Inventory\SuppliersController::class);
Route::resource('product', \App\Http\Controllers\Inventory\ProductsController::class);
Route::put('add_meta/{product}', [\App\Http\Controllers\Inventory\ProductsController::class, 'addMeta'])->name('product.add_meta');
Route::put('disable_product/{product}', [\App\Http\Controllers\Inventory\ProductsController::class, 'disable'])->name('product.disable');

//Purchase
Route::resource('purchase', \App\Http\Controllers\Inventory\PurchaseController::class);
Route::get('product_select', [\App\Http\Controllers\Inventory\PurchaseController::class, 'products'])->name('purchase_products');
Route::get('product_supplier', [\App\Http\Controllers\Inventory\PurchaseController::class, 'suppliers'])->name('purchase_suppliers');
Route::get('product_stocks/{product}', [\App\Http\Controllers\Inventory\PurchaseController::class, 'productStocks'])->name('purchase_existing_stocks');
