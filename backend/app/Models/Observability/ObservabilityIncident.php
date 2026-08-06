<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Enums\Observability\IncidentSeverity;
use App\Enums\Observability\IncidentStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObservabilityIncident extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'observability_incidents';

    protected $fillable = [
        'incident_number',
        'title',
        'description',
        'severity',
        'status',
        'service_id',
        'alert_id',
        'environment',
        'affected_components',
        'impact',
        'started_at',
        'acknowledged_at',
        'resolved_at',
        'closed_at',
        'assigned_to_user_id',
        'created_by_user_id',
        'postmortem',
        'timeline',
        'metadata',
    ];

    protected $casts = [
        'affected_components' => 'array',
        'impact' => 'array',
        'started_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'timeline' => 'array',
        'metadata' => 'array',
        'severity' => IncidentSeverity::class,
        'status' => IncidentStatus::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ObservabilityService::class, 'service_id');
    }

    public function alert(): BelongsTo
    {
        return $this->belongsTo(ObservabilityAlert::class, 'alert_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(ObservabilityIncidentTimelineEvent::class, 'incident_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(ObservabilityNotification::class, 'incident_id');
    }

    public function statusPageIncidents(): HasMany
    {
        return $this->hasMany(ObservabilityStatusPageIncident::class, 'incident_id');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            IncidentStatus::TRIGGERED->value,
            IncidentStatus::ACKNOWLEDGED->value,
            IncidentStatus::INVESTIGATING->value,
        ]);
    }

    public function scopeBySeverity($query, IncidentSeverity $severity)
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

    public function scopeBetween($query, $start, $end)
    {
        return $query->whereBetween('started_at', [$start, $end]);
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isResolved(): bool
    {
        return $this->status->isResolved();
    }

    public function acknowledge(string $userId): void
    {
        $this->update([
            'status' => IncidentStatus::ACKNOWLEDGED,
            'acknowledged_at' => now(),
        ]);
        $this->addTimelineEvent('status_change', 'Incident acknowledged', $userId);
    }

    public function resolve(string $userId): void
    {
        $this->update([
            'status' => IncidentStatus::RESOLVED,
            'resolved_at' => now(),
        ]);
        $this->addTimelineEvent('status_change', 'Incident resolved', $userId);
    }

    public function close(string $userId): void
    {
        $this->update([
            'status' => IncidentStatus::CLOSED,
            'closed_at' => now(),
        ]);
        $this->addTimelineEvent('status_change', 'Incident closed', $userId);
    }

    public function addTimelineEvent(string $eventType, string $title, ?string $userId = null, ?string $description = null, ?array $metadata = null): void
    {
        $this->timelineEvents()->create([
            'event_type' => $eventType,
            'title' => $title,
            'description' => $description,
            'user_id' => $userId,
            'metadata' => $metadata,
            'occurred_at' => now(),
        ]);
    }

    public static function generateIncidentNumber(): string
    {
        $prefix = 'INC';
        $date = now()->format('Ymd');
        $lastIncident = self::whereDate('created_at', now()->toDateString())
            ->orderBy('incident_number', 'desc')
            ->first();
        
        $sequence = $lastIncident ? ((int) substr($lastIncident->incident_number, -4)) + 1 : 1;
        
        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }
}
