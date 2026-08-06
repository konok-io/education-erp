<?php

declare(strict_types=1);

namespace App\Enums\DevSecOps;

enum Severity: string
{
    case NONE = 'none';
    case INFO = 'info';
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';

    public function label(): string
    {
        return match($this) {
            self::NONE => 'None',
            self::INFO => 'Info',
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::CRITICAL => 'Critical',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NONE => 'gray',
            self::INFO => 'blue',
            self::LOW => 'cyan',
            self::MEDIUM => 'yellow',
            self::HIGH => 'orange',
            self::CRITICAL => 'red',
        };
    }

    public function score(): int
    {
        return match($this) {
            self::NONE => 0,
            self::INFO => 1,
            self::LOW => 2,
            self::MEDIUM => 5,
            self::HIGH => 7,
            self::CRITICAL => 10,
        };
    }

    public function order(): int
    {
        return match($this) {
            self::CRITICAL => 1,
            self::HIGH => 2,
            self::MEDIUM => 3,
            self::LOW => 4,
            self::INFO => 5,
            self::NONE => 6,
        };
    }
}
