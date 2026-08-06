<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Enums\Observability\ServiceStatus;
use App\Enums\Observability\ServiceType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObservabilityService extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'observability_services';

    protected $fillable = [
        'name',
        'type',
        'environment',
        'status',
        'metadata',
        'tags',
        'is_active',
    ];

    protected $casts = [
        'metadata' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
        'status' => ServiceStatus::class,
        'type' => ServiceType::class,
    ];

    public function metrics(): HasMany
    {
        return $this->hasMany(ObservabilityMetric::class, 'service_id');
    }

    public function logEntries(): HasMany
    {
        return $this->hasMany(ObservabilityLogEntry::class, 'service_id');
    }

    public function traces(): HasMany
    {
        return $this->hasMany(ObservabilityTrace::class, 'service_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(ObservabilityAlert::class, 'service_id');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(ObservabilityIncident::class, 'service_id');
    }

    public function healthChecks(): HasMany
    {
        return $this->hasMany(ObservabilityHealthCheck::class, 'service_id');
    }

    public function slos(): HasMany
    {
        return $this->hasMany(ObservabilitySlo::class, 'service_id');
    }

    public function syntheticTests(): HasMany
    {
        return $this->hasMany(ObservabilitySyntheticTest::class, 'service_id');
    }

    public function runbooks(): HasMany
    {
        return $this->hasMany(ObservabilityRunbook::class, 'service_id');
    }

    public function dashboards(): HasMany
    {
        return $this->hasMany(ObservabilityDashboard::class, 'service_id');
    }

    public function chaosExperiments(): HasMany
    {
        return $this->hasMany(ObservabilityChaosExperiment::class, 'service_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByEnvironment($query, string $environment)
    {
        return $query->where('environment', $environment);
    }

    public function scopeByStatus($query, ServiceStatus $status)
    {
        return $query->where('status', $status);
    }
}
