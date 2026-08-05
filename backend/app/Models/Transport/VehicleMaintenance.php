<?php

declare(strict_types=1);

namespace App\Models\Transport;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleMaintenance extends Model
{
    use HasUuid;

    protected $table = 'vehicle_maintenances';

    protected $fillable = [
        'uuid',
        'maintenance_no',
        'vehicle_id',
        'maintenance_type',
        'priority',
        'service_date',
        'next_service_date',
        'vendor',
        'technician_name',
        'cost',
        'description',
        'work_done',
        'status',
        'odometer',
        'created_by',
    ];

    protected $casts = [
        'service_date' => 'date',
        'next_service_date' => 'date',
        'cost' => 'decimal:2',
    ];

    // ===================== STATUS =====================
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== MAINTENANCE TYPES =====================
    public const TYPE_ROUTINE = 'routine';
    public const TYPE_ENGINE = 'engine';
    public const TYPE_OIL_CHANGE = 'oil_change';
    public const TYPE_TYRE = 'tyre';
    public const TYPE_BATTERY = 'battery';
    public const TYPE_BRAKE = 'brake';
    public const TYPE_EMERGENCY = 'emergency';

    // ===================== PRIORITIES =====================
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    // ===================== RELATIONSHIPS =====================

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // ===================== SCOPES =====================

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeDue($query)
    {
        return $query->where('next_service_date', '<=', now()->addDays(7));
    }

    // ===================== METHODS =====================

    public static function generateMaintenanceNo(): string
    {
        $prefix = 'MNT';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%04d', $prefix, $year, $count);
    }

    public static function maintenanceTypes(): array
    {
        return [
            self::TYPE_ROUTINE => 'Routine Service',
            self::TYPE_ENGINE => 'Engine Service',
            self::TYPE_OIL_CHANGE => 'Oil Change',
            self::TYPE_TYRE => 'Tyre Replacement',
            self::TYPE_BATTERY => 'Battery Replacement',
            self::TYPE_BRAKE => 'Brake Service',
            self::TYPE_EMERGENCY => 'Emergency Repair',
        ];
    }

    public static function priorities(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_NORMAL => 'Normal',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function complete(string $workDone, float $cost = null): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'work_done' => $workDone,
            'cost' => $cost ?? $this->cost,
        ]);
    }

    public function isDue(): bool
    {
        return $this->next_service_date && $this->next_service_date <= now();
    }
}
