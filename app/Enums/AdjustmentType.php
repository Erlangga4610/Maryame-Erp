<?php

namespace App\Enums;

enum AdjustmentType: string
{
    case MINOR = 'minor';
    case MAJOR = 'major';
    case REACTIVE = 'reactive';

    public function label(): string
    {
        return match ($this) {
            self::MINOR => 'Minor Adjustment (Direct Execute)',
            self::MAJOR => 'Major Adjustment (Need MC/BM Approval)',
            self::REACTIVE => 'Reactive (Fast-track ≤6 hours)',
        };
    }

    public function needsApproval(): bool
    {
        return match ($this) {
            self::MINOR => false,
            self::MAJOR => true,
            self::REACTIVE => false,
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
