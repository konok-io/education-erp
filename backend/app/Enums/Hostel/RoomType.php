<?php

declare(strict_types=1);

namespace App\Enums\Hostel;

enum RoomType: string
{
    case SINGLE = 'single';
    case DOUBLE = 'double';
    case TRIPLE = 'triple';
    case DORMITORY = 'dormitory';
    case SUITE = 'suite';

    public function label(): string
    {
        return match($this) {
            self::SINGLE => 'Single Room',
            self::DOUBLE => 'Double Room',
            self::TRIPLE => 'Triple Room',
            self::DORMITORY => 'Dormitory',
            self::SUITE => 'Suite',
        };
    }

    public function capacity(): int
    {
        return match($this) {
            self::SINGLE => 1,
            self::DOUBLE => 2,
            self::TRIPLE => 3,
            self::DORMITORY => 8,
            self::SUITE => 4,
        };
    }
}
