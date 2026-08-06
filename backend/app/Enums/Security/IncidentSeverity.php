<?php

declare(strict_types=1);

namespace App\Enums\Security;

enum IncidentSeverity: string
{
    case P1_CRITICAL = 'p1_critical';
    case P2_HIGH = 'p2_high';
    case P3_MEDIUM = 'p3_medium';
    case P4_LOW = 'p4_low';

    public function label(): string
    {
        return match($this) {
            self::P1_CRITICAL => 'P1 - Critical',
            self::P2_HIGH => 'P2 - High',
            self::P3_MEDIUM => 'P3 - Medium',
            self::P4_LOW => 'P4 - Low',
        };
    }

    public function slaMinutes(): int
    {
        return match($this) {
            self::P1_CRITICAL => 15,
            self::P2_HIGH => 60,
            self::P3_MEDIUM => 240,
            self::P4_LOW => 1440,
        };
    }
}
