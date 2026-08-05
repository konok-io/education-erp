<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetMaintenance extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'asset_maintenances';

    protected $fillable = [
        'uuid',
        'maintenance_no',
        'asset_id',
        'maintenance_type',
        'priority',
        'scheduled_date',
        'start_date',
        'completion_date',
        'vendor',
        'technician_name',
        'cost',
        'description',
        'work_done',
        'status',
        'created_by',
        'assigned_to',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'start_date' => 'date',
        'completion_date' => 'date',
        'cost' => 'decimal:2',
    ];

    // ===================== STATUS =====================
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== TYPES =====================
    public const TYPE_PREVENTIVE = 'preventive';
    public const TYPE_CORRECTIVE = 'corrective';
    public const TYPE_PREDICTIVE = 'predictive';

    // ===================== PRIORITIES =====================
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    // ===================== RELATIONSHIPS =====================

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    // ===================== SCOPES =====================

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
            ->where('scheduled_date', '<', now());
    }

    // ===================== METHODS =====================

    public static function generateMaintenanceNo(): string
    {
        $prefix = 'MNT';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%04d', $prefix, $year, $count);
    }

    public function start(): void
    {
        $this->update([
            'status' => self::STATUS_IN_PROGRESS,
            'start_date' => now(),
        ]);
    }

    public function complete(string $workDone): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completion_date' => now(),
            'work_done' => $workDone,
        ]);

        // Update asset condition if needed
        $this->asset->update(['status' => \App\Models\Inventory\Asset::STATUS_AVAILABLE]);
    }

    public function cancel(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }
}
