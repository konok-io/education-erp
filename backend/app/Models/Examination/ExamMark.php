<?php

declare(strict_types=1);

namespace App\Models\Examination;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamMark extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'exam_marks';

    protected $fillable = [
        'uuid',
        'exam_subject_id',
        'student_id',
        'student_name',
        'student_roll',
        'theory_marks',
        'practical_marks',
        'total_marks',
        'pass_marks',
        'result',
        'grade',
        'teacher_remarks',
        'moderator_remarks',
        'status',
        'entered_by',
        'verified_by',
        'approved_by',
        'entered_at',
        'verified_at',
        'approved_at',
    ];

    protected $casts = [
        'theory_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'pass_marks' => 'decimal:2',
        'entered_at' => 'datetime',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_LOCKED = 'locked';
    public const STATUS_PUBLISHED = 'published';

    // ===================== RESULTS =====================
    public const RESULT_PASS = 'pass';
    public const RESULT_FAIL = 'fail';
    public const RESULT_ABSENT = 'absent';

    // ===================== RELATIONSHIPS =====================

    public function subject(): BelongsTo
    {
        return $this->belongsTo(ExamSubject::class, 'exam_subject_id');
    }

    public function entryBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'entered_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // ===================== SCOPES =====================

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    // ===================== METHODS =====================

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_VERIFIED => 'Verified',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_LOCKED => 'Locked',
            self::STATUS_PUBLISHED => 'Published',
        ];
    }

    public static function calculateGrade(float $marks, float $total): string
    {
        $percentage = ($marks / $total) * 100;

        return match (true) {
            $percentage >= 80 => 'A+',
            $percentage >= 70 => 'A',
            $percentage >= 60 => 'A-',
            $percentage >= 50 => 'B',
            $percentage >= 40 => 'C',
            default => 'F',
        };
    }

    public function calculateTotal(): void
    {
        $this->total_marks = ($this->theory_marks ?? 0) + ($this->practical_marks ?? 0);
    }

    public function evaluateResult(float $passMarks): void
    {
        if ($this->total_marks >= $passMarks) {
            $this->result = self::RESULT_PASS;
        } else {
            $this->result = self::RESULT_FAIL;
        }
    }

    public function submit(): void
    {
        $this->update([
            'status' => self::STATUS_SUBMITTED,
            'entered_at' => now(),
            'entered_by' => auth()->id(),
        ]);
    }

    public function verify(): void
    {
        $this->update([
            'status' => self::STATUS_VERIFIED,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);
    }

    public function approve(): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);
    }

    public function lock(): void
    {
        $this->update(['status' => self::STATUS_LOCKED]);
    }

    public function publish(): void
    {
        $this->update(['status' => self::STATUS_PUBLISHED]);
    }
}
