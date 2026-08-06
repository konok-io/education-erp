<?php

declare(strict_types=1);

namespace App\Enums\Analytics;

enum ReportType: string
{
    case ACADEMIC = 'academic';
    case FINANCIAL = 'financial';
    case ADMINISTRATIVE = 'administrative';
    case HR = 'hr';
    case STUDENT = 'student';
    case ATTENDANCE = 'attendance';
    case EXAMINATION = 'examination';
    case INVENTORY = 'inventory';

    public function label(): string
    {
        return match($this) {
            self::ACADEMIC => 'Academic',
            self::FINANCIAL => 'Financial',
            self::ADMINISTRATIVE => 'Administrative',
            self::HR => 'HR',
            self::STUDENT => 'Student',
            self::ATTENDANCE => 'Attendance',
            self::EXAMINATION => 'Examination',
            self::INVENTORY => 'Inventory',
        };
    }
}

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
