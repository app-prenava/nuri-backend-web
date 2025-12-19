<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Support\AuthToken;
use App\Models\PregnancyTip;
use App\Models\TipCategory;

class PregnancyTipController extends Controller
{
    /**
     * GET /api/tips
     * Public endpoint untuk mendapatkan semua tips (hanya published)
     * Support filter by category dan search
     */
    public function index(Request $request): JsonResponse
    {
        $query = PregnancyTip::with(['category', 'creator'])
            ->published()
            ->orderBy('order')
            ->orderByDesc('created_at');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by category slug
        if ($request->has('category_slug')) {
            $category = TipCategory::where('slug', $request->category_slug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Search by judul atau konten
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                  ->orWhere('konten', 'like', '%' . $search . '%');
            });
        }

        $tips = $query->get()->map(function ($tip) {
            return [
                'id' => $tip->id,
                'judul' => $tip->judul,
                'title' => $tip->judul, // Also provide 'title' alias for frontend compatibility
                'konten' => $tip->konten,
                'content' => $tip->konten, // Also provide 'content' alias for frontend compatibility
                'category' => $tip->category ? [
                    'id' => $tip->category->id,
                    'name' => $tip->category->name,
                    'slug' => $tip->category->slug,
                    'icon_name' => $tip->category->icon_name,
                    'icon_url' => $tip->category->icon_url,
                ] : null,
                'categoryId' => $tip->category_id, // Include category_id directly as backup
                'created_by' => $tip->creator ? [
                    'id' => $tip->creator->user_id,
                    'name' => $tip->creator->name,
                ] : null,
                // penting untuk FE: status publish & urutan
                'is_published' => $tip->is_published,
                'order' => $tip->order,
                'created_at' => $tip->created_at,
                'updated_at' => $tip->updated_at,
            ];
        });

        return response()->json([
            'status' => 'berhasil',
            'data' => $tips,
        ], 200);
    }

    /**
     * GET /api/tips/{id}
     * Public endpoint untuk mendapatkan detail tip
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $tip = PregnancyTip::with(['category', 'creator'])
            ->published()
            ->find($id);

        if (!$tip) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tip tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => 'berhasil',
            'data' => [
                'id' => $tip->id,
                'judul' => $tip->judul,
                'title' => $tip->judul, // Also provide 'title' alias for frontend compatibility
                'konten' => $tip->konten,
                'content' => $tip->konten, // Also provide 'content' alias for frontend compatibility
                'category' => $tip->category ? [
                    'id' => $tip->category->id,
                    'name' => $tip->category->name,
                    'slug' => $tip->category->slug,
                    'icon_name' => $tip->category->icon_name,
                    'icon_url' => $tip->category->icon_url,
                ] : null,
                'categoryId' => $tip->category_id, // Include category_id directly as backup
                'created_by' => $tip->creator ? [
                    'id' => $tip->creator->user_id,
                    'name' => $tip->creator->name,
                ] : null,
                'created_at' => $tip->created_at,
                'updated_at' => $tip->updated_at,
            ],
        ], 200);
    }

    /**
     * POST /api/tips
     * Admin/Bidan only - Create tip baru
     */
    public function store(Request $request): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, ['admin', 'bidan']);

        $validator = Validator::make($request->all(), [
            'category_id' => ['required', 'integer', 'exists:tip_categories,id'],
            'judul' => ['required', 'string', 'max:255'],
            'konten' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tip = PregnancyTip::create([
            'category_id' => $request->category_id,
            'created_by' => $uid,
            'judul' => $request->judul,
            'konten' => $request->konten,
            'is_published' => $request->is_published ?? true,
            'order' => $request->order ?? 0,
        ]);

        $tip->load(['category', 'creator']);

        return response()->json([
            'status' => 'berhasil',
            'message' => 'Tip berhasil ditambahkan',
            'data' => [
                'id' => $tip->id,
                'judul' => $tip->judul,
                'title' => $tip->judul, // Also provide 'title' alias for frontend compatibility
                'konten' => $tip->konten,
                'content' => $tip->konten, // Also provide 'content' alias for frontend compatibility
                'category' => $tip->category ? [
                    'id' => $tip->category->id,
                    'name' => $tip->category->name,
                    'slug' => $tip->category->slug,
                    'icon_name' => $tip->category->icon_name,
                    'icon_url' => $tip->category->icon_url,
                ] : null,
                'categoryId' => $tip->category_id, // Include category_id directly as backup
                'created_by' => $tip->creator ? [
                    'id' => $tip->creator->user_id,
                    'name' => $tip->creator->name,
                ] : null,
                'is_published' => $tip->is_published,
                'created_at' => $tip->created_at,
                'updated_at' => $tip->updated_at,
            ],
        ], 201);
    }

    /**
     * PUT /api/tips/{id}
     * Admin/Bidan only - Update tip (hanya tip miliknya atau admin bisa update semua)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, ['admin', 'bidan']);

        $tip = PregnancyTip::find($id);
        if (!$tip) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tip tidak ditemukan',
            ], 404);
        }

        // Cek authorization: hanya admin atau creator yang bisa update
        if ($role !== 'admin' && $tip->created_by !== $uid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki izin untuk mengupdate tip ini',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => ['sometimes', 'required', 'integer', 'exists:tip_categories,id'],
            'judul' => ['sometimes', 'required', 'string', 'max:255'],
            'konten' => ['sometimes', 'required', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tip->update($request->only([
            'category_id', 'judul', 'konten', 'is_published', 'order'
        ]));

        $tip->load(['category', 'creator']);

        return response()->json([
            'status' => 'berhasil',
            'message' => 'Tip berhasil diperbarui',
            'data' => [
                'id' => $tip->id,
                'judul' => $tip->judul,
                'title' => $tip->judul, // Also provide 'title' alias for frontend compatibility
                'konten' => $tip->konten,
                'content' => $tip->konten, // Also provide 'content' alias for frontend compatibility
                'category' => $tip->category ? [
                    'id' => $tip->category->id,
                    'name' => $tip->category->name,
                    'slug' => $tip->category->slug,
                    'icon_name' => $tip->category->icon_name,
                    'icon_url' => $tip->category->icon_url,
                ] : null,
                'categoryId' => $tip->category_id, // Include category_id directly as backup
                'created_by' => $tip->creator ? [
                    'id' => $tip->creator->user_id,
                    'name' => $tip->creator->name,
                ] : null,
                'is_published' => $tip->is_published,
                'created_at' => $tip->created_at,
                'updated_at' => $tip->updated_at,
            ],
        ], 200);
    }

    /**
     * DELETE /api/tips/{id}
     * Admin/Bidan only - Delete tip (hanya tip miliknya atau admin bisa delete semua)
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, ['admin', 'bidan']);

        $tip = PregnancyTip::find($id);
        if (!$tip) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tip tidak ditemukan',
            ], 404);
        }

        // Cek authorization: hanya admin atau creator yang bisa delete
        if ($role !== 'admin' && $tip->created_by !== $uid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki izin untuk menghapus tip ini',
            ], 403);
        }

        $tip->delete();

        return response()->json([
            'status' => 'berhasil',
            'message' => 'Tip berhasil dihapus',
        ], 200);
    }
}
