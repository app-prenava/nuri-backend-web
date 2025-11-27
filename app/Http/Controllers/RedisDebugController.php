<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;

class RedisDebugController extends Controller
{
    public function likes(): JsonResponse
    {
        $redis = Redis::connection('likes');
        $redis->select(2);

        $keys = $redis->keys('thread:likes:*');
        $totalKeys = count($keys);

        $data = [];

        foreach ($keys as $key) {
            $realKey = str_replace('laravel_cache:', '', $key); // ✅ hilangkan prefix
            $threadId = (int) str_replace('thread:likes:', '', $realKey);
            $likes = (int) $redis->get($realKey); // ✅ ambil pakai key tanpa prefix

            $data[] = [
                'key' => $key,
                'thread_id' => $threadId,
                'likes' => $likes,
            ];
        }

        return response()->json([
            'status' => 'success',
            'total_keys' => $totalKeys,
            'data' => $data,
        ]);
    }
}
