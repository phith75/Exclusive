<?php

namespace App\Enum;

enum LendTicketStatus: int
{
    case APPROVED = 1;
    case RETURNED = 2;

    public function label(): string
    {
        return match ($this) {
            LendTicketStatus::APPROVED => 'Approved',
            LendTicketStatus::RETURNED => 'Returned',
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
