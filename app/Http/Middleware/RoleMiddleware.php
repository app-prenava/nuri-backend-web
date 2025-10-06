<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $role = strtolower((string) $payload->get('role'));
        } catch (TokenExpiredException $e) {
            return response()->json(['status'=>'error','message'=>'Token has expired.'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['status'=>'error','message'=>'Token is invalid.'], 401);
        } catch (JWTException $e) {
            return response()->json(['status'=>'error','message'=>'Unable to parse token.'], 401);
        } catch (\Throwable $e) {
            return response()->json(['status'=>'error','message'=>'Invalid or missing token.'], 401);
        }

        // normalisasi roles dari parameter
        $roles = array_map(static fn($r) => strtolower(trim($r)), $roles);

        if (!in_array($role, $roles, true)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Access denied: role not permitted.',
            ], 403);
        }

        return $next($request);
    }
}
