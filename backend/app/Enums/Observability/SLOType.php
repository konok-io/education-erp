<?php

declare(strict_types=1);

namespace App\Enums\Observability;

enum SLOType: string
{
    case AVAILABILITY = 'availability';
    case LATENCY = 'latency';
    case RELIABILITY = 'reliability';
    case THROUGHPUT = 'throughput';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
