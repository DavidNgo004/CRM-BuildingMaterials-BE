<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if(!Auth::check() || !in_array(Auth::user()->role, $roles)){
            return response()->json([
                "error" => "Permission denied"
            ],403);
        }

        return $next($request);
    }
}