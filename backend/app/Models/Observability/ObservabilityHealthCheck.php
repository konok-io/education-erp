<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Enums\Observability\HealthCheckStatus;
use App\Enums\Observability\HealthCheckType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObservabilityHealthCheck extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'observability_health_checks';

    protected $fillable = [
        'service_id',
        'name',
        'type',
        'endpoint',
        'method',
        'status',
        'check_interval_seconds',
        'timeout_seconds',
        'retry_count',
        'headers',
        'expected_response',
        'is_active',
        'environment',
        'metadata',
        'last_check_at',
        'last_status',
        'last_error',
        'last_response_time_ms',
    ];

    protected $casts = [
        'headers' => 'array',
        'expected_response' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'last_check_at' => 'datetime',
        'last_response_time_ms' => 'decimal:6',
        'type' => HealthCheckType::class,
        'status' => HealthCheckStatus::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ObservabilityService::class, 'service_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(ObservabilityHealthCheckResult::class, 'health_check_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, HealthCheckType $type)
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

    public function scopeByStatus($query, HealthCheckStatus $status)
    {
        return $query->where('status', $status);
    }

    public function isHealthy(): bool
    {
        return $this->status === HealthCheckStatus::HEALTHY;
    }

    public function isUnhealthy(): bool
    {
        return $this->status === HealthCheckStatus::UNHEALTHY;
    }

    public function updateStatus(HealthCheckStatus $status, ?string $error = null, ?float $responseTime = null): void
    {
        $this->update([
            'status' => $status,
            'last_check_at' => now(),
            'last_status' => $status->value,
            'last_error' => $error,
            'last_response_time_ms' => $responseTime,
        ]);
    }
}
