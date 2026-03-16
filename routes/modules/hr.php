<?php

use Illuminate\Support\Facades\Route;
use App\Modules\HR\Http\Livewire\TimeClock;
use App\Modules\HR\Http\Livewire\LeaveManager;
use App\Modules\HR\Http\Livewire\RHDashboard;
use App\Modules\HR\Http\Livewire\EmployeeManager;
use App\Modules\HR\Http\Livewire\PayrollManager;

Route::middleware(['auth', 'verified'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/attendance', TimeClock::class)->name('attendance');
    Route::get('/leaves', LeaveManager::class)->name('leaves');
    Route::get('/dashboard', RHDashboard::class)->name('dashboard');
    Route::get('/employees', EmployeeManager::class)->name('employees');
    Route::get('/payroll', PayrollManager::class)->name('payroll');
});