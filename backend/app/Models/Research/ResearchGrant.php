<?php

declare(strict_types=1);

namespace App\Models\Research;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResearchGrant extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'research_grants';

    protected $fillable = [
        'uuid', 'grant_number', 'project_id', 'grant_title', 'description',
        'funding_agency_id', 'grant_amount', 'currency', 'application_date',
        'approval_date', 'start_date', 'end_date', 'status', 'budget_breakdown',
        'released_amount', 'terms_conditions', 'agreement_document', 'created_by',
    ];

    protected $casts = [
        'grant_amount' => 'decimal:2',
        'released_amount' => 'decimal:2',
        'budget_breakdown' => 'array',
        'application_date' => 'date',
        'approval_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_TERMINATED = 'terminated';

    // ===================== RELATIONSHIPS =====================

    public function project(): BelongsTo
    {
        return $this->belongsTo(ResearchProject::class, 'project_id');
    }

    public function fundingAgency(): BelongsTo
    {
        return $this->belongsTo(FundingAgency::class, 'funding_agency_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    // ===================== METHODS =====================

    public static function generateGrantNumber(): string
    {
        $prefix = 'GRT';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_TERMINATED => 'Terminated',
        ];
    }

    public function getRemainingAmount(): float
    {
        return (float) $this->grant_amount - (float) $this->released_amount;
    }

    public function releaseAmount(float $amount): void
    {
        $newReleased = (float) $this->released_amount + $amount;
        $this->update([
            'released_amount' => $newReleased,
            'status' => $newReleased >= $this->grant_amount ? self::STATUS_COMPLETED : self::STATUS_ACTIVE,
        ]);
    }
}
