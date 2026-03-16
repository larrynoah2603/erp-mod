<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'OK']);
    }
}
