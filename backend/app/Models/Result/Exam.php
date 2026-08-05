<?php

declare(strict_types=1);

namespace App\Models\Result;

use App\Models\Academic\AcademicSession;
use App\Models\Academic\AcademicLevel;
use App\Models\Academic\Program;
use App\Models\Academic\Semester;
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
        'exam_code',
        'exam_name',
        'exam_name_bn',
        'academic_session_id',
        'academic_level_id',
        'program_id',
        'semester_id',
        'exam_type',
        'start_date',
        'end_date',
        'description',
        'instructions',
        'total_marks',
        'pass_marks',
        'status',
        'is_published',
        'published_at',
        'published_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_marks' => 'decimal:2',
        'pass_marks' => 'decimal:2',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    // ===================== EXAM TYPES =====================
    public const TYPE_CLASS_TEST = 'class_test';
    public const TYPE_MONTHLY = 'monthly';
    public const TYPE_MID_TERM = 'mid_term';
    public const TYPE_PRE_TEST = 'pre_test';
    public const TYPE_TEST_EXAM = 'test_exam';
    public const TYPE_FINAL = 'final';
    public const TYPE_SEMESTER_FINAL = 'semester_final';
    public const TYPE_IMPROVEMENT = 'improvement';
    public const TYPE_SUPPLEMENTARY = 'supplementary';
    public const TYPE_BOARD = 'board';

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== RELATIONSHIPS =====================

    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class, 'academic_level_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ExamSchedule::class, 'exam_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class, 'exam_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', '!=', self::STATUS_CANCELLED);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // ===================== METHODS =====================

    public static function generateExamCode(): string
    {
        $prefix = 'EXM';
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now()->toDateString())->count() + 1;
        return sprintf('%s-%s-%04d', $prefix, $date, $count);
    }

    public static function examTypes(): array
    {
        return [
            self::TYPE_CLASS_TEST => 'Class Test',
            self::TYPE_MONTHLY => 'Monthly Test',
            self::TYPE_MID_TERM => 'Mid Term',
            self::TYPE_PRE_TEST => 'Pre Test',
            self::TYPE_TEST_EXAM => 'Test Exam',
            self::TYPE_FINAL => 'Final Exam',
            self::TYPE_SEMESTER_FINAL => 'Semester Final',
            self::TYPE_IMPROVEMENT => 'Improvement Exam',
            self::TYPE_SUPPLEMENTARY => 'Supplementary Exam',
            self::TYPE_BOARD => 'Board Exam',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ONGOING => 'Ongoing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function publish(int $userId): void
    {
        $this->update([
            'is_published' => true,
            'published_at' => now(),
            'published_by' => $userId,
        ]);
    }

    public function unpublish(): void
    {
        $this->update([
            'is_published' => false,
            'published_at' => null,
            'published_by' => null,
        ]);
    }
}
