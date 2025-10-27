<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BroadcastingAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Try to authenticate using Sanctum first
        if ($request->bearerToken()) {
            $user = Auth::guard('sanctum')->user();
            if ($user) {
                Auth::setUser($user);
                return $next($request);
            }
        }

        // Fall back to web authentication
        if (Auth::check()) {
            return $next($request);
        }

        // If no authentication, return 403
        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
