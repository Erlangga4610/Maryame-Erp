<?php

namespace App\Content\Enums;

enum ContentPriority: string
{
    case HIGH = 'high';
    case MEDIUM = 'medium';
    case LOW = 'low';

    public function label(): string
    {
        return match ($this) {
            self::HIGH => 'High Priority',
            self::MEDIUM => 'Medium Priority',
            self::LOW => 'Low Priority',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::HIGH => 'danger',
            self::MEDIUM => 'warning',
            self::LOW => 'success',
        };
    }

    public function value(): int
    {
        return match ($this) {
            self::HIGH => 3,
            self::MEDIUM => 2,
            self::LOW => 1,
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
