<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    public function index()
    {
        return view('core::livewire.dashboard');
    }
}
