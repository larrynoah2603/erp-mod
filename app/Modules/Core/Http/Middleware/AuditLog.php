<?php

namespace App\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuditLog
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->user()) {
            logger()->info('Request audited', [
                'user_id' => $request->user()->id,
                'method' => $request->method(),
                'url' => $request->fullUrl(),
            ]);
        }

        return $response;
    }
}
