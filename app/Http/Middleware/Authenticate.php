<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // Jika tidak ada guard yang ditentukan, gunakan guard default
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Jika sudah terautentikasi, lanjutkan request
                return $next($request);
            }
        }

        // Handle response berdasarkan tipe request
        return $this->unauthenticatedResponse($request, $guards);
    }

    /**
     * Generate response untuk unauthenticated access
     */
    protected function unauthenticatedResponse($request, array $guards)
    {
        // Jika request menginginkan JSON (API)
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Jika menggunakan guard 'api' tapi bukan request JSON
        if (in_array('api', $guards)) {
            return response()->json([
                'status' => 'error',
                'message' => 'API token required.'
            ], 401);
        }

        // Untuk request web biasa, redirect ke login
        return redirect()->guest(route('login'));
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}