<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAttendance extends Model
{
    use HasUuid;

    protected $table = 'employee_attendances';

    protected $fillable = [
        'uuid',
        'employee_id',
        'attendance_date',
        'check_in',
        'check_out',
        'status',
        'overtime_hours',
        'remarks',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
        'overtime_hours' => 'decimal:2',
    ];

    // ===================== STATUS =====================
    public const STATUS_PRESENT = 'present';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_LATE = 'late';
    public const STATUS_HALF_DAY = 'half_day';
    public const STATUS_HOLIDAY = 'holiday';
    public const STATUS_LEAVE = 'leave';

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // ===================== METHODS =====================

    public static function statuses(): array
    {
        return [
            self::STATUS_PRESENT => 'Present',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_LATE => 'Late',
            self::STATUS_HALF_DAY => 'Half Day',
            self::STATUS_HOLIDAY => 'Holiday',
            self::STATUS_LEAVE => 'Leave',
        ];
    }

    public function calculateOvertime(?string $shiftEnd = null): float
    {
        if (!$this->check_out || !$shiftEnd) {
            return 0;
        }

        $checkout = strtotime($this->check_out);
        $shiftEndTime = strtotime($shiftEnd);

        if ($checkout > $shiftEndTime) {
            $overtimeMinutes = ($checkout - $shiftEndTime) / 60;
            return round($overtimeMinutes / 60, 2);
        }

        return 0;
    }
}
