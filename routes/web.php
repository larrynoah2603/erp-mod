<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check() && Route::has('dashboard')) {
        return redirect()->route('dashboard');
    }

    if (! auth()->check() && Route::has('login')) {
        return redirect()->route('login');
    }

    return view('welcome');
})->name('home');
