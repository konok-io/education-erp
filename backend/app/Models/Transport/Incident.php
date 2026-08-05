<?php

declare(strict_types=1);

namespace App\Models\Transport;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incident extends Model
{
    use HasUuid;

    protected $table = 'incidents';

    protected $fillable = [
        'uuid',
        'incident_no',
        'vehicle_id',
        'driver_id',
        'incident_date',
        'incident_type',
        'description',
        'status',
        'reported_by',
        'resolution',
        'resolved_at',
        'remarks',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'resolved_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_REPORTED = 'reported';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    // ===================== INCIDENT TYPES =====================
    public const TYPE_BREAKDOWN = 'breakdown';
    public const TYPE_LATE_ARRIVAL = 'late_arrival';
    public const TYPE_ROUTE_CHANGE = 'route_change';
    public const TYPE_COMPLAINT = 'complaint';
    public const TYPE_TRAFFIC = 'traffic';
    public const TYPE_WEATHER = 'weather';
    public const TYPE_OTHER = 'other';

    // ===================== RELATIONSHIPS =====================

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reported_by');
    }

    // ===================== SCOPES =====================

    public function scopeUnresolved($query)
    {
        return $query->whereNotIn('status', [self::STATUS_RESOLVED, self::STATUS_CLOSED]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('incident_date', now()->toDateString());
    }

    // ===================== METHODS =====================

    public static function generateIncidentNo(): string
    {
        $prefix = 'INC';
        $year = now()->format('Y');
        $count = self::whereYear('incident_date', now()->year)->count() + 1;
        return sprintf('%s/%s/%04d', $prefix, $year, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_REPORTED => 'Reported',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_CLOSED => 'Closed',
        ];
    }

    public static function incidentTypes(): array
    {
        return [
            self::TYPE_BREAKDOWN => 'Breakdown',
            self::TYPE_LATE_ARRIVAL => 'Late Arrival',
            self::TYPE_ROUTE_CHANGE => 'Route Change',
            self::TYPE_COMPLAINT => 'Passenger Complaint',
            self::TYPE_TRAFFIC => 'Traffic Issue',
            self::TYPE_WEATHER => 'Weather Related',
            self::TYPE_OTHER => 'Other',
        ];
    }

    public function resolve(string $resolution): void
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolution' => $resolution,
            'resolved_at' => now(),
        ]);
    }

    public function close(): void
    {
        $this->update(['status' => self::STATUS_CLOSED]);
    }
}
