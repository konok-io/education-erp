<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Enums\Observability\MetricType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservabilityMetric extends Model
{
    use HasUuids, HasUuid;

    protected $table = 'observability_metrics';

    protected $fillable = [
        'service_id',
        'name',
        'type',
        'value',
        'unit',
        'timestamp',
        'labels',
        'tags',
        'environment',
    ];

    protected $casts = [
        'value' => 'decimal:6',
        'timestamp' => 'datetime',
        'labels' => 'array',
        'tags' => 'array',
        'type' => MetricType::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ObservabilityService::class, 'service_id');
    }

    public function scopeByService($query, string $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name);
    }

    public function scopeByType($query, MetricType $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByEnvironment($query, string $environment)
    {
        return $query->where('environment', $environment);
    }

    public function scopeBetween($query, $start, $end)
    {
        return $query->whereBetween('timestamp', [$start, $end]);
    }

    public function scopeLatest($query, int $limit = 100)
    {
        return $query->orderBy('timestamp', 'desc')->limit($limit);
    }
}
