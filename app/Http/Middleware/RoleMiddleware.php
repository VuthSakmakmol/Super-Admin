<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check() || !auth()->user()->hasAnyRole($roles)) {
            abort(403, 'Access denied');
        } 
        return $next($request);
    }
}
