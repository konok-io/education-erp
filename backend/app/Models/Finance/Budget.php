<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'budgets';

    protected $fillable = [
        'uuid',
        'budget_code',
        'name',
        'fiscal_year_id',
        'cost_center_id',
        'account_id',
        'budget_type',
        'amount',
        'allocated_amount',
        'spent_amount',
        'start_date',
        'end_date',
        'status',
        'approved_by',
        'approved_at',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'allocated_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // ===================== TYPES =====================
    public const TYPE_ANNUAL = 'annual';
    public const TYPE_MONTHLY = 'monthly';
    public const TYPE_QUARTERLY = 'quarterly';
    public const TYPE_PROJECT = 'project';

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXCEEDED = 'exceeded';
    public const STATUS_CLOSED = 'closed';

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(BudgetAllocation::class, 'budget_id');
    }

    public static function types(): array
    {
        return [
            self::TYPE_ANNUAL => 'Annual Budget',
            self::TYPE_MONTHLY => 'Monthly Budget',
            self::TYPE_QUARTERLY => 'Quarterly Budget',
            self::TYPE_PROJECT => 'Project Budget',
        ];
    }

    public function getRemainingBudget(): float
    {
        return (float) ($this->allocated_amount - $this->spent_amount);
    }

    public function getUtilizationPercentage(): float
    {
        if ($this->allocated_amount <= 0) {
            return 0;
        }

        return round(($this->spent_amount / $this->allocated_amount) * 100, 2);
    }

    public function isExceeded(): bool
    {
        return $this->spent_amount > $this->allocated_amount;
    }
}
