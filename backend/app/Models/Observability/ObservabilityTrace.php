<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ObservabilityTrace extends Model
{
    use HasUuids, HasUuid;

    protected $table = 'observability_traces';

    protected $fillable = [
        'service_id',
        'trace_id',
        'name',
        'operation',
        'status',
        'duration_ms',
        'environment',
        'started_at',
        'ended_at',
        'tags',
    ];

    protected $casts = [
        'duration_ms' => 'decimal:6',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'tags' => 'array',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ObservabilityService::class, 'service_id');
    }

    public function spans(): HasMany
    {
        return $this->hasMany(ObservabilitySpan::class, 'trace_id', 'trace_id');
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

    public function scopeByTraceId($query, string $traceId)
    {
        return $query->where('trace_id', $traceId);
    }

    public function scopeBetween($query, $start, $end)
    {
        return $query->whereBetween('started_at', [$start, $end]);
    }

    public function scopeSlow($query, float $threshold = 1000)
    {
        return $query->where('duration_ms', '>', $threshold);
    }

    public function scopeWithErrors($query)
    {
        return $query->where('status', 'error');
    }
}
