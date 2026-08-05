<?php

declare(strict_types=1);

namespace App\Models\Examination;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamMalpractice extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'exam_malpractices';

    protected $fillable = [
        'uuid',
        'exam_subject_id',
        'exam_hall_id',
        'student_id',
        'student_name',
        'student_roll',
        'seat_number',
        'incident_type',
        'description',
        'evidence',
        'invigilator_id',
        'action_taken',
        'remarks',
        'status',
    ];

    // ===================== STATUS =====================
    public const STATUS_REPORTED = 'reported';
    public const STATUS_UNDER_INVESTIGATION = 'under_investigation';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_DISMISSED = 'dismissed';

    // ===================== INCIDENT TYPES =====================
    public const INCIDENT_CHEATING = 'cheating';
    public const INCIDENT_LATE_ENTRY = 'late_entry';
    public const INCIDENT_MOBILE_PHONE = 'mobile_phone';
    public const INCIDENT_IDENTITY_FRAUD = 'identity_fraud';
    public const INCIDENT_BEHAVIOR = 'behavior';
    public const INCIDENT_OTHER = 'other';

    // ===================== RELATIONSHIPS =====================

    public function subject(): BelongsTo
    {
        return $this->belongsTo(ExamSubject::class, 'exam_subject_id');
    }

    public function hall(): BelongsTo
    {
        return $this->belongsTo(ExamHall::class, 'exam_hall_id');
    }

    public function invigilator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'invigilator_id');
    }

    // ===================== METHODS =====================

    public static function statuses(): array
    {
        return [
            self::STATUS_REPORTED => 'Reported',
            self::STATUS_UNDER_INVESTIGATION => 'Under Investigation',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_DISMISSED => 'Dismissed',
        ];
    }

    public static function incidentTypes(): array
    {
        return [
            self::INCIDENT_CHEATING => 'Cheating',
            self::INCIDENT_LATE_ENTRY => 'Late Entry',
            self::INCIDENT_MOBILE_PHONE => 'Mobile Phone',
            self::INCIDENT_IDENTITY_FRAUD => 'Identity Fraud',
            self::INCIDENT_BEHAVIOR => 'Misbehavior',
            self::INCIDENT_OTHER => 'Other',
        ];
    }
}
