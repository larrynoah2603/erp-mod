<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Stock\Http\Livewire\InventoryManager;
use App\Modules\Stock\Http\Livewire\ProductForm;
use App\Modules\Stock\Http\Livewire\StockAlerts;
use App\Modules\Stock\Http\Livewire\SupplierManager;

Route::middleware(['auth', 'verified'])->prefix('stock')->name('stock.')->group(function () {
    Route::get('/inventory', InventoryManager::class)->name('inventory');
    Route::get('/products/create', ProductForm::class)->name('products.create');
    Route::get('/products/{id}/edit', ProductForm::class)->name('products.edit');
    Route::get('/alerts', StockAlerts::class)->name('alerts');
    Route::get('/suppliers', SupplierManager::class)->name('suppliers');
});