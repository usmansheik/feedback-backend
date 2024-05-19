<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated and has the admin role
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            return $next($request);
        }

        // If not authenticated or doesn't have the admin role, return unauthorized response
        return response()->json(['error' => 'Unauthorized.'], 403);
    }
}
