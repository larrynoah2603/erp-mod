<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;

class ImportController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'OK']);
    }
}
