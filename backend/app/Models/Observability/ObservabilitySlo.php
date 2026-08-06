<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Enums\Observability\SLOType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObservabilitySlo extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'observability_slos';

    protected $fillable = [
        'service_id',
        'name',
        'description',
        'type',
        'indicator_type',
        'indicator_name',
        'target_percentage',
        'current_percentage',
        'time_window',
        'environment',
        'threshold_config',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'target_percentage' => 'decimal:4',
        'current_percentage' => 'decimal:4',
        'threshold_config' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'type' => SLOType::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ObservabilityService::class, 'service_id');
    }

    public function errorBudgets(): HasMany
    {
        return $this->hasMany(ObservabilityErrorBudget::class, 'slo_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, SLOType $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByService($query, string $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeByEnvironment($query, string $environment)
    {
        return $query->where('environment', $environment);
    }

    public function isBreached(): bool
    {
        return $this->current_percentage < $this->target_percentage;
    }

    public function calculateErrorBudget(): float
    {
        $totalBudget = 100 - $this->target_percentage;
        $spentBudget = 100 - $this->current_percentage;
        
        return max(0, $totalBudget - $spentBudget);
    }

    public function getBurnRate(): float
    {
        if ($this->current_percentage >= $this->target_percentage) {
            return 0;
        }
        
        $totalBudget = 100 - $this->target_percentage;
        $spentBudget = 100 - $this->current_percentage;
        
        return $spentBudget / $totalBudget;
    }
}
