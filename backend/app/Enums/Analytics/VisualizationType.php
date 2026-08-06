<?php

declare(strict_types=1);

namespace App\Enums\Analytics;

enum VisualizationType: string
{
    case TABLE = 'table';
    case CHART = 'chart';
    case GRAPH = 'graph';
    case MAP = 'map';
    case DASHBOARD = 'dashboard';
    case PIVOT = 'pivot';

    public function label(): string
    {
        return match($this) {
            self::TABLE => 'Table',
            self::CHART => 'Chart',
            self::GRAPH => 'Graph',
            self::MAP => 'Map',
            self::DASHBOARD => 'Dashboard',
            self::PIVOT => 'Pivot Table',
        };
    }
}
