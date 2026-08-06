<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservabilityIncidentTimelineEvent extends Model
{
    use HasUuids, HasUuid;

    protected $table = 'observability_incident_timeline_events';

    protected $fillable = [
        'incident_id',
        'event_type',
        'title',
        'description',
        'user_id',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(ObservabilityIncident::class, 'incident_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeByType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeByIncident($query, string $incidentId)
    {
        return $query->where('incident_id', $incidentId);
    }
}
