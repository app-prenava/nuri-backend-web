<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class ShopLog
{
    public static function record($action, int $userId, array $product, bool $isAdminDelete = false)
    {
        DB::table('shop_logs')->insert([
            'user_id'         => $userId,
            'product_id'      => $product['product_id'] ?? null,
            'product_name'    => $product['product_name'] ?? null,
            'price'           => $product['price'] ?? null,
            'url'             => $product['url'] ?? null,
            'action'          => $action,
            'data_snapshot'   => json_encode($product, JSON_UNESCAPED_UNICODE),
            'admin_deleted_at'=> $isAdminDelete ? now() : null,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }
}
