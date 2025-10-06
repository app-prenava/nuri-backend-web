<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $next($request);
            }
        }

        return $this->unauthenticatedResponse($request, $guards);
    }

    /**
     * Generate JSON response for unauthenticated access.
     */
    protected function unauthenticatedResponse($request, array $guards): JsonResponse
    {
        $message = 'Unauthenticated. Please provide a valid API token.';

        if (in_array('api', $guards)) {
            $message = 'Access denied. A valid Bearer token is required.';
        }

        return response()->json([
            'status'  => 'error',
            'message' => $message,
        ], 401);
    }

    /**
     * Override the default redirect behavior (disabled for API).
     */
    protected function redirectTo($request)
    {
        return null;
    }
}
