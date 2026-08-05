<?php

declare(strict_types=1);

namespace App\Models\Examination;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamActivity extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'exam_activities';

    protected $fillable = [
        'uuid',
        'user_id',
        'activity_type',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // ===================== ACTIVITY TYPES =====================
    public const ACTIVITY_EXAM_CREATED = 'exam_created';
    public const ACTIVITY_EXAM_UPDATED = 'exam_updated';
    public const ACTIVITY_EXAM_PUBLISHED = 'exam_published';
    public const ACTIVITY_ROUTINE_PUBLISHED = 'routine_published';
    public const ACTIVITY_SEAT_PLAN_GENERATED = 'seat_plan_generated';
    public const ACTIVITY_ADMIT_CARD_GENERATED = 'admit_card_generated';
    public const ACTIVITY_INVIGILATOR_ASSIGNED = 'invigilator_assigned';
    public const ACTIVITY_ATTENDANCE_SUBMITTED = 'attendance_submitted';
    public const ACTIVITY_MARKS_SUBMITTED = 'marks_submitted';
    public const ACTIVITY_MARKS_APPROVED = 'marks_approved';
    public const ACTIVITY_RESULT_RELEASED = 'result_released';

    // ===================== RELATIONSHIPS =====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // ===================== METHODS =====================

    public static function activityTypes(): array
    {
        return [
            self::ACTIVITY_EXAM_CREATED => 'Exam Created',
            self::ACTIVITY_EXAM_UPDATED => 'Exam Updated',
            self::ACTIVITY_EXAM_PUBLISHED => 'Exam Published',
            self::ACTIVITY_ROUTINE_PUBLISHED => 'Routine Published',
            self::ACTIVITY_SEAT_PLAN_GENERATED => 'Seat Plan Generated',
            self::ACTIVITY_ADMIT_CARD_GENERATED => 'Admit Card Generated',
            self::ACTIVITY_INVIGILATOR_ASSIGNED => 'Invigilator Assigned',
            self::ACTIVITY_ATTENDANCE_SUBMITTED => 'Attendance Submitted',
            self::ACTIVITY_MARKS_SUBMITTED => 'Marks Submitted',
            self::ACTIVITY_MARKS_APPROVED => 'Marks Approved',
            self::ACTIVITY_RESULT_RELEASED => 'Result Released',
        ];
    }

    public static function log(
        string $activityType,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        return self::create([
            'user_id' => auth()->id(),
            'activity_type' => $activityType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
