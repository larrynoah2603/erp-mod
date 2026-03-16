<?php

namespace App\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ModuleAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            abort(401);
        }

        return $next($request);
    }
}
