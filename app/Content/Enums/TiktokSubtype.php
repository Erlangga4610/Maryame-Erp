<?php

namespace App\Content\Enums;

enum TiktokSubtype: string
{
    case KK_INTERAKTIF = 'kk_interaktif';
    case KK_SOFT_SELLING = 'kk_soft_selling';
    case NON_KK = 'non_kk';

    public function label(): string
    {
        return match ($this) {
            self::KK_INTERAKTIF => 'KK Interaktif',
            self::KK_SOFT_SELLING => 'KK Soft Selling',
            self::NON_KK => 'Non-KK',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
