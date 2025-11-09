<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Support\AuthToken;
use Illuminate\Support\Facades\Redis;


class ThreadViewsController extends Controller
{
    public function detail(Request $request, int $id)
    {
        [$uid] = AuthToken::assertRoleFresh($request, ['ibu_hamil', 'bidan']);

        $thread = DB::table('threads')
            ->select('thread_id', 'user_id', 'category', 'content', 'views', 'created_at')
            ->where('thread_id', $id)
            ->first();

        if (! $thread) {
            return response()->json(['status' => 'error', 'message' => 'Thread tidak ditemukan'], 404);
        }

        $redis = Redis::connection();
        $redis->select(1);

        $viewKey = "thread:views:$id";
        $userViewKey = "thread:viewed:$id:$uid";
        $ttlSeconds = (int) env('THREAD_VIEW_TTL_HOURS', 24) * 3600;

        if (! $redis->exists($userViewKey)) {
            $views = $redis->incr($viewKey);
            $redis->setex($userViewKey, $ttlSeconds, 1);

            DB::table('threads')->where('thread_id', $id)->update(['views' => $views]);
            DB::statement('INSERT IGNORE INTO thread_views (thread_id, user_id, created_at) VALUES (?, ?, NOW())', [$id, $uid]);
        } else {
            $views = (int) ($redis->get($viewKey) ?? $thread->views ?? 0);
        }

        $ttl = $redis->ttl($userViewKey);

        return response()->json([
            'status' => 'success',
            'data' => [
                'thread_id'  => $thread->thread_id,
                'user_id'    => $thread->user_id,
                'category'   => $thread->category,
                'content'    => $thread->content,
                'views'      => $views,
                'ttl'        => $ttl,
                'created_at' => $thread->created_at,
            ],
        ]);
    }


    public function showCache(Request $request)
    {
        [$uid] = AuthToken::assertRoleFresh($request, ['admin']);

        $redis = Redis::connection();
        $redis->select(1);

        $keys = array_unique(array_merge(
            $redis->keys('thread:views:*'),
            $redis->keys('thread:viewed:*'),
            $redis->keys('laravel_cache:thread:view*')
        ));

        $data = ['views' => [], 'viewed' => []];

        foreach ($keys as $key) {
            $ttl  = $redis->ttl($key);
            $val  = $redis->get($key);

            if ($ttl === -2) {
                $alt = str_starts_with($key, 'laravel_cache:')
                    ? substr($key, strlen('laravel_cache:'))
                    : 'laravel_cache:' . $key;

                $ttlAlt = $redis->ttl($alt);
                if ($ttlAlt !== -2) {
                    $key = $alt;
                    $ttl = $ttlAlt;
                    $val = $redis->get($alt);
                }
            }

            if (str_contains($key, 'thread:views:')) {
                $threadId = (int) preg_replace('/.*thread:views:(\d+)$/', '$1', $key);
                $data['views'][] = [
                    'key'       => $key,
                    'thread_id' => $threadId,
                    'value'     => $val,
                    'ttl'       => $ttl,
                ];
            } elseif (str_contains($key, 'thread:viewed:')) {
                if (preg_match('/thread:viewed:(\d+):(\d+)$/', $key, $m)) {
                    $data['viewed'][] = [
                        'key'       => $key,
                        'thread_id' => (int) $m[1],
                        'user_id'   => (int) $m[2],
                        'value'     => $val,
                        'ttl'       => $ttl,
                    ];
                } else {
                    $data['viewed'][] = [
                        'key'       => $key,
                        'thread_id' => null,
                        'user_id'   => null,
                        'value'     => $val,
                        'ttl'       => $ttl,
                    ];
                }
            }
        }

        return response()->json([
            'status'       => 'success',
            'views_count'  => count($data['views']),
            'viewed_count' => count($data['viewed']),
            'data'         => $data,
        ]);
    }
}
