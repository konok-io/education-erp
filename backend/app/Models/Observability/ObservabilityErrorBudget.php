<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservabilityErrorBudget extends Model
{
    use HasUuids, HasUuid;

    protected $table = 'observability_error_budgets';

    protected $fillable = [
        'slo_id',
        'period',
        'start_date',
        'end_date',
        'total_budget',
        'spent_budget',
        'remaining_budget',
        'burn_rate',
        'projected_budget_exhaustion',
        'is_breached',
        'metadata',
    ];

    protected $casts = [
        'total_budget' => 'decimal:6',
        'spent_budget' => 'decimal:6',
        'remaining_budget' => 'decimal:6',
        'burn_rate' => 'decimal:4',
        'projected_budget_exhaustion' => 'decimal:4',
        'is_breached' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'metadata' => 'array',
    ];

    public function slo(): BelongsTo
    {
        return $this->belongsTo(ObservabilitySlo::class, 'slo_id');
    }

    public function scopeBySlo($query, string $sloId)
    {
        return $query->where('slo_id', $sloId);
    }

    public function scopeByPeriod($query, string $period)
    {
        return $query->where('period', $period);
    }

    public function scopeCurrent($query)
    {
        return $query->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopeBreached($query)
    {
        return $query->where('is_breached', true);
    }

    public function calculateRemaining(): float
    {
        return max(0, $this->total_budget - $this->spent_budget);
    }

    public function calculateBurnRate(): float
    {
        if ($this->total_budget <= 0) {
            return 0;
        }
        
        return $this->spent_budget / $this->total_budget;
    }

    public function updateSpentBudget(float $amount): void
    {
        $this->update([
            'spent_budget' => $this->spent_budget + $amount,
            'remaining_budget' => $this->calculateRemaining(),
            'burn_rate' => $this->calculateBurnRate(),
        ]);
    }
}
