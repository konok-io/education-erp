<?php

declare(strict_types=1);

namespace App\Enums\Observability;

enum MetricType: string
{
    case COUNTER = 'counter';
    case GAUGE = 'gauge';
    case HISTOGRAM = 'histogram';
    case SUMMARY = 'summary';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
