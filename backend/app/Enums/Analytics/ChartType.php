<?php

declare(strict_types=1);

namespace App\Enums\Analytics;

enum ChartType: string
{
    case BAR = 'bar';
    case LINE = 'line';
    case PIE = 'pie';
    case AREA = 'area';
    case SCATTER = 'scatter';
    case DONUT = 'donut';
    case GAUGE = 'gauge';

    public function label(): string
    {
        return match($this) {
            self::BAR => 'Bar Chart',
            self::LINE => 'Line Chart',
            self::PIE => 'Pie Chart',
            self::AREA => 'Area Chart',
            self::SCATTER => 'Scatter Plot',
            self::DONUT => 'Donut Chart',
            self::GAUGE => 'Gauge',
        };
    }
}
