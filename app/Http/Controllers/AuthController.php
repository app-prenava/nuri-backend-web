<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    private function issueToken(User $user): string
    {
        $claims = [
            'uid'   => $user->user_id,
            'role'  => $user->role,
            'name'  => $user->name,
            'email' => $user->email,
            'tv'    => (int) $user->token_version,
        ];

        $ttlMap   = config('auth_tokens.ttl_seconds');
        $ttlSec   = $ttlMap[$user->role] ?? ($ttlMap['default'] ?? 0);

        if ($ttlSec && $ttlSec > 0) {
            $claims['exp'] = now()->addSeconds((int) $ttlSec)->timestamp;
        }

        return JWTAuth::claims($claims)->fromUser($user);
    }
    public function register(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if ($v->fails()) {
            return response()->json($v->errors(), 422);
        }

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'ibu_hamil',
            'is_active' => true,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'User registered successfully',
            'user'    => $this->userPayload($user),
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'email'    => ['required','email'],
            'password' => ['required','string','min:6'],
        ]);
        if ($v->fails()) return response()->json($v->errors(), 422);

        $cred = $request->only('email','password');

        $user = User::where('email',$cred['email'])->first();
        if (!$user) {
            return response()->json(['status'=>'error','message'=>'Unauthorized'], 401);
        }

        if (!$user->is_active) {
            return response()->json(['status'=>'error','message'=>'Account is deactivated. Contact admin.'], 403);
        }

        if (!auth('api')->attempt($cred)) {
            return response()->json(['status'=>'error','message'=>'Unauthorized'], 401);
        }

        $token = $this->issueToken($user);

        $ttlMap   = config('auth_tokens.ttl_seconds');
        $ttlSec   = $ttlMap[$user->role] ?? ($ttlMap['default'] ?? 0);

        return response()->json([
            'status' => 'success',
            'user'   => [
                'user_id' => $user->user_id,
                'name'    => $user->name,
                'email'   => $user->email,
                'role'    => $user->role,
            ],
            'authorization' => [
                'token'      => $token,
                'type'       => 'bearer',
                'expires_in' => $ttlSec ?: null,
            ],
        ]);
    }

    public function logout(): JsonResponse
    {
        try { \Tymon\JWTAuth\Facades\JWTAuth::invalidate(\Tymon\JWTAuth\Facades\JWTAuth::getToken()); } catch (\Throwable $e) {}

        return response()->json(['status'=>'success','message'=>'Successfully logged out']);
    }

    public function refresh(Request $request): JsonResponse
    {
        [$uid, $role, $payload] = \App\Support\AuthToken::ensureActiveAndFreshOrFail($request);
        $user = User::select('user_id','name','email','role','token_version')->find($uid);
        $token = $this->issueToken($user);

        $ttlMap   = config('auth_tokens.ttl_seconds');
        $ttlSec   = $ttlMap[$user->role] ?? ($ttlMap['default'] ?? 0);

        return response()->json([
            'status' => 'success',
            'user'   => [
                'user_id' => $user->user_id,
                'name'    => $user->name,
                'email'   => $user->email,
                'role'    => $user->role,
            ],
            'authorization' => [
                'token'      => $token,
                'type'       => 'bearer',
                'expires_in' => $ttlSec ?: null,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        [$uid, $role] = \App\Support\AuthToken::ensureActiveAndFreshOrFail($request);
        $user = User::select('user_id','name','email','role')->find($uid);

        return response()->json($user);
    }

    public function change(Request $request): JsonResponse
    {
        try {
            $token   = JWTAuth::parseToken()->getToken();
            $payload = JWTAuth::setToken($token)->getPayload();
            $uid     = (int) $payload->get('uid');
        } catch (TokenExpiredException $e) {
            abort(response()->json(['status'=>'error','message'=>'Token has expired.'], 401));
        } catch (TokenInvalidException $e) {
            abort(response()->json(['status'=>'error','message'=>'Token is invalid.'], 401));
        } catch (JWTException $e) {
            abort(response()->json(['status'=>'error','message'=>'Unable to parse token.'], 401));
        } catch (\Throwable $e) {
            abort(response()->json(['status'=>'error','message'=>'Invalid or missing token.'], 401));
        }

        $data = $request->validate([
            'new_password' => ['required','string','min:6'],
        ]);

        $user = DB::table('users')->where('user_id', $uid)->first();
        if (! $user) {
            return response()->json(['status'=>'error','message'=>'User not found.'], 404);
        }

        DB::table('users')
            ->where('user_id', $uid)
            ->update([
                'password'      => Hash::make($data['new_password']),
                'token_version' => DB::raw('token_version + 1'),
                'updated_at'    => now(),
            ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Password changed. Previous tokens revoked.',
        ], 200);
    }

    private function makeToken(User $user): string
    {
        /** @var \Tymon\JWTAuth\JWTGuard $jwt */
        $jwt = Auth::guard('api');

        $builder = $jwt->claims([
            'uid'   => $user->user_id,
            'role'  => $user->role,
            'name'  => $user->name,
            'email' => $user->email,
        ]);

        if (in_array($user->role, ['bidan', 'dinkes'])) {
            config(['jwt.ttl' => 120]);
        } else {
            config(['jwt.ttl' => null]);
        }

        return $builder->fromUser($user);
    }

    private function userPayload(User $user): array
    {
        return [
            'user_id' => $user->user_id,
            'name'    => $user->name,
            'email'   => $user->email,
            'role'    => $user->role,
        ];
    }
}
