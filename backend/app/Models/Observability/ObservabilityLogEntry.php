<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Enums\Observability\LogLevel;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservabilityLogEntry extends Model
{
    use HasUuids, HasUuid;

    protected $table = 'observability_log_entries';

    protected $fillable = [
        'service_id',
        'level',
        'source',
        'message',
        'context',
        'extra',
        'environment',
        'host',
        'trace_id',
        'span_id',
        'logged_at',
    ];

    protected $casts = [
        'context' => 'array',
        'extra' => 'array',
        'logged_at' => 'datetime',
        'level' => LogLevel::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ObservabilityService::class, 'service_id');
    }

    public function scopeByLevel($query, LogLevel $level)
    {
        return $query->where('level', $level);
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeByService($query, string $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeByEnvironment($query, string $environment)
    {
        return $query->where('environment', $environment);
    }

    public function scopeByTraceId($query, string $traceId)
    {
        return $query->where('trace_id', $traceId);
    }

    public function scopeErrorLevel($query)
    {
        return $query->whereIn('level', [
            LogLevel::ERROR->value,
            LogLevel::CRITICAL->value,
            LogLevel::ALERT->value,
            LogLevel::EMERGENCY->value,
        ]);
    }

    public function scopeBetween($query, $start, $end)
    {
        return $query->whereBetween('logged_at', [$start, $end]);
    }

    public function scopeLatest($query, int $limit = 100)
    {
        return $query->orderBy('logged_at', 'desc')->limit($limit);
    }
}
