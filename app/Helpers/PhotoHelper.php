<?php

namespace App\Helpers;

class PhotoHelper
{
    /**
     * Check if a photo value is already a full URL
     *
     * @param string|null $photo
     * @return bool
     */
    public static function isFullUrl(?string $photo): bool
    {
        if (empty($photo)) {
            return false;
        }

        return str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://');
    }

    /**
     * Transform photo to URL if it's a relative path
     *
     * @param string|null $photo
     * @param string $disk
     * @return string|null
     */
    public static function transformPhotoUrl(?string $photo, string $disk = 'supabase'): ?string
    {
        if (empty($photo)) {
            return null;
        }

        // If already a full URL, return as-is
        if (self::isFullUrl($photo)) {
            return $photo;
        }

        // Otherwise, treat as relative path and generate URL
        return \Illuminate\Support\Facades\Storage::disk($disk)->url($photo);
    }

    /**
     * Extract relative path from Supabase URL
     * Used for file deletion operations
     *
     * @param string $url
     * @return string|null
     */
    public static function extractPathFromUrl(string $url): ?string
    {
        $pattern = '/\/storage\/v1\/object\/public\/images\/(.+)$/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
