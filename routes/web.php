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


Route::get('/home', function () {
    if (Route::has('dashboard')) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('home');
})->name('legacy.home');
