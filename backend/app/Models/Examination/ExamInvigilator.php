<?php

declare(strict_types=1);

namespace App\Models\Examination;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamInvigilator extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'exam_invigilators';

    protected $fillable = [
        'uuid',
        'exam_id',
        'user_id',
        'exam_hall_id',
        'exam_subject_id',
        'duty_date',
        'reporting_time',
        'role',
        'status',
        'remarks',
    ];

    protected $casts = [
        'duty_date' => 'date',
        'reporting_time' => 'datetime:H:i',
    ];

    // ===================== STATUS =====================
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_ABSENT = 'absent';

    // ===================== ROLES =====================
    public const ROLE_INVIGILATOR = 'invigilator';
    public const ROLE_SENIOR_INVIGILATOR = 'senior_invigilator';
    public const ROLE_ROOM_INCHARGE = 'room_incharge';

    // ===================== RELATIONSHIPS =====================

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function hall(): BelongsTo
    {
        return $this->belongsTo(ExamHall::class, 'exam_hall_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(ExamSubject::class, 'exam_subject_id');
    }

    // ===================== SCOPES =====================

    public function scopeToday($query)
    {
        return $query->whereDate('duty_date', now()->toDateString());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('duty_date', '>', now());
    }

    // ===================== METHODS =====================

    public static function statuses(): array
    {
        return [
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_ABSENT => 'Absent',
        ];
    }

    public static function roles(): array
    {
        return [
            self::ROLE_INVIGILATOR => 'Invigilator',
            self::ROLE_SENIOR_INVIGILATOR => 'Senior Invigilator',
            self::ROLE_ROOM_INCHARGE => 'Room Incharge',
        ];
    }
}
