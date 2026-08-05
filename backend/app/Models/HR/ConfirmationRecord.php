<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfirmationRecord extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'confirmation_records';

    protected $fillable = [
        'uuid',
        'confirmation_no',
        'employee_id',
        'probation_start_date',
        'probation_end_date',
        'performance_summary',
        'recommendation',
        'recommendation_remarks',
        'status',
        'recommended_by',
        'recommended_date',
        'reviewed_by',
        'reviewed_date',
        'approved_by',
        'approved_date',
        'confirmation_date',
        'confirmation_letter',
        'remarks',
    ];

    protected $casts = [
        'probation_start_date' => 'date',
        'probation_end_date' => 'date',
        'recommended_date' => 'datetime',
        'reviewed_date' => 'datetime',
        'approved_date' => 'datetime',
        'confirmation_date' => 'date',
    ];

    // ===================== RECOMMENDATIONS =====================
    public const RECOMMEND_CONFIRM = 'confirm';
    public const RECOMMEND_EXTEND = 'extend_probation';
    public const RECOMMEND_TERMINATE = 'terminate';

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_RECOMMENDED = 'recommended';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function recommendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommended_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ===================== METHODS =====================

    public static function generateConfirmationNo(): string
    {
        $prefix = 'CONF';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%05d', $prefix, $year, $count);
    }

    public static function recommendations(): array
    {
        return [
            self::RECOMMEND_CONFIRM => 'Confirm Employment',
            self::RECOMMEND_EXTEND => 'Extend Probation',
            self::RECOMMEND_TERMINATE => 'Terminate Employment',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_RECOMMENDED => 'Recommended',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function getProbationDurationAttribute(): int
    {
        return $this->probation_start_date->diffInMonths($this->probation_end_date);
    }

    public function isProbationEnded(): bool
    {
        return now()->greaterThanOrEqualTo($this->probation_end_date);
    }
}
