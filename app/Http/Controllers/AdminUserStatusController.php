<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Support\AuthToken;
use Tymon\JWTAuth\Facades\JWTAuth;


class AdminUserStatusController extends Controller
{
    public function deactivate(Request $request, int $userId): JsonResponse
    {
        [, $role] = AuthToken::uidRoleOrFail($request);
        if ($role !== 'admin') {
            return response()->json(['status'=>'error','message'=>'Unauthorized: admin role required.'], 401);
        }

        $updated = DB::table('users')
            ->where('user_id', $userId)
            ->update([
                'is_active'     => false,
                'token_version' => DB::raw('token_version + 1'),
                'updated_at'    => now(),
            ]);

        if (!$updated) {
            return response()->json(['status'=>'error','message'=>'User not found.'], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'User deactivated and all tokens revoked.',
        ]);
    }

    public function activate(Request $request, int $userId): JsonResponse
    {
        [, $role] = AuthToken::uidRoleOrFail($request);
        if ($role !== 'admin') {
            return response()->json(['status'=>'error','message'=>'Unauthorized: admin role required.'], 401);
        }

        $updated = DB::table('users')
            ->where('user_id', $userId)
            ->update([
                'is_active'     => true,
                'token_version' => DB::raw('token_version + 1'),
                'updated_at'    => now(),
            ]);

        if (!$updated) {
            return response()->json(['status'=>'error','message'=>'User not found.'], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'User activated. Old tokens revoked.',
        ]);
    }
}
