<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Support\AuthToken;
use App\Helpers\ShopLog;

class ShopController extends Controller
{
    protected function formatPrice(string $price): string
    {
        $clean = preg_replace('/\D/', '', $price);
        if ($clean === '') return '0';
        return number_format((int)$clean, 0, ',', '.');
    }

    public function getAll(Request $request)
    {
        [$uid, $role] = AuthToken::assertRoleFresh($request, 'ibu_hamil');

        $data = (int) $request->query('data', 30);
        $page = (int) $request->query('page', 1);

        if ($data < 1) $data = 1;
        if ($data > 100) $data = 100;
        if ($page < 1) $page = 1;

        $query = DB::table('shop')->orderByDesc('product_id');

        $total = $query->count();
        $result = $query
            ->offset(($page - 1) * $data)
            ->limit($data)
            ->get()
            ->map(function ($item) {
                $item->photo = url('storage/' . ltrim($item->photo, '/'));
                return $item;
            });

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

        if ($data < 1) $data = 1;
        if ($data > 100) $data = 100;
        if ($page < 1) $page = 1;

        $query = DB::table('shop')
            ->where('user_id', $uid)
            ->orderByDesc('product_id');

        $total = $query->count();
        $result = $query
            ->offset(($page - 1) * $data)
            ->limit($data)
            ->get()
            ->map(function ($item) {
                $item->photo = url('storage/' . ltrim($item->photo, '/'));
                return $item;
            });

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
            'photo'        => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:500'],
        ], $messages);

        if ($v->fails()) return response()->json(['status' => 'error', 'errors' => $v->errors()], 422);

        $path = $request->file('photo')->store('shop', 'public');
        $priceFormatted = $this->formatPrice($request->price);

        $product_id = DB::table('shop')->insertGetId([
            'user_id'      => $uid,
            'product_name' => $request->product_name,
            'price'        => $priceFormatted,
            'url'          => $request->url,
            'photo'        => $path,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $photoUrl = $path ? asset('storage/' . $path) : null;

        ShopLog::record('create', $uid, [
            'product_id'   => $product_id,
            'product_name' => $request->product_name,
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
                'price'        => $priceFormatted,
                'url'          => $request->url,
                'photo'        => $photoUrl,
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
            'photo'        => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:500'],
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => 'error', 'errors' => $v->errors()], 422);
        }

        $update = [];
        if ($request->has('product_name')) $update['product_name'] = $request->product_name;
        if ($request->has('price')) $update['price'] = $this->formatPrice($request->price);
        if ($request->has('url')) $update['url'] = $request->url;

        if ($request->hasFile('photo')) {
            $newPath = $request->file('photo')->store('shop', 'public');
            $update['photo'] = $newPath;

            if (!empty($row->photo) && Storage::disk('public')->exists($row->photo)) {
                Storage::disk('public')->delete($row->photo);
            }
        }

        $update['updated_at'] = now();

        DB::table('shop')->where('product_id', $id)->update($update);

        $merged = array_merge((array) $row, $update);
        $merged['photo'] = isset($update['photo'])
            ? asset('storage/' . $update['photo'])
            : asset('storage/' . $row->photo);

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

        if (!empty($row->photo) && Storage::disk('public')->exists($row->photo)) {
            Storage::disk('public')->delete($row->photo);
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


}
