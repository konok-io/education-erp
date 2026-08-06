<?php

declare(strict_types=1);

namespace App\Enums\Library;

enum BorrowingStatus: string
{
    case BORROWED = 'borrowed';
    case RETURNED = 'returned';
    case OVERDUE = 'overdue';
    case LOST = 'lost';
    case DAMAGED = 'damaged';

    public function label(): string
    {
        return match($this) {
            self::BORROWED => 'Borrowed',
            self::RETURNED => 'Returned',
            self::OVERDUE => 'Overdue',
            self::LOST => 'Lost',
            self::DAMAGED => 'Damaged',
        };
    }
}
