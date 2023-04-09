<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!$request->user()->is_admin) {
            return response()->json(['error' => 'Unauthenticated.'], 403);
        }

        return $next($request);
    }
}
