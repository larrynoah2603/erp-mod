<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function index()
    {
        return view('core::livewire.dashboard');
    }
}
