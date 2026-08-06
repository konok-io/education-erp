<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Enums\Observability\AlertSeverity;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservabilityAuditLog extends Model
{
    use HasUuids, HasUuid;

    protected $table = 'observability_audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'event_type',
        'severity',
        'service_id',
        'user_id',
        'event_data',
        'metadata',
        'source',
        'environment',
        'occurred_at',
    ];

    protected $casts = [
        'event_data' => 'array',
        'metadata' => 'array',
        'occurred_at' => 'datetime',
        'severity' => AlertSeverity::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ObservabilityService::class, 'service_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeByService($query, string $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeByEnvironment($query, string $environment)
    {
        return $query->where('environment', $environment);
    }

    public function scopeBetween($query, $start, $end)
    {
        return $query->whereBetween('occurred_at', [$start, $end]);
    }

    public function scopeLatest($query, int $limit = 100)
    {
        return $query->orderBy('occurred_at', 'desc')->limit($limit);
    }

    public static function log(
        string $eventType,
        string $severity,
        ?string $serviceId = null,
        ?string $userId = null,
        ?array $eventData = null,
        ?array $metadata = null,
        string $source = 'system',
        string $environment = 'production'
    ): self {
        return self::create([
            'event_type' => $eventType,
            'severity' => $severity,
            'service_id' => $serviceId,
            'user_id' => $userId,
            'event_data' => $eventData,
            'metadata' => $metadata,
            'source' => $source,
            'environment' => $environment,
            'occurred_at' => now(),
        ]);
    }
}
