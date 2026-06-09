<?php

namespace App\Content\Enums;

enum ContentStatus: string
{
    case DRAFT = 'draft';
    case IN_PRODUCTION = 'in_production';
    case READY_REVIEW = 'ready_review';
    case APPROVED = 'approved';
    case SCHEDULED = 'scheduled';
    case PUBLISHED = 'published';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::IN_PRODUCTION => 'In Production',
            self::READY_REVIEW => 'Ready for Review',
            self::APPROVED => 'Approved',
            self::SCHEDULED => 'Scheduled',
            self::PUBLISHED => 'Published',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::IN_PRODUCTION => 'blue',
            self::READY_REVIEW => 'amber',
            self::APPROVED => 'green',
            self::SCHEDULED => 'purple',
            self::PUBLISHED => 'emerald',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
