<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Support\AuthToken;
use App\Helpers\ShopLog;
use App\Models\ShopReview;
use App\Services\SupabaseService;

class ShopController extends Controller
{
    protected SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    protected function formatPrice(string $price): string
    {
        $clean = preg_replace('/\D/', '', $price);
        if ($clean === '') return '0';
        return number_format((int)$clean, 0, ',', '.');
    }

    public function getAll(Request $request)
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, ['ibu_hamil', 'admin']);

        $data = (int) $request->query('data', 30);
        $page = (int) $request->query('page', 1);
        $category = $request->query('category');

        if ($data < 1) $data = 1;
        if ($data > 100) $data = 100;
        if ($page < 1) $page = 1;

        $query = DB::table('shop')
            ->leftJoin('users', 'shop.user_id', '=', 'users.user_id')
            ->select('shop.*', 'users.name as seller_name');

        if (!empty($category)) {
            $query->where('category', $category);
        }

        $query->orderByDesc('product_id');

        $total = $query->count();
        $result = $query
            ->offset(($page - 1) * $data)
            ->limit($data)
            ->get();
        // Photo sudah berupa public URL dari Supabase

        return response()->json([
            'current_page' => $page,
            'per_page'     => $data,
            'total'        => $total,
            'last_page'    => (int) ceil($total / $data),
            'from'         => ($page - 1) * $data + 1,
            'to'           => ($page - 1) * $data + count($result),
            'data'         => $result
        ]);
    }

    public function getByUser(Request $request)
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, 'ibu_hamil');

        $data = (int) $request->query('data', 30);
        $page = (int) $request->query('page', 1);
        $category = $request->query('category');

        if ($data < 1) $data = 1;
        if ($data > 100) $data = 100;
        if ($page < 1) $page = 1;

        $query = DB::table('shop')
            ->leftJoin('users', 'shop.user_id', '=', 'users.user_id')
            ->select('shop.*', 'users.name as seller_name')
            ->where('shop.user_id', $uid);

        if (!empty($category)) {
            $query->where('category', $category);
        }

        $query->orderByDesc('product_id');

        $total = $query->count();
        $result = $query
            ->offset(($page - 1) * $data)
            ->limit($data)
            ->get();
        // Photo sudah berupa public URL dari Supabase

        return response()->json([
            'current_page' => $page,
            'per_page'     => $data,
            'total'        => $total,
            'last_page'    => (int) ceil($total / $data),
            'from'         => ($page - 1) * $data + 1,
            'to'           => ($page - 1) * $data + count($result),
            'data'         => $result,
        ]);
    }


    public function create(Request $request): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, 'ibu_hamil');

        $messages = [
            'photo.image' => 'File harus berupa foto',
            'photo.mimes' => 'File harus berupa foto',
            'photo.max'   => 'Ukuran file melebihi batas upload, pastikan file dibawah 500KB',
            'url.url'     => 'Data URL belum benar, input dengan format lengkap',
        ];

        $v = Validator::make($request->all(), [
            'product_name' => ['required', 'string', 'max:255'],
            'price'        => ['required', 'string', 'max:50'],
            'url'          => ['required', 'url', 'max:2048'],
            'description'  => ['nullable', 'string', 'max:2000'],
            'category'     => ['nullable', 'string', 'in:vitamin,makanan,peralatan_bayi,kesehatan,lainnya'],
            'photo'        => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:500'],
        ], $messages);

        if ($v->fails()) return response()->json(['status' => 'error', 'errors' => $v->errors()], 422);

        // Upload ke Supabase Storage
        $uploadResult = $this->supabase->uploadFile($request->file('photo'), 'shop');

        if (!$uploadResult) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengupload foto ke Supabase',
            ], 500);
        }

        $priceFormatted = $this->formatPrice($request->price);
        $photoUrl = $uploadResult['public_url'];

        $product_id = DB::table('shop')->insertGetId([
            'user_id'      => $uid,
            'product_name' => $request->product_name,
            'category'     => $request->category,
            'description'  => $request->description,
            'price'        => $priceFormatted,
            'url'          => $request->url,
            'photo'        => $photoUrl,
            'average_rating' => 0,
            'rating_count'   => 0,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        ShopLog::record('create', $uid, [
            'product_id'   => $product_id,
            'product_name' => $request->product_name,
            'category'     => $request->category,
            'price'        => $priceFormatted,
            'url'          => $request->url,
            'photo'        => $photoUrl,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil ditambahkan.',
            'data'    => [
                'product_id'   => $product_id,
                'product_name' => $request->product_name,
                'description'  => $request->description,
                'price'        => $priceFormatted,
                'url'          => $request->url,
                'photo'        => $photoUrl,
                'average_rating' => 0,
                'rating_count'   => 0,
            ],
        ], 201);
    }


    public function update(Request $request, int $id): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, 'ibu_hamil');

        $row = DB::table('shop')->where('product_id', $id)->where('user_id', $uid)->first();
        if (! $row) {
            return response()->json(['status'=>'error','message'=>'Produk tidak ditemukan atau bukan milik kamu.'], 404);
        }

        $messages = [
            'photo.image' => 'File harus berupa foto',
            'photo.mimes' => 'File harus berupa foto',
            'photo.max'   => 'Ukuran file melebihi batas upload, pastikan file dibawah 500KB',
            'url.url'     => 'Data URL belum benar, input dengan format lengkap',
        ];

        $v = Validator::make($request->all(), [
            'product_name' => ['sometimes', 'required', 'string', 'max:255'],
            'price'        => ['sometimes', 'required', 'string', 'max:50'],
            'url'          => ['sometimes', 'required', 'url', 'max:2048'],
            'description'  => ['sometimes', 'nullable', 'string', 'max:2000'],
            'category'     => ['sometimes', 'nullable', 'string', 'in:vitamin,makanan,peralatan_bayi,kesehatan,lainnya'],
            'photo'        => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:500'],
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => 'error', 'errors' => $v->errors()], 422);
        }

        $update = [];
        if ($request->has('product_name')) $update['product_name'] = $request->product_name;
        if ($request->has('price')) $update['price'] = $this->formatPrice($request->price);
        if ($request->has('url')) $update['url'] = $request->url;
        if ($request->has('description')) $update['description'] = $request->description;
        if ($request->has('category')) $update['category'] = $request->category;

        if ($request->hasFile('photo')) {
            // Upload ke Supabase Storage
            $uploadResult = $this->supabase->uploadFile($request->file('photo'), 'shop');

            if ($uploadResult) {
                $update['photo'] = $uploadResult['public_url'];

                // Hapus file lama dari Supabase
                if (!empty($row->photo)) {
                    $oldPath = $this->extractPathFromUrl($row->photo);
                    if ($oldPath) {
                        $this->supabase->delete($oldPath);
                    }
                }
            }
        }

        $update['updated_at'] = now();

        DB::table('shop')->where('product_id', $id)->update($update);

        $merged = array_merge((array) $row, $update);
        // Photo sudah berupa public URL dari Supabase

        ShopLog::record('update', $uid, $merged);


        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil diperbarui.',
        ], 200);
    }


    public function delete(Request $request, int $id): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, ['ibu_hamil', 'admin']);

        $row = DB::table('shop')->where('product_id', $id)->first();
        if (! $row) {
            return response()->json(['status'=>'error','message'=>'Produk tidak ditemukan.'], 404);
        }

        if ($role === 'ibu_hamil' && (int) $row->user_id !== $uid) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized: kamu tidak memiliki izin untuk menghapus produk ini.',
            ], 403);
        }

        // Hapus file dari Supabase
        if (!empty($row->photo)) {
            $path = $this->extractPathFromUrl($row->photo);
            if ($path) {
                $this->supabase->delete($path);
            }
        }

        DB::table('shop')->where('product_id', $id)->delete();

        ShopLog::record(
            $role === 'admin' ? 'admin_delete' : 'delete',
            $uid,
            (array) $row,
            $role === 'admin'
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil dihapus.',
        ]);
    }

    /**
     * Simpan atau perbarui review untuk satu produk oleh user yang sedang login.
     */
    public function upsertReview(Request $request, int $productId): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, ['ibu_hamil', 'bidan', 'admin']);

        $product = DB::table('shop')->where('product_id', $productId)->first();
        if (! $product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $now = now();

        $existing = ShopReview::where('product_id', $productId)
            ->where('user_id', $uid)
            ->first();

        if ($existing) {
            $existing->rating  = (int) $request->rating;
            $existing->comment = $request->comment;
            $existing->updated_at = $now;
            $existing->save();
            $review = $existing;
            $message = 'Review berhasil diperbarui.';
        } else {
            $review = ShopReview::create([
                'product_id' => $productId,
                'user_id'    => $uid,
                'rating'     => (int) $request->rating,
                'comment'    => $request->comment,
            ]);
            $message = 'Review berhasil ditambahkan.';
        }

        $this->recalculateProductRating($productId);

        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $review,
        ], 201);
    }

    /**
     * Ambil daftar review untuk satu produk (dengan pagination sederhana).
     */
    public function getReviews(Request $request, int $productId): JsonResponse
    {
        // Hanya memastikan user login & akun aktif, role bebas selama valid
        AuthToken::ensureActiveAndFreshOrFail($request);

        $product = DB::table('shop')->where('product_id', $productId)->first();
        if (! $product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        $perPage = (int) $request->query('data', 10);
        $page    = (int) $request->query('page', 1);

        if ($perPage < 1) $perPage = 1;
        if ($perPage > 100) $perPage = 100;
        if ($page < 1) $page = 1;

        $query = DB::table('shop_reviews')
            ->leftJoin('users', 'shop_reviews.user_id', '=', 'users.user_id')
            ->where('shop_reviews.product_id', $productId)
            ->orderByDesc('shop_reviews.created_at');

        $total  = $query->count();
        $offset = ($page - 1) * $perPage;

        $items = $query
            ->offset($offset)
            ->limit($perPage)
            ->select(
                'shop_reviews.id',
                'shop_reviews.product_id',
                'shop_reviews.user_id',
                'shop_reviews.rating',
                'shop_reviews.comment',
                'shop_reviews.created_at',
                'shop_reviews.updated_at',
                'users.name as user_name',
                DB::raw('NULL as user_profile_image')
            )
            ->get();

        return response()->json([
            'status'       => 'success',
            'current_page' => $page,
            'per_page'     => $perPage,
            'total'        => $total,
            'last_page'    => (int) ceil($total / $perPage),
            'from'         => $total === 0 ? 0 : $offset + 1,
            'to'           => $offset + $items->count(),
            'data'         => $items,
        ]);
    }

    /**
     * Hapus review milik sendiri (atau oleh admin).
     */
    public function deleteReview(Request $request, int $productId, int $reviewId): JsonResponse
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, ['ibu_hamil', 'bidan', 'admin']);

        $review = ShopReview::where('id', $reviewId)
            ->where('product_id', $productId)
            ->first();

        if (! $review) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Review tidak ditemukan.',
            ], 404);
        }

        if ($role !== 'admin' && (int) $review->user_id !== $uid) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kamu tidak memiliki izin untuk menghapus review ini.',
            ], 403);
        }

        $review->delete();

        $this->recalculateProductRating($productId);

        return response()->json([
            'status'  => 'success',
            'message' => 'Review berhasil dihapus.',
        ]);
    }

    /**
     * Hitung ulang rata-rata rating dan jumlah review untuk satu produk.
     */
    protected function recalculateProductRating(int $productId): void
    {
        $aggregate = ShopReview::where('product_id', $productId)
            ->selectRaw('COUNT(*) as total, AVG(rating) as avg_rating')
            ->first();

        $count = (int) ($aggregate->total ?? 0);
        $avg   = $count > 0 ? round((float) $aggregate->avg_rating, 2) : 0;

        DB::table('shop')
            ->where('product_id', $productId)
            ->update([
                'average_rating' => $avg,
                'rating_count'   => $count,
                'updated_at'     => now(),
            ]);
    }

    public function getShopLogs(Request $request)
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, 'admin');

        $data = (int) $request->query('data', 50);
        if ($data > 100) $data = 100;

        $page = (int) $request->query('page', 1);
        $offset = ($page - 1) * $data;

        $total = DB::table('shop_logs')->count();

        $logs = DB::table('shop_logs')
            ->orderByDesc('shop_logs_id')
            ->offset($offset)
            ->limit($data)
            ->get()
            ->map(function ($log) {
                $log->data_snapshot = $log->data_snapshot
                    ? json_decode($log->data_snapshot, true)
                    : null;

                $log->can_delete = !in_array($log->action, ['delete', 'admin_delete']);
                return $log;
            });

        return response()->json([
            'current_page' => $page,
            'per_page'     => $data,
            'total'        => $total,
            'last_page'    => ceil($total / $data),
            'from'         => $offset + 1,
            'to'           => $offset + count($logs),
            'data'         => $logs,
        ]);
    }

    /**
     * Extract path dari Supabase URL
     * Contoh: https://xxx.supabase.co/storage/v1/object/public/images/shop/xxx.jpg
     * Result: shop/xxx.jpg
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
