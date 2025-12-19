<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Support\AuthToken;
use App\Models\TipCategory;

class TipCategoryController extends Controller
{
    /**
     * GET /api/tips/categories
     * Public endpoint untuk mendapatkan semua kategori aktif
     */
    public function index(Request $request): JsonResponse
    {
        $categories = TipCategory::active()
            ->orderBy('order')
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'icon_name' => $category->icon_name,
                    'icon_url' => $category->icon_url,
                    'description' => $category->description,
                    'tips_count' => $category->tips()->published()->count(),
                ];
            });

        return response()->json([
            'status' => 'berhasil',
            'data' => $categories,
        ], 200);
    }

    /**
     * GET /api/tips/categories/all
     * Admin only - Get semua kategori termasuk yang inactive
     */
    public function getAll(Request $request): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, 'admin');

        $categories = TipCategory::orderBy('order')
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'icon_name' => $category->icon_name,
                    'icon_url' => $category->icon_url,
                    'description' => $category->description,
                    'order' => $category->order,
                    'is_active' => $category->is_active, // Include is_active untuk admin
                    'tips_count' => $category->tips()->published()->count(),
                ];
            });

        return response()->json([
            'status' => 'berhasil',
            'data' => $categories,
        ], 200);
    }

    /**
     * POST /api/tips/categories
     * Admin only - Create kategori baru
     */
    public function store(Request $request): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, 'admin');

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', 'unique:tip_categories,slug'],
            'icon_name' => ['nullable', 'string', 'max:100'],
            'icon_url' => ['nullable', 'string', 'max:2000'], // Menerima SVG string atau URL
            'description' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable'], // Terima integer (0/1) atau boolean
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Convert is_active ke boolean (terima integer 0/1 atau boolean)
        $isActive = true; // default
        if ($request->has('is_active')) {
            $isActiveValue = $request->is_active;
            if (is_numeric($isActiveValue)) {
                $isActive = (int)$isActiveValue === 1;
            } else {
                $isActive = (bool)$isActiveValue;
            }
        }

        $category = TipCategory::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'icon_name' => $request->icon_name,
            'icon_url' => $request->icon_url,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'is_active' => $isActive,
        ]);

        return response()->json([
            'status' => 'berhasil',
            'message' => 'Kategori berhasil ditambahkan',
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'icon_name' => $category->icon_name,
                'icon_url' => $category->icon_url,
                'description' => $category->description,
                'order' => $category->order,
                'is_active' => $category->is_active,
            ],
        ], 201);
    }

    /**
     * PUT /api/tips/categories/{id}
     * Admin only - Update kategori
     */
    public function update(Request $request, int $id): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, 'admin');

        $category = TipCategory::find($id);
        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'slug' => ['sometimes', 'required', 'string', 'max:100', 'unique:tip_categories,slug,' . $id],
            'icon_name' => ['nullable', 'string', 'max:100'],
            'icon_url' => ['nullable', 'string', 'max:2000'], // Menerima SVG string atau URL
            'description' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable'], // Terima integer (0/1) atau boolean
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Prepare update data
        $updateData = $request->only([
            'name', 'slug', 'icon_name', 'icon_url', 'description', 'order'
        ]);

        // Convert is_active ke boolean jika ada di request
        if ($request->has('is_active')) {
            $isActiveValue = $request->is_active;
            if (is_numeric($isActiveValue)) {
                $updateData['is_active'] = (int)$isActiveValue === 1;
            } else {
                $updateData['is_active'] = (bool)$isActiveValue;
            }
        }

        $category->update($updateData);

        return response()->json([
            'status' => 'berhasil',
            'message' => 'Kategori berhasil diperbarui',
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'icon_name' => $category->icon_name,
                'icon_url' => $category->icon_url,
                'description' => $category->description,
                'order' => $category->order,
                'is_active' => $category->is_active,
            ],
        ], 200);
    }

    /**
     * DELETE /api/tips/categories/{id}
     * Admin only - Delete kategori
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, 'admin');

        $category = TipCategory::find($id);
        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }

        // Cek apakah ada tips yang menggunakan kategori ini
        $tipsCount = $category->tips()->count();
        if ($tipsCount > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak dapat dihapus karena masih memiliki ' . $tipsCount . ' tips',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'status' => 'berhasil',
            'message' => 'Kategori berhasil dihapus',
        ], 200);
    }
}
