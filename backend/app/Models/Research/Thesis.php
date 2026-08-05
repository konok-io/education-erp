<?php

declare(strict_types=1);

namespace App\Models\Research;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thesis extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'theses';

    protected $fillable = [
        'uuid', 'thesis_number', 'thesis_title', 'abstract', 'thesis_type',
        'student_id', 'student_name', 'student_email', 'student_roll',
        'department', 'program', 'supervisor', 'co_supervisor', 'degree',
        'submission_year', 'submission_date', 'defense_date', 'defense_score',
        'grade', 'committee_members', 'keywords', 'status', 'pdf_document',
        'doi', 'acknowledgments', 'references', 'project_id', 'created_by',
    ];

    protected $casts = [
        'committee_members' => 'array',
        'keywords' => 'array',
        'defense_score' => 'decimal:2',
        'submission_date' => 'date',
        'defense_date' => 'date',
    ];

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_DEFENSE_SCHEDULED = 'defense_scheduled';
    public const STATUS_DEFENDED = 'defended';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ARCHIVED = 'archived';

    // ===================== THESIS TYPES =====================
    public const TYPE_THESIS = 'thesis';
    public const TYPE_DISSERTATION = 'dissertation';
    public const TYPE_PROJECT_REPORT = 'project_report';

    // ===================== RELATIONSHIPS =====================

    public function project(): BelongsTo
    {
        return $this->belongsTo(ResearchProject::class, 'project_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // ===================== METHODS =====================

    public static function generateThesisNumber(): string
    {
        $prefix = 'THS';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_DEFENSE_SCHEDULED => 'Defense Scheduled',
            self::STATUS_DEFENDED => 'Defended',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }
}
