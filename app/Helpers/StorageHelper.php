<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageHelper
{
    private static string $disk = 'gcs';

    /**
     * Upload file to GCS and return the path (relative to disk root).
     */
    public static function upload(UploadedFile $file, string $folder = 'assets', ?string $filename = null): string
    {
        $ext = $file->getClientOriginalExtension();
        $name = $filename ?? Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'-'.time();
        $path = $folder.'/'.$name.'.'.$ext;

        Storage::disk(self::$disk)->put($path, file_get_contents($file->getRealPath()), [
            'ContentType' => $file->getMimeType(),
        ]);

        return $path;
    }

    /**
     * Upload from a raw file path (for downloads or streams).
     */
    public static function uploadFromPath(string $sourcePath, string $folder, string $filename): string
    {
        $path = $folder.'/'.$filename;

        Storage::disk(self::$disk)->put($path, file_get_contents($sourcePath));

        return $path;
    }

    /**
     * Delete file from GCS.
     */
    public static function delete(?string $path): bool
    {
        if (! $path) {
            return false;
        }

        return Storage::disk(self::$disk)->delete($path);
    }

    /**
     * Get URL for a file — uses signed proxy since public access is blocked.
     */
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return url('/storage/gcs/'.$path);
    }

    /**
     * Check if file exists.
     */
    public static function exists(?string $path): bool
    {
        if (! $path) {
            return false;
        }

        return Storage::disk(self::$disk)->exists($path);
    }

    /**
     * Get file size in bytes.
     */
    public static function size(?string $path): ?int
    {
        if (! $path || ! self::exists($path)) {
            return null;
        }

        return Storage::disk(self::$disk)->size($path);
    }
}
