<?php

declare(strict_types=1);

namespace App\Models\Transport;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'vehicles';

    protected $fillable = [
        'uuid',
        'vehicle_number',
        'registration_number',
        'vehicle_type',
        'brand',
        'model',
        'manufacture_year',
        'color',
        'engine_number',
        'chassis_number',
        'seat_capacity',
        'purchase_date',
        'purchase_cost',
        'fuel_type',
        'tank_capacity',
        'current_odometer',
        'status',
        'image',
        'description',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'tank_capacity' => 'decimal:2',
        'seat_capacity' => 'integer',
        'manufacture_year' => 'integer',
        'is_active' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_DISPOSED = 'disposed';
    public const STATUS_ACCIDENT = 'accident';

    // ===================== VEHICLE TYPES =====================
    public const TYPE_BUS = 'bus';
    public const TYPE_MINI_BUS = 'mini_bus';
    public const TYPE_MICRO_BUS = 'micro_bus';
    public const TYPE_VAN = 'van';
    public const TYPE_CAR = 'car';
    public const TYPE_PICKUP = 'pickup';
    public const TYPE_AMBULANCE = 'ambulance';
    public const TYPE_MOTORCYCLE = 'motorcycle';

    // ===================== FUEL TYPES =====================
    public const FUEL_DIESEL = 'diesel';
    public const FUEL_PETROL = 'petrol';
    public const FUEL_OCTANE = 'octane';
    public const FUEL_CNG = 'cng';
    public const FUEL_ELECTRIC = 'electric';

    // ===================== RELATIONSHIPS =====================

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'vehicle_id');
    }

    public function fuelRecords(): HasMany
    {
        return $this->hasMany(FuelRecord::class, 'vehicle_id');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(VehicleMaintenance::class, 'vehicle_id');
    }

    public function insurances(): HasMany
    {
        return $this->hasMany(VehicleInsurance::class, 'vehicle_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VehicleDocument::class, 'vehicle_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TransportAssignment::class, 'vehicle_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)->where('is_active', true);
    }

    // ===================== METHODS =====================

    public static function vehicleTypes(): array
    {
        return [
            self::TYPE_BUS => 'School Bus',
            self::TYPE_MINI_BUS => 'Mini Bus',
            self::TYPE_MICRO_BUS => 'Micro Bus',
            self::TYPE_VAN => 'Van',
            self::TYPE_CAR => 'Car',
            self::TYPE_PICKUP => 'Pickup',
            self::TYPE_AMBULANCE => 'Ambulance',
            self::TYPE_MOTORCYCLE => 'Motorcycle',
        ];
    }

    public static function fuelTypes(): array
    {
        return [
            self::FUEL_DIESEL => 'Diesel',
            self::FUEL_PETROL => 'Petrol',
            self::FUEL_OCTANE => 'Octane',
            self::FUEL_CNG => 'CNG',
            self::FUEL_ELECTRIC => 'Electric',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_MAINTENANCE => 'Under Maintenance',
            self::STATUS_RESERVED => 'Reserved',
            self::STATUS_DISPOSED => 'Disposed',
            self::STATUS_ACCIDENT => 'Accident',
        ];
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_ACTIVE && $this->is_active;
    }

    public function getCurrentInsurance(): ?VehicleInsurance
    {
        return $this->insurances()
            ->where('expiry_date', '>=', now())
            ->where('status', 'active')
            ->latest('expiry_date')
            ->first();
    }

    public function getMaintenanceDue(): bool
    {
        return $this->maintenances()
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->exists();
    }
}
