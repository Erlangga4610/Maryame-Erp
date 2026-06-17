<?php

namespace App\Content\Enums;

enum ContentPriority: string
{
    case RUTIN = 'rutin';
    case CAMPAIGN = 'campaign';
    case SPONTAN = 'spontan';

    public function label(): string
    {
        return match ($this) {
            self::RUTIN => 'Rutin',
            self::CAMPAIGN => 'Campaign',
            self::SPONTAN => 'Spontan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::RUTIN => 'success',
            self::CAMPAIGN => 'danger',
            self::SPONTAN => 'warning',
        };
    }

    public function value(): int
    {
        return match ($this) {
            self::RUTIN => 1,
            self::CAMPAIGN => 3,
            self::SPONTAN => 2,
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
