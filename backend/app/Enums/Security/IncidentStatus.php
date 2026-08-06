<?php

declare(strict_types=1);

namespace App\Enums\Security;

enum IncidentStatus: string
{
    case DETECTED = 'detected';
    case INVESTIGATING = 'investigating';
    case CONTAINED = 'contained';
    case ERADICATED = 'eradicated';
    case RECOVERED = 'recovered';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match($this) {
            self::DETECTED => 'Detected',
            self::INVESTIGATING => 'Investigating',
            self::CONTAINED => 'Contained',
            self::ERADICATED => 'Eradicated',
            self::RECOVERED => 'Recovered',
            self::CLOSED => 'Closed',
        };
    }
}
