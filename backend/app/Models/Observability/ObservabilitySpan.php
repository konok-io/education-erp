<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ObservabilitySpan extends Model
{
    use HasUuids, HasUuid;

    protected $table = 'observability_spans';

    protected $fillable = [
        'trace_id',
        'parent_id',
        'span_id',
        'name',
        'type',
        'status',
        'duration_ms',
        'attributes',
        'events',
        'links',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'duration_ms' => 'decimal:6',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'attributes' => 'array',
        'events' => 'array',
        'links' => 'array',
    ];

    public function trace(): BelongsTo
    {
        return $this->belongsTo(ObservabilityTrace::class, 'trace_id', 'trace_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ObservabilitySpan::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ObservabilitySpan::class, 'parent_id');
    }

    public function scopeByTrace($query, string $traceId)
    {
        return $query->where('trace_id', $traceId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeWithErrors($query)
    {
        return $query->where('status', 'error');
    }
}
