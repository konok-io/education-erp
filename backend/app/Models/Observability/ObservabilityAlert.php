<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Enums\Observability\AlertSeverity;
use App\Enums\Observability\AlertStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObservabilityAlert extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'observability_alerts';

    protected $fillable = [
        'service_id',
        'name',
        'description',
        'severity',
        'status',
        'metric_name',
        'condition',
        'threshold',
        'current_value',
        'environment',
        'metadata',
        'labels',
        'triggered_at',
        'resolved_at',
        'triggered_by_user_id',
        'acknowledged_by_user_id',
    ];

    protected $casts = [
        'threshold' => 'decimal:6',
        'current_value' => 'decimal:6',
        'triggered_at' => 'datetime',
        'resolved_at' => 'datetime',
        'metadata' => 'array',
        'labels' => 'array',
        'severity' => AlertSeverity::class,
        'status' => AlertStatus::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ObservabilityService::class, 'service_id');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(ObservabilityIncident::class, 'alert_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(ObservabilityNotification::class, 'alert_id');
    }

    public function triggeredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by_user_id');
    }

    public function acknowledgedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by_user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', AlertStatus::ACTIVE);
    }

    public function scopeBySeverity($query, AlertSeverity $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByService($query, string $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeByEnvironment($query, string $environment)
    {
        return $query->where('environment', $environment);
    }

    public function scopeTriggered($query)
    {
        return $query->whereNotNull('triggered_at');
    }

    public function scopeResolved($query)
    {
        return $query->whereNotNull('resolved_at');
    }

    public function scopeBetween($query, $start, $end)
    {
        return $query->whereBetween('triggered_at', [$start, $end]);
    }

    public function isActive(): bool
    {
        return $this->status === AlertStatus::ACTIVE;
    }

    public function isResolved(): bool
    {
        return $this->status === AlertStatus::RESOLVED;
    }

    public function resolve(): void
    {
        $this->update([
            'status' => AlertStatus::RESOLVED,
            'resolved_at' => now(),
        ]);
    }

    public function acknowledge(string $userId): void
    {
        $this->update([
            'status' => AlertStatus::ACKNOWLEDGED,
            'acknowledged_by_user_id' => $userId,
        ]);
    }
}
