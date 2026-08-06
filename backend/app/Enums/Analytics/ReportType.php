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
