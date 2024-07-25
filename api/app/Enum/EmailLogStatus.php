<?php

namespace App\Enum;

enum EmailLogStatus: int
{
    case SENT = 1;
    case FAILED = 2;

    public function label(): string
    {
        return match ($this) {
            EmailLogStatus::SENT => 'Sent',
            EmailLogStatus::FAILED => 'Failed',
        };
    }

    public static function valuesArray(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
}
