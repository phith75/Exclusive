<?php

namespace App\Enum;

enum ReviewStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 2;

    public function label(): string
    {
        return match ($this) {
            ReviewStatus::ACTIVE => 'Active',
            ReviewStatus::INACTIVE => 'Inactive',
        };
    }

    public static function valuesArray(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
}
