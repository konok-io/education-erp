<?php

declare(strict_types=1);

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportMaintenanceLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transport_maintenance_logs';

    const TYPE_OIL_CHANGE = 'oil_change';
    const TYPE_TYRE = 'tyre';
    const TYPE_BATTERY = 'battery';
    const TYPE_REPAIR = 'repair';
    const TYPE_SERVICE = 'service';
    const TYPE_INSPECTION = 'inspection';
    const TYPE_INSURANCE = 'insurance';
    const TYPE_FITNESS = 'fitness';
    const TYPE_OTHER = 'other';

    protected $fillable = [
        'uuid',
        'log_no',
        'vehicle_id',
        'date',
        'maintenance_type',
        'description',
        'cost',
        'vendor',
        'invoice_no',
        'next_due_date',
        'next_due_km',
        'recorded_by',
        'approved_by',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'cost' => 'decimal:2',
        'next_due_date' => 'date',
        'next_due_km' => 'integer',
    ];

    public static function generateLogNo(): string
    {
        $prefix = 'ML';
        $year = date('Y');
        $month = date('m');
        $last = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $last ? ((int) substr($last->log_no, -5)) + 1 : 1;
        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    public static function maintenanceTypes(): array
    {
        return [
            self::TYPE_OIL_CHANGE => 'Oil Change',
            self::TYPE_TYRE => 'Tyre',
            self::TYPE_BATTERY => 'Battery',
            self::TYPE_REPAIR => 'Repair',
            self::TYPE_SERVICE => 'Service',
            self::TYPE_INSPECTION => 'Inspection',
            self::TYPE_INSURANCE => 'Insurance',
            self::TYPE_FITNESS => 'Fitness',
            self::TYPE_OTHER => 'Other',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(TransportVehicle::class, 'vehicle_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
}
