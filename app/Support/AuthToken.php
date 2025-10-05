<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

final class AuthToken
{
    public static function payloadOrFail(Request $request)
    {
        try {
            $token = \Tymon\JWTAuth\Facades\JWTAuth::getToken();

            if (!$token) {
                $authHeader = $request->header('Authorization') ?: $request->server('HTTP_AUTHORIZATION');
                if ($authHeader && preg_match('/^Bearer\s+(.+)$/i', $authHeader, $m)) {
                    $token = $m[1];
                }
            }

            if (!$token) {
                abort(response()->json(['status'=>'error','message'=>'Missing Authorization: Bearer <token> header.'], 401));
            }

            return \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->getPayload();

        } catch (TokenExpiredException $e) {
            abort(response()->json(['status'=>'error','message'=>'Token has expired.'], 401));
        } catch (TokenInvalidException $e) {
            abort(response()->json(['status'=>'error','message'=>'Token is invalid.'], 401));
        } catch (JWTException $e) {
            abort(response()->json(['status'=>'error','message'=>'Unable to parse token.'], 401));
        } catch (\Throwable $e) {
            abort(response()->json(['status'=>'error','message'=>'Invalid or missing token.'], 401));
        }
    }

    public static function uidRoleOrFail(Request $request): array
    {
        JWTAuth::parseToken()->getToken();
        
        $p = self::payloadOrFail($request);
        $uid  = (int) $p->get('uid');
        $role = strtolower((string) $p->get('role'));

        if (!$uid || !$role) {
            abort(response()->json(['status'=>'error','message'=>'Token missing required claims (uid/role).'], 401));
        }
        return [$uid, $role, $p];
    }

    public static function ensureActiveAndFreshOrFail(Request $request): array
    {
        [$uid, $role, $payload] = self::uidRoleOrFail($request);
        $tv = (int) $payload->get('tv');

        $row = DB::table('users')
            ->where('user_id', $uid)
            ->select('is_active','token_version')
            ->first();

        if (!$row || !$row->is_active || (int)$row->token_version !== $tv) {
            abort(response()->json(['status'=>'error','message'=>'Token revoked or account deactivated.'], 401));
        }

        return [$uid, $role, $payload];
    }
}
