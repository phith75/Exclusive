<?php

namespace App\Enum;

enum UserStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 2;

    public function label(): string
    {
        return match ($this) {
            UserStatus::ACTIVE => 'Active',
            UserStatus::INACTIVE => 'Inactive',
        };
    }

    public static function valuesArray(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
