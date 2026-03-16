<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Sales\Http\Livewire\InvoiceManager;
use App\Modules\Sales\Http\Livewire\InvoiceForm;
use App\Modules\Sales\Http\Livewire\CustomerManager;
use App\Modules\Sales\Http\Livewire\PaymentManager;

Route::middleware(['auth', 'verified'])->prefix('sales')->name('sales.')->group(function () {
    Route::get('/invoices', InvoiceManager::class)->name('invoices');
    Route::get('/invoices/create', InvoiceForm::class)->name('invoices.create');
    Route::get('/invoices/{id}/edit', InvoiceForm::class)->name('invoices.edit');
    Route::get('/customers', CustomerManager::class)->name('customers');
    Route::get('/payments', PaymentManager::class)->name('payments');
});