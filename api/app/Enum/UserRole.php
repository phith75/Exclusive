<?php

namespace App\Enum;

enum UserRole: int
{
    case ADMIN = 1;
    case USER = 2;

    public function label(): string
    {
        return match ($this) {
            UserRole::ADMIN => 'Admin',
            UserRole::USER => 'User',
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
