<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
public function handle(Request $request, Closure $next, $role)
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    
    if ($user->role !== $role) {
        // Redirect to appropriate dashboard based on actual role
        return redirect()->route("{$user->role}.dashboard")
               ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    return $next($request);
}
}