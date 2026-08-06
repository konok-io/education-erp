<?php

declare(strict_types=1);

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportVehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transport_vehicles';

    const TYPE_BUS = 'bus';
    const TYPE_MINI_BUS = 'mini_bus';
    const TYPE_MICRO_BUS = 'micro_bus';
    const TYPE_VAN = 'van';
    const TYPE_CAR = 'car';
    const TYPE_AMBULANCE = 'ambulance';
    const TYPE_OTHER = 'other';

    const STATUS_ACTIVE = 'active';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_REPAIR = 'repair';
    const STATUS_DECOMMISSIONED = 'decommissioned';

    protected $fillable = [
        'uuid',
        'vehicle_number',
        'registration_no',
        'vehicle_type',
        'brand',
        'model',
        'capacity',
        'color',
        'chassis_no',
        'engine_no',
        'purchase_date',
        'purchase_price',
        'insurance_expiry',
        'tax_token',
        'fitness_expiry',
        'fuel_type',
        'avg_mileage',
        'status',
        'description',
        'photo',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'insurance_expiry' => 'date',
        'tax_token' => 'date',
        'fitness_expiry' => 'date',
        'avg_mileage' => 'decimal:2',
    ];

    public static function vehicleTypes(): array
    {
        return [
            self::TYPE_BUS => 'Bus',
            self::TYPE_MINI_BUS => 'Mini Bus',
            self::TYPE_MICRO_BUS => 'Micro Bus',
            self::TYPE_VAN => 'Van',
            self::TYPE_CAR => 'Car',
            self::TYPE_AMBULANCE => 'Ambulance',
            self::TYPE_OTHER => 'Other',
        ];
    }

    public function routes(): HasMany
    {
        return $this->hasMany(TransportRoute::class, 'vehicle_id');
    }

    public function drivers(): HasMany
    {
        return $this->hasMany(TransportRoute::class, 'driver_id');
    }

    public function fuelLogs(): HasMany
    {
        return $this->hasMany(TransportFuelLog::class, 'vehicle_id');
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(TransportMaintenanceLog::class, 'vehicle_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(TransportAllocation::class, 'vehicle_id');
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isInsuranceDue(): bool
    {
        return $this->insurance_expiry && $this->insurance_expiry->diffInDays(now()) <= 30;
    }

    public function isFitnessDue(): bool
    {
        return $this->fitness_expiry && $this->fitness_expiry->diffInDays(now()) <= 30;
    }
}
