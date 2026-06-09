<?php

namespace App\Content\Enums;

enum ContentType: string
{
    case FEED = 'feed';
    case REELS = 'reels';
    case STORY = 'story';
    case CAROUSEL = 'carousel';

    public function label(): string
    {
        return match ($this) {
            self::FEED => 'Feed Post',
            self::REELS => 'Reels Video',
            self::STORY => 'Story',
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
