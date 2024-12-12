<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class ProtectSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->role === 'super admin' && !auth()->user()->hasRole('super admin')) {
            return redirect()->back()->with('error', 'Only Super Admin can assign the Super Admin role.');
        }

        return $next($request);
    }
}
