<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostelAttendance extends Model
{
    use HasUuid;

    protected $table = 'hostel_attendances';

    protected $fillable = [
        'uuid',
        'hostel_id',
        'attendance_type',
        'attendance_date',
        'student_name',
        'student_id_number',
        'bed_id',
        'check_in_time',
        'check_out_time',
        'status',
        'remarks',
        'recorded_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
    ];

    // ===================== STATUS =====================
    public const STATUS_PRESENT = 'present';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_LATE = 'late';
    public const STATUS_LEAVE = 'leave';
    public const STATUS_EARLY_LEAVE = 'early_leave';

    // ===================== TYPES =====================
    public const TYPE_MORNING = 'morning';
    public const TYPE_NIGHT = 'night';
    public const TYPE_MIDDAY = 'midday';

    // ===================== RELATIONSHIPS =====================

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class, 'bed_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by');
    }

    // ===================== SCOPES =====================

    public function scopeToday($query)
    {
        return $query->whereDate('attendance_date', now()->toDateString());
    }

    public function scopePresent($query)
    {
        return $query->where('status', self::STATUS_PRESENT);
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', self::STATUS_ABSENT);
    }

    // ===================== METHODS =====================

    public static function attendanceTypes(): array
    {
        return [
            self::TYPE_MORNING => 'Morning',
            self::TYPE_NIGHT => 'Night',
            self::TYPE_MIDDAY => 'Midday',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PRESENT => 'Present',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_LATE => 'Late',
            self::STATUS_LEAVE => 'On Leave',
            self::STATUS_EARLY_LEAVE => 'Early Leave',
        ];
    }
}
