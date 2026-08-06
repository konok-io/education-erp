<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exams';

    const TYPE_CLASS_TEST = 'class_test';
    const TYPE_QUIZ = 'quiz';
    const TYPE_ASSIGNMENT = 'assignment';
    const TYPE_MID_TERM = 'mid_term';
    const TYPE_FINAL = 'final';
    const TYPE_MODEL_TEST = 'model_test';
    const TYPE_ADMISSION = 'admission';
    const TYPE_PRACTICAL = 'practical';
    const TYPE_VIVA = 'viva';
    const TYPE_IMPROVEMENT = 'improvement';
    const TYPE_SUPPLEMENTARY = 'supplementary';

    const MODE_ONLINE = 'online';
    const MODE_OFFLINE = 'offline';
    const MODE_CBT = 'cbt';
    const MODE_OMR = 'omr';

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ONGOING = 'ongoing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'uuid',
        'exam_code',
        'title',
        'title_bn',
        'session_id',
        'subject_id',
        'teacher_id',
        'exam_type',
        'exam_date',
        'start_time',
        'end_time',
        'duration',
        'full_marks',
        'pass_marks',
        'practical_marks',
        'theory_marks',
        'center_id',
        'mode',
        'status',
        'description',
        'settings',
        'negative_marking',
        'negative_mark_value',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration' => 'integer',
        'full_marks' => 'decimal:2',
        'pass_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'theory_marks' => 'decimal:2',
        'negative_marking' => 'boolean',
        'negative_mark_value' => 'decimal:2',
        'settings' => 'array',
    ];

    public static function generateExamCode(): string
    {
        $prefix = 'EX';
        $year = date('Y');
        $month = date('m');
        $last = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $last ? ((int) substr($last->exam_code, -5)) + 1 : 1;
        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    public static function examTypes(): array
    {
        return [
            self::TYPE_CLASS_TEST => 'Class Test',
            self::TYPE_QUIZ => 'Quiz',
            self::TYPE_ASSIGNMENT => 'Assignment',
            self::TYPE_MID_TERM => 'Mid Term',
            self::TYPE_FINAL => 'Final Exam',
            self::TYPE_MODEL_TEST => 'Model Test',
            self::TYPE_ADMISSION => 'Admission Test',
            self::TYPE_PRACTICAL => 'Practical',
            self::TYPE_VIVA => 'Viva',
            self::TYPE_IMPROVEMENT => 'Improvement',
            self::TYPE_SUPPLEMENTARY => 'Supplementary',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'session_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Subject::class, 'subject_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'teacher_id');
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(ExamCenter::class, 'center_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class, 'exam_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class, 'exam_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(ExamAttendance::class, 'exam_id');
    }
}
