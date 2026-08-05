<?php

declare(strict_types=1);

namespace App\Models\Examination;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'exams';

    protected $fillable = [
        'uuid',
        'exam_name',
        'exam_code',
        'exam_type',
        'exam_session_id',
        'class_id',
        'section_id',
        'start_date',
        'end_date',
        'result_publish_date',
        'description',
        'instructions',
        'status',
        'created_by',
        'is_published',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'result_publish_date' => 'date',
        'is_published' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== TYPES =====================
    public const TYPE_CLASS_TEST = 'class_test';
    public const TYPE_MONTHLY = 'monthly';
    public const TYPE_WEEKLY = 'weekly';
    public const TYPE_TUTORIAL = 'tutorial';
    public const TYPE_MID_TERM = 'mid_term';
    public const TYPE_PRE_TEST = 'pre_test';
    public const TYPE_TEST = 'test';
    public const TYPE_FINAL = 'final';
    public const TYPE_BOARD_PREP = 'board_prep';
    public const TYPE_SEMESTER = 'semester_final';
    public const TYPE_IMPROVEMENT = 'improvement';
    public const TYPE_RETAKE = 'retake';

    // ===================== RELATIONSHIPS =====================

    public function session(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(ExamSubject::class, 'exam_id');
    }

    public function invigilators(): HasMany
    {
        return $this->hasMany(ExamInvigilator::class, 'exam_id');
    }

    public function admitCards(): HasMany
    {
        return $this->hasMany(ExamAdmitCard::class, 'exam_id');
    }

    public function seatPlans(): HasMany
    {
        return $this->hasMany(ExamSeatPlan::class, 'exam_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // ===================== SCOPES =====================

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', self::STATUS_ONGOING);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now())->where('status', self::STATUS_SCHEDULED);
    }

    // ===================== METHODS =====================

    public static function examTypes(): array
    {
        return [
            self::TYPE_CLASS_TEST => 'Class Test',
            self::TYPE_MONTHLY => 'Monthly',
            self::TYPE_WEEKLY => 'Weekly',
            self::TYPE_TUTORIAL => 'Tutorial',
            self::TYPE_MID_TERM => 'Mid Term',
            self::TYPE_PRE_TEST => 'Pre-Test',
            self::TYPE_TEST => 'Test',
            self::TYPE_FINAL => 'Final',
            self::TYPE_BOARD_PREP => 'Board Preparation',
            self::TYPE_SEMESTER => 'Semester Final',
            self::TYPE_IMPROVEMENT => 'Improvement',
            self::TYPE_RETAKE => 'Retake',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_ONGOING => 'Ongoing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function publish(): void
    {
        $this->update(['is_published' => true]);
    }

    public function updateStatus(): void
    {
        if ($this->status === self::STATUS_CANCELLED) {
            return;
        }

        if ($this->end_date < now()) {
            $this->update(['status' => self::STATUS_COMPLETED]);
        } elseif ($this->start_date <= now()) {
            $this->update(['status' => self::STATUS_ONGOING]);
        }
    }
}
