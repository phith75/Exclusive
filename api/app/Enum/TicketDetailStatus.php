<?php

// write enum status lendTicketStatus on this file

namespace App\Enum;

enum TicketDetailStatus: int
{
    case BORROWED = 1;
    case RETURNED = 2;
    case OVERDUE = 3;
    case LOST = 4;

    public function label(): string
    {
        return match ($this) {
            TicketDetailStatus::BORROWED => 'borrowed',
            TicketDetailStatus::RETURNED => 'returned',
            TicketDetailStatus::OVERDUE => 'overdue',
            TicketDetailStatus::LOST => 'lost',
        };
    }

    public static function valuesArray(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
}
