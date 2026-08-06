<?php

declare(strict_types=1);

namespace App\Enums\Observability;

enum ServiceType: string
{
    case API = 'api';
    case FRONTEND = 'frontend';
    case DATABASE = 'database';
    case QUEUE = 'queue';
    case CACHE = 'cache';
    case STORAGE = 'storage';
    case NETWORK = 'network';
    case ELECTRON = 'electron';
    case ANDROID = 'android';
    case IOS = 'ios';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
