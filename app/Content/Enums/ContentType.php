<?php

namespace App\Content\Enums;

enum ContentType: string
{
    case EDUKASI = 'edukasi';
    case JUALAN = 'jualan';
    case TESTIMONI = 'testimoni';
    case TRENDING = 'trending';
    case UGC = 'ugc';
    case CAMPAIGN = 'campaign';

    public function label(): string
    {
        return match ($this) {
            self::EDUKASI => 'Edukasi',
            self::JUALAN => 'Jualan',
            self::TESTIMONI => 'Testimoni',
            self::TRENDING => 'Trending',
            self::UGC => 'UGC',
            self::CAMPAIGN => 'Campaign',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
