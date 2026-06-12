<?php

namespace App\Content\Enums;

enum CalendarEntryStatus: string
{
    case DRAFT = 'draft';
    case IN_REVIEW = 'in_review';
    case APPROVED = 'approved';
    case DISTRIBUTED = 'distributed';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::IN_REVIEW => 'In Review',
            self::APPROVED => 'Approved',
            self::DISTRIBUTED => 'Distributed',
            self::ARCHIVED => 'Archived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'zinc',
            self::IN_REVIEW => 'amber',
            self::APPROVED => 'green',
            self::DISTRIBUTED => 'purple',
            self::ARCHIVED => 'zinc',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->all();
    }
}
