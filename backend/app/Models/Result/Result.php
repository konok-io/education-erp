<?php

declare(strict_types=1);

namespace App\Models\Result;

use App\Models\Student\Student;
use App\Models\Academic\AcademicSession;
use App\Models\Academic\AcademicClass;
use App\Models\Academic\Semester;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Result extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'results';

    protected $fillable = [
        'uuid',
        'result_no',
        'student_id',
        'exam_id',
        'class_id',
        'section_id',
        'session_id',
        'semester_id',
        'total_marks',
        'obtained_marks',
        'gpa',
        'grade',
        'status',
        'is_published',
        'published_at',
        'remarks',
        'created_by',
        'verified_by',
        'verified_at',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'total_marks' => 'decimal:2',
        'obtained_marks' => 'decimal:2',
        'gpa' => 'decimal:2',
        'is_published' => 'boolean',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    // ===================== RELATIONSHIPS =====================

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Section::class, 'section_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(ResultDetail::class, 'result_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
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

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByStudent($query, string $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByClass($query, string $classId)
    {
        return $query->where('class_id', $classId);
    }

    // ===================== METHODS =====================

    public static function generateResultNo(): string
    {
        $prefix = 'RES';
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now()->toDateString())->count() + 1;
        return sprintf('%s-%s-%04d', $prefix, $date, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_VERIFIED => 'Verified',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    public function calculateGPA(): float
    {
        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($this->details as $detail) {
            if ($detail->grade_point > 0) {
                $totalPoints += $detail->grade_point * $detail->credit;
                $totalCredits += $detail->credit;
            }
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;
    }

    public function publish(int $userId): void
    {
        $this->update([
            'is_published' => true,
            'status' => self::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
    }
}
