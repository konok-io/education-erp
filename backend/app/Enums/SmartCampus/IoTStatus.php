<?php

declare(strict_types=1);

namespace App\Enums\SmartCampus;

enum IoTStatus: string
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';
    case MAINTENANCE = 'maintenance';
    case ERROR = 'error';

    public function label(): string
    {
        return match($this) {
            self::ONLINE => 'Online',
            self::OFFLINE => 'Offline',
            self::MAINTENANCE => 'Maintenance',
            self::ERROR => 'Error',
        };
    }
}
