<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exam_results';

    const STATUS_PENDING = 'pending';
    const STATUS_EVALUATED = 'evaluated';
    const STATUS_VERIFIED = 'verified';
    const STATUS_APPROVED = 'approved';
    const STATUS_PUBLISHED = 'published';

    const RESULT_PASSED = 'passed';
    const RESULT_FAILED = 'failed';
    const RESULT_ABSENT = 'absent';
    const RESULT_DISQUALIFIED = 'disqualified';

    protected $fillable = [
        'uuid',
        'result_no',
        'exam_id',
        'student_id',
        'student_name',
        'roll_number',
        'obtained_marks',
        'full_marks',
        'pass_marks',
        'percentage',
        'status',
        'result',
        'total_correct',
        'total_wrong',
        'total_not_answered',
        'negative_marks',
        'teacher_remarks',
        'evaluated_by',
        'evaluated_at',
        'verified_by',
        'verified_at',
        'approved_by',
        'approved_at',
        'published_at',
    ];

    protected $casts = [
        'obtained_marks' => 'decimal:2',
        'full_marks' => 'decimal:2',
        'pass_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'negative_marks' => 'decimal:2',
        'total_correct' => 'integer',
        'total_wrong' => 'integer',
        'total_not_answered' => 'integer',
        'evaluated_at' => 'datetime',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public static function generateResultNo(): string
    {
        $prefix = 'ER';
        $year = date('Y');
        $last = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $sequence = $last ? ((int) substr($last->result_no, -5)) + 1 : 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'evaluated_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function isPassed(): bool
    {
        return $this->result === self::RESULT_PASSED;
    }

    public function calculateResult(): void
    {
        $percentage = ($this->obtained_marks / $this->full_marks) * 100;
        $this->percentage = round($percentage, 2);

        if ($this->percentage >= 40) {
            $this->result = self::RESULT_PASSED;
        } else {
            $this->result = self::RESULT_FAILED;
        }
    }
}
