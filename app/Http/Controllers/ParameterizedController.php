<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Support\AuthToken;

class ParameterizedController extends Controller
{

    public function store(Request $request): JsonResponse
    {
        [$uid] = AuthToken::assertRoleFresh($request, ['admin']);

        $validated = $request->validate([
            'key'   => 'required|string|max:150',
            'value' => 'nullable|string|max:255',
        ]);

        $id = DB::table('parameterized')->insertGetId([
            'key'        => $validated['key'],
            'value'      => $validated['value'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Parameter created successfully.',
            'data' => [
                'parameterized_id' => $id,
                'key' => $validated['key'],
                'value' => $validated['value'] ?? null,
            ],
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        [$uid] = AuthToken::assertRoleFresh($request, ['admin']);

        $validated = $request->validate([
            'key'   => 'required|string|max:150',
            'value' => 'nullable|string|max:255',
        ]);

        $exists = DB::table('parameterized')->where('parameterized_id', $id)->exists();

        if (! $exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter not found.',
            ], 404);
        }

        DB::table('parameterized')
            ->where('parameterized_id', $id)
            ->update([
                'key'        => $validated['key'],
                'value'      => $validated['value'] ?? null,
                'updated_at' => now(),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Parameter updated successfully.',
            'data' => [
                'parameterized_id' => $id,
                'key' => $validated['key'],
                'value' => $validated['value'] ?? null,
            ],
        ]);
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        [$uid] = AuthToken::assertRoleFresh($request, ['admin']);

        $deleted = DB::table('parameterized')
            ->where('parameterized_id', $id)
            ->delete();

        if (! $deleted) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter not found or already deleted.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Parameter deleted successfully.',
            'data' => ['parameterized_id' => $id],
        ]);
    }
}
