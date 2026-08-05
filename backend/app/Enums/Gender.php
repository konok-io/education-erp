<?php

declare(strict_types=1);

namespace App\Enums;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
            self::OTHER => 'Other',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::MALE => 'male',
            self::FEMALE => 'female',
            self::OTHER => 'other',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
