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
            ->where('user_id', $uid)
            ->select('thread_bookmarks_id', 'thread_id', 'created_at')
            ->get();

        if ($bookmarks->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        }

        $threadIds = $bookmarks->pluck('thread_id')->all();

        $validThreadIds = DB::table('threads')
            ->whereIn('thread_id', $threadIds)
            ->pluck('thread_id')
            ->all();

        $deleted = array_diff($threadIds, $validThreadIds);

        if (! empty($deleted)) {
            DB::table('thread_bookmarks')
                ->where('user_id', $uid)
                ->whereIn('thread_id', $deleted)
                ->delete();
        }

        $final = $bookmarks->filter(function ($b) use ($validThreadIds) {
            return in_array($b->thread_id, $validThreadIds);
        })->values();

        return response()->json([
            'status' => 'success',
            'data' => $final,
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
