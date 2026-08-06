<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Enums\Observability\SyntheticTestType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObservabilitySyntheticTest extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'observability_synthetic_tests';

    protected $fillable = [
        'service_id',
        'name',
        'type',
        'endpoint',
        'method',
        'schedule',
        'status',
        'request_config',
        'assertions',
        'environment',
        'headers',
        'cookies',
        'follow_redirects',
        'timeout_seconds',
        'last_run_at',
        'last_run_status',
        'last_run_duration_ms',
        'last_run_result',
        'is_active',
    ];

    protected $casts = [
        'request_config' => 'array',
        'assertions' => 'array',
        'headers' => 'array',
        'cookies' => 'array',
        'last_run_result' => 'array',
        'is_active' => 'boolean',
        'follow_redirects' => 'boolean',
        'last_run_at' => 'datetime',
        'last_run_duration_ms' => 'decimal:6',
        'type' => SyntheticTestType::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ObservabilityService::class, 'service_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(ObservabilitySyntheticTestResult::class, 'test_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, SyntheticTestType $type)
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

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function isHealthy(): bool
    {
        return $this->last_run_status === 'success';
    }

    public function isFailing(): bool
    {
        return $this->last_run_status === 'failed';
    }

    public function updateLastRun(string $status, float $duration, ?array $result = null): void
    {
        $this->update([
            'last_run_at' => now(),
            'last_run_status' => $status,
            'last_run_duration_ms' => $duration,
            'last_run_result' => $result,
        ]);
    }
}
