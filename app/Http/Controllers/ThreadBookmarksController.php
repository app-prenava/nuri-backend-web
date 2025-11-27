<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\AuthToken;

class ThreadBookmarksController extends Controller
{
    public function store(Request $request)
    {
        [$uid] = AuthToken::assertRoleFresh($request, ['ibu_hamil', 'bidan']);

        $request->validate([
            'thread_id' => 'required|integer',
        ]);

        $threadId = $request->thread_id;

        $exists = DB::table('threads')
            ->where('thread_id', $threadId)
            ->exists();

        if (! $exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thread not found.'
            ], 404);
        }

        $already = DB::table('thread_bookmarks')
            ->where('thread_id', $threadId)
            ->where('user_id', $uid)
            ->exists();

        if ($already) {
            return response()->json([
                'status' => 'error',
                'message' => 'Already bookmarked.'
            ], 409);
        }

        DB::table('thread_bookmarks')->insert([
            'thread_id'  => $threadId,
            'user_id'    => $uid,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Bookmark added successfully.',
            'data' => [
                'thread_id' => $threadId,
                'user_id'   => $uid,
            ]
        ]);
    }

    public function index(Request $request)
    {
        [$uid] = AuthToken::assertRoleFresh($request, ['ibu_hamil', 'bidan']);

        $bookmarks = DB::table('thread_bookmarks')
            ->where('thread_bookmarks.user_id', $uid)
            ->pluck('thread_id', 'thread_bookmarks_id');

        if ($bookmarks->isEmpty()) {
            return response()->json(['status' => 'success', 'data' => []]);
        }

        $threadIds = $bookmarks->values()->all();

        $threads = DB::table('threads')
            ->join('users', 'threads.user_id', '=', 'users.user_id')
            ->whereIn('threads.thread_id', $threadIds)
            ->select(
                'threads.thread_id',
                'threads.user_id',
                'users.name',
                'threads.content',
                'threads.likes_count',
                'threads.views',
                'threads.created_at'
            )
            ->get();

        $validThreadIds = $threads->pluck('thread_id')->all();
        $deleted = array_diff($threadIds, $validThreadIds);

        if (! empty($deleted)) {
            DB::table('thread_bookmarks')
                ->where('user_id', $uid)
                ->whereIn('thread_id', $deleted)
                ->delete();
        }

        $data = [];

        foreach ($threads as $t) {
            $bookmarkId = $bookmarks->search($t->thread_id);

            $cleanContent = preg_replace('/<img[^>]*>/i', '', $t->content);
            $cleanContent = mb_strimwidth(strip_tags($cleanContent), 0, 120, '...');

            $data[] = [
                'thread_bookmarks_id' => $bookmarkId,
                'thread_id'           => $t->thread_id,
                'user_name'           => $t->name,
                'content_preview'     => $cleanContent,
                'likes_count'         => $t->likes_count,
                'views'               => $t->views,
                'created_ad'          => $t->created_at,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => array_values($data),
        ]);
    }


    public function destroy(Request $request, int $id)
    {
        [$uid] = AuthToken::assertRoleFresh($request, ['ibu_hamil', 'bidan']);

        $deleted = DB::table('thread_bookmarks')
            ->where('thread_bookmarks_id', $id)
            ->where('user_id', $uid)
            ->delete();

        if (! $deleted) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bookmark not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Bookmark deleted successfully.'
        ]);
    }
}
