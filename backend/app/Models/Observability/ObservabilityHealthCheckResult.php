<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservabilityHealthCheckResult extends Model
{
    use HasUuids, HasUuid;

    protected $table = 'observability_health_check_results';

    protected $fillable = [
        'health_check_id',
        'status',
        'response_time_ms',
        'http_status_code',
        'error_message',
        'response_body',
        'checked_at',
    ];

    protected $casts = [
        'response_time_ms' => 'decimal:6',
        'response_body' => 'array',
        'checked_at' => 'datetime',
    ];

    public function healthCheck(): BelongsTo
    {
        return $this->belongsTo(ObservabilityHealthCheck::class, 'health_check_id');
    }

    public function scopeByHealthCheck($query, string $healthCheckId)
    {
        return $query->where('health_check_id', $healthCheckId);
    }

    public function scopeBetween($query, $start, $end)
    {
        return $query->whereBetween('checked_at', [$start, $end]);
    }

    public function scopeLatest($query, int $limit = 100)
    {
        return $query->orderBy('checked_at', 'desc')->limit($limit);
    }

    public function isHealthy(): bool
    {
        return $this->status === 'healthy';
    }

    public function isUnhealthy(): bool
    {
        return $this->status === 'unhealthy';
    }
}
