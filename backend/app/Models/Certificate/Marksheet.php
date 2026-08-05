<?php

declare(strict_types=1);

namespace App\Models\Certificate;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Marksheet extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'marksheets';

    protected $fillable = [
        'uuid',
        'marksheet_number',
        'student_id',
        'student_name',
        'student_roll',
        'registration_no',
        'father_name',
        'mother_name',
        'department',
        'class_name',
        'session',
        'semester',
        'subject_marks',
        'total_marks',
        'obtained_marks',
        'grade',
        'gpa',
        'result_status',
        'remarks',
        'qr_code',
        'verification_token',
        'pdf_path',
        'signature_id',
        'seal_id',
        'issue_date',
        'status',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'subject_marks' => 'array',
        'total_marks' => 'decimal:2',
        'obtained_marks' => 'decimal:2',
        'gpa' => 'decimal:2',
        'issue_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_APPROVAL = 'pending_approval';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_ISSUED = 'issued';

    // ===================== RESULT STATUS =====================
    public const RESULT_PASSED = 'passed';
    public const RESULT_FAILED = 'failed';
    public const RESULT_PROMOTED = 'promoted';

    // ===================== RELATIONSHIPS =====================

    public function signature(): BelongsTo
    {
        return $this->belongsTo(DigitalSignature::class, 'signature_id');
    }

    public function seal(): BelongsTo
    {
        return $this->belongsTo(DigitalSeal::class, 'seal_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // ===================== SCOPES =====================

    public function scopeIssued($query)
    {
        return $query->where('status', self::STATUS_ISSUED);
    }

    // ===================== METHODS =====================

    public static function generateMarksheetNumber(): string
    {
        $prefix = 'MS';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public static function generateVerificationToken(): string
    {
        return Str::random(32);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING_APPROVAL => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_ISSUED => 'Issued',
        ];
    }

    public static function resultStatuses(): array
    {
        return [
            self::RESULT_PASSED => 'Passed',
            self::RESULT_FAILED => 'Failed',
            self::RESULT_PROMOTED => 'Promoted',
        ];
    }

    public function calculateGrade(): void
    {
        $percentage = $this->total_marks > 0 ? ($this->obtained_marks / $this->total_marks) * 100 : 0;

        $this->grade = match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'A-',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C',
            $percentage >= 40 => 'D',
            default => 'F',
        };
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function issue(): void
    {
        $this->update([
            'status' => self::STATUS_ISSUED,
            'issue_date' => now(),
        ]);
    }
}
