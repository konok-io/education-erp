<?php

declare(strict_types=1);

namespace App\Models\Research;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResearchProject extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'research_projects';

    protected $fillable = [
        'uuid', 'project_code', 'project_title', 'abstract', 'objectives',
        'expected_outcome', 'category', 'research_type', 'department',
        'keywords', 'start_date', 'end_date', 'status', 'priority',
        'budget', 'budget_currency', 'methodology', 'literature_review',
        'scope', 'limitations', 'references', 'ethics_approval',
        'ethics_certificate', 'principal_investigator_id', 'proposal_document',
        'progress_report', 'is_featured', 'is_public', 'created_by',
        'approved_by', 'approved_at',
    ];

    protected $casts = [
        'keywords' => 'array',
        'budget' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_DEPARTMENT_REVIEW = 'department_review';
    public const STATUS_COMMITTEE_REVIEW = 'committee_review';
    public const STATUS_ETHICS_REVIEW = 'ethics_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_TERMINATED = 'terminated';

    // ===================== RESEARCH TYPES =====================
    public const TYPE_FACULTY = 'faculty';
    public const TYPE_STUDENT = 'student';
    public const TYPE_COLLABORATIVE = 'collaborative';
    public const TYPE_GOVERNMENT = 'government';
    public const TYPE_INDUSTRY = 'industry';
    public const TYPE_INTERNATIONAL = 'international';
    public const TYPE_INNOVATION = 'innovation';

    // ===================== PRIORITIES =====================
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    // ===================== RELATIONSHIPS =====================

    public function principalInvestigator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'principal_investigator_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(ResearchTeam::class, 'project_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ResearchMilestone::class, 'project_id');
    }

    public function grants(): HasMany
    {
        return $this->hasMany(ResearchGrant::class, 'project_id');
    }

    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class, 'project_id');
    }

    public function patents(): HasMany
    {
        return $this->hasMany(Patent::class, 'project_id');
    }

    public function innovations(): HasMany
    {
        return $this->hasMany(Innovation::class, 'project_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    // ===================== METHODS =====================

    public static function generateProjectCode(): string
    {
        $prefix = 'RPRJ';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public static function researchTypes(): array
    {
        return [
            self::TYPE_FACULTY => 'Faculty Research',
            self::TYPE_STUDENT => 'Student Research',
            self::TYPE_COLLABORATIVE => 'Collaborative Research',
            self::TYPE_GOVERNMENT => 'Government Project',
            self::TYPE_INDUSTRY => 'Industry Project',
            self::TYPE_INTERNATIONAL => 'International Research',
            self::TYPE_INNOVATION => 'Innovation Project',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_DEPARTMENT_REVIEW => 'Department Review',
            self::STATUS_COMMITTEE_REVIEW => 'Committee Review',
            self::STATUS_ETHICS_REVIEW => 'Ethics Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_TERMINATED => 'Terminated',
        ];
    }

    public static function priorities(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_NORMAL => 'Normal',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }

    public function getProgressPercentage(): int
    {
        $totalMilestones = $this->milestones()->count();
        if ($totalMilestones === 0) {
            return 0;
        }
        $completed = $this->milestones()->where('status', 'completed')->count();
        return (int) round(($completed / $totalMilestones) * 100);
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function activate(): void
    {
        $this->update(['status' => self::STATUS_ACTIVE]);
    }

    public function complete(): void
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }
}
