<?php

namespace App\Content\Enums;

enum ContentFormat: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';
    case CAROUSEL = 'carousel';

    public function label(): string
    {
        return match ($this) {
            self::IMAGE => 'Image',
            self::VIDEO => 'Video',
            self::CAROUSEL => 'Carousel',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
