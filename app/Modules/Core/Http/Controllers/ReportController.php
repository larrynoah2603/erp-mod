<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        return view('core::livewire.dashboard');
    }
}
