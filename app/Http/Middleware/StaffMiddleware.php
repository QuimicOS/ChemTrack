<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class StaffMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the authenticated user has the Administrator role
        if (Auth::check() && Auth::user()->role === 'Staff') {
            return $next($request);
        }

        // If not, return an unauthorized response
        return response()->json(['error' => 'Unauthorized , Stick to Staff'], 403);
    }
}
