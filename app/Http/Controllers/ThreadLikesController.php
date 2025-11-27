<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Support\AuthToken;

class ThreadLikesController extends Controller
{
    public function like(Request $request, int $id)
    {
        [$uid] = AuthToken::assertRoleFresh($request, ['ibu_hamil', 'bidan']);

        $thread = DB::table('threads')
            ->select('thread_id', 'likes_count')
            ->where('thread_id', $id)
            ->first();

        if (! $thread) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thread not found',
            ], 404);
        }

        $redis = Redis::connection('likes');

        $likeKey = "thread:likes:$id";
        $userLikeKey = "thread:liked:$id:$uid";
        $ttl = (int) env('THREAD_LIKE_TTL_HOURS', 24) * 3600;

        if (! $redis->exists($userLikeKey)) {
            $likes = $redis->incr($likeKey);
            $redis->expire($likeKey, $ttl);
            $redis->setex($userLikeKey, $ttl, 1);
            $action = 'liked';
        } else {
            $likes = max(0, $redis->decr($likeKey));
            $redis->del($userLikeKey);
            $action = 'unliked';
        }

        return response()->json([
            'status' => 'success',
            'message' => "Thread successfully {$action}.",
            'data' => [
                'thread_id'   => $id,
                'likes_count' => $likes,
                'liked'       => (bool) $redis->exists($userLikeKey),
            ],
        ]);
    }

    
}
