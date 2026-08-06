<?php

declare(strict_types=1);

namespace App\Enums\Observability;

enum HealthCheckType: string
{
    case API = 'api';
    case DATABASE = 'database';
    case QUEUE = 'queue';
    case CACHE = 'cache';
    case STORAGE = 'storage';
    case SMTP = 'smtp';
    case SMS = 'sms';
    case PAYMENT = 'payment';
    case CUSTOM = 'custom';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
