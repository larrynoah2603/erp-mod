<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;

class ExportController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'OK']);
    }
}
