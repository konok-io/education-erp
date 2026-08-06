<?php

declare(strict_types=1);

namespace App\Enums\Exam;

enum ExamAttendanceStatus: string
{
    case PRESENT = 'present';
    case ABSENT = 'absent';
    case LATE = 'late';
    case DISQUALIFIED = 'disqualified';
    case EXEMPTED = 'exempted';

    public function label(): string
    {
        return match($this) {
            self::PRESENT => 'Present',
            self::ABSENT => 'Absent',
            self::LATE => 'Late',
            self::DISQUALIFIED => 'Disqualified',
            self::EXEMPTED => 'Exempted',
        };
    }

    public function isPresent(): bool
    {
        return in_array($this, [self::PRESENT, self::LATE]);
    }
}
