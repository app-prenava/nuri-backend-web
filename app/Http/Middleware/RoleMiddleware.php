<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, $roles)) {
            // Jika request ingin JSON (misalnya API)
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            // Kalau tidak, redirect ke halaman tertentu (misalnya login)
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses.');
        }

        return $next($request);
    }
}