<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if(Auth::user()->role !== $role){
            return response()->json([
                "error" => "Permission denied"
            ],403);
        }

        return $next($request);
    }
}