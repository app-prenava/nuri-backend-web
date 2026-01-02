<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Support\AuthToken;
use Illuminate\Support\Collection;
use App\Services\SupabaseService;


class BannerController extends Controller
{
    protected SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function create(Request $request): JsonResponse
    {
        [,$role] = AuthToken::assertRoleFresh($request, 'admin');

        $messages = [
            'photo.mimes' => 'File harus berupa foto',
            'photo.max'   => 'Ukuran file melebihi batas upload, pastikan file dibawah 500KB',
            'url.url'     => 'Data URL belum benar, input dengan format lengkap',
        ];

        $data = $request->validate([
            'name'      => ['required','string','max:255'],
            'photo'     => ['required','file','mimes:jpg,jpeg,png,webp','max:2000'],
            'is_active' => ['nullable','boolean'],
            'url'       => ['required','string','max:2048','url'],
        ], $messages);

        // Upload ke Supabase Storage
        $uploadResult = $this->supabase->uploadFile($request->file('photo'), 'banners');

        if (!$uploadResult) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengupload foto ke Supabase',
            ], 500);
        }

        DB::table('ad_banner')->insert([
            'name'       => $data['name'],
            'photo'      => $uploadResult['public_url'],
            'is_active'  => isset($data['is_active']) ? (bool)$data['is_active'] : true,
            'url'        => $data['url'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Banner berhasil ditambahkan',
            'data'    => [
                'name'       => $data['name'],
                'is_active'  => isset($data['is_active']) ? (bool)$data['is_active'] : true,
                'photo'      => $uploadResult['public_url'],
            ],
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        [,$role] = AuthToken::assertRoleFresh($request, 'admin');

        $banner = DB::table('ad_banner')->where('id', $id)->first();
        if (! $banner) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data banner tidak ada',
            ], 404);
        }

        $messages = [
            'photo.mimes' => 'File harus berupa foto',
            'photo.max'   => 'Ukuran file melebihi batas upload, pastikan file dibawah 500KB',
            'url.url'     => 'Data URL belum benar, input dengan format lengkap',
        ];

        $data = $request->validate([
            'name'      => ['sometimes','required','string','max:255'],
            'photo'     => ['sometimes','file','mimes:jpg,jpeg,png,webp','max:500'],
            'is_active' => ['sometimes','boolean'],
            'url'       => ['sometimes','required','string','max:2048','url'],
        ], $messages);

        $update = [];
        if ($request->has('name'))      $update['name'] = $data['name'];
        if ($request->has('is_active')) $update['is_active'] = (bool)$data['is_active'];
        if ($request->has('url'))       $update['url'] = $data['url'];

        if ($request->hasFile('photo')) {
            // Upload ke Supabase Storage
            $uploadResult = $this->supabase->uploadFile($request->file('photo'), 'banners');

            if ($uploadResult) {
                $update['photo'] = $uploadResult['public_url'];

                // Hapus file lama dari Supabase jika URL lengkap tersimpan
                if (!empty($banner->photo)) {
                    $oldPath = $this->extractPathFromUrl($banner->photo);
                    if ($oldPath) {
                        $this->supabase->delete($oldPath);
                    }
                }
            }
        }

        if (!empty($update)) {
            $update['updated_at'] = now();
            DB::table('ad_banner')->where('id', $id)->update($update);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data banner berhasil diupdate',
        ], 200);
    }

    public function delete(Request $request, int $id): JsonResponse
    {
        [,$role] = AuthToken::assertRoleFresh($request, 'admin');

        $banner = DB::table('ad_banner')->where('id', $id)->first();
        if (! $banner) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data banner tidak ada',
            ], 404);
        }

        // Hapus file dari Supabase
        if (!empty($banner->photo)) {
            $path = $this->extractPathFromUrl($banner->photo);
            if ($path) {
                $this->supabase->delete($path);
            }
        }

        DB::table('ad_banner')->where('id', $id)->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data banner berhasil dihapus',
        ], 200);
    }

    public function ShowOnProd(Request $request): JsonResponse
    {
        [,$role] = AuthToken::assertRoleFresh($request, ['ibu_hamil','admin']);

        $rows = DB::table('ad_banner')
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->limit(7)
            ->get(['id','name','photo','url','is_active','created_at']);

        return response()->json([
            'status' => 'success',
            'data'   => $this->transformBannerPhotos($rows),
        ]);
    }

    public function ShowAll(Request $request): JsonResponse
    {
        [,$role] = AuthToken::assertRoleFresh($request, ['ibu_hamil','admin']);

        $rows = DB::table('ad_banner')
            ->orderByDesc('created_at')
            ->get(['id','name','photo','url','is_active','created_at']);

        return response()->json([
            'status' => 'success',
            'data'   => $this->transformBannerPhotos($rows),
        ]);
    }

    /** @return \Illuminate\Support\Collection */
    private function transformBannerPhotos(Collection $rows): Collection
    {
        // Photo sudah berupa public URL dari Supabase, tidak perlu transformasi
        return $rows;
    }

    /**
     * Extract path dari Supabase URL
     * Contoh: https://xxx.supabase.co/storage/v1/object/public/images/banners/xxx.jpg
     * Result: banners/xxx.jpg
     */
    private function extractPathFromUrl(string $url): ?string
    {
        $pattern = '/\/storage\/v1\/object\/public\/images\/(.+)$/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

}
