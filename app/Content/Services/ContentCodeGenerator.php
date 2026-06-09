<?php

namespace App\Content\Services;

use App\Content\Models\Content;
use App\Models\Platform;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContentCodeGenerator
{
    public static function generate(Platform $platform): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');

        return DB::transaction(function () use ($platform, $year, $month) {
            $prefix = "{$platform->code}-{$year}-{$month}-";

            $lastContent = Content::withTrashed()
                ->where('content_code', 'like', $prefix.'%')
                ->orderBy('content_code', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastContent) {
                $lastNumber = (int) substr($lastContent->content_code, -3);
                $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '001';
            }

            $code = $prefix.$nextNumber;

            if (Content::withTrashed()->where('content_code', $code)->exists()) {
                Log::warning('Content code collision detected', ['code' => $code]);

                return self::generate($platform);
            }

            return $code;
        });
    }

    public static function validateFormat(string $code): bool
    {
        return (bool) preg_match('/^[A-Z]{3,5}-\d{4}-\d{2}-\d{3}$/', $code);
    }

    public static function extractPlatformCode(string $code): string
    {
        return explode('-', $code)[0];
    }
}
