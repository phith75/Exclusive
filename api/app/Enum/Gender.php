<?php

namespace App\Enum;

enum Gender: int
{
    case MALE = 1;
    case FEMALE = 2;
    case OTHER = 3;

    public function label(): string
    {
        return match ($this) {
            Gender::MALE => 'Male',
            Gender::FEMALE => 'Female',
            Gender::OTHER => 'Other',
        };
    }

    public static function valuesArray(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
