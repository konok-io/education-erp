<?php

declare(strict_types=1);

namespace App\Enums\Observability;

enum ChaosExperimentType: string
{
    case SERVER_FAILURE = 'server_failure';
    case DATABASE_FAILURE = 'database_failure';
    case NETWORK_FAILURE = 'network_failure';
    case CACHE_FAILURE = 'cache_failure';
    case QUEUE_FAILURE = 'queue_failure';
    case REGION_FAILURE = 'region_failure';
    case CUSTOM = 'custom';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
