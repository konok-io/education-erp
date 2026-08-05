<?php

declare(strict_types=1);

namespace App\Models\Examination;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSubject extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'exam_subjects';

    protected $fillable = [
        'uuid',
        'exam_id',
        'subject_id',
        'subject_code',
        'subject_name',
        'exam_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'full_marks',
        'pass_marks',
        'practical_marks',
        'theory_marks',
        'exam_mode',
        'syllabus',
        'status',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'full_marks' => 'decimal:2',
        'pass_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'theory_marks' => 'decimal:2',
        'duration_minutes' => 'integer',
    ];

    // ===================== STATUS =====================
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== MODES =====================
    public const MODE_WRITTEN = 'written';
    public const MODE_PRACTICAL = 'practical';
    public const MODE_VIVA = 'viva';
    public const MODE_PROJECT = 'project';
    public const MODE_BOTH = 'both';

    // ===================== RELATIONSHIPS =====================

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function marks(): HasMany
    {
        return $this->hasMany(ExamMark::class, 'exam_subject_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(ExamAttendance::class, 'exam_subject_id');
    }

    public function seatPlans(): HasMany
    {
        return $this->hasMany(ExamSeatPlan::class, 'exam_subject_id');
    }

    // ===================== SCOPES =====================

    public function scopeToday($query)
    {
        return $query->whereDate('exam_date', now()->toDateString());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('exam_date', '>', now());
    }

    // ===================== METHODS =====================

    public static function examModes(): array
    {
        return [
            self::MODE_WRITTEN => 'Written',
            self::MODE_PRACTICAL => 'Practical',
            self::MODE_VIVA => 'Viva',
            self::MODE_PROJECT => 'Project',
            self::MODE_BOTH => 'Written + Practical',
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

    public function getDurationAttribute(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        return sprintf('%d:%02d', $hours, $minutes);
    }
}
