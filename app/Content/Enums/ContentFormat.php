<?php

namespace App\Content\Enums;

enum ContentFormat: string
{
    case VIDEO = 'video';
    case CAROUSEL = 'carousel';
    case PHOTO = 'photo';
    case STORIES = 'stories';
    case LISTING = 'listing';
    case BLOG = 'blog';
    case BROADCAST = 'broadcast';

    public function label(): string
    {
        return match ($this) {
            self::VIDEO => 'Video',
            self::CAROUSEL => 'Carousel',
            self::PHOTO => 'Photo',
            self::STORIES => 'Stories',
            self::LISTING => 'Listing',
            self::BLOG => 'Blog',
            self::BROADCAST => 'Broadcast',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
