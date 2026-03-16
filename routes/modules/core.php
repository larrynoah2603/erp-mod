<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Core\Http\Livewire\Dashboard;
use App\Modules\Core\Http\Livewire\Profile;
use App\Modules\Core\Http\Livewire\Settings;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', Profile::class)->name('profile');
    Route::get('/settings', Settings::class)->name('settings');
});