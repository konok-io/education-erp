<?php

declare(strict_types=1);

namespace App\Models\Transport;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelRecord extends Model
{
    use HasUuid;

    protected $table = 'fuel_records';

    protected $fillable = [
        'uuid',
        'fuel_no',
        'vehicle_id',
        'fuel_date',
        'fuel_type',
        'quantity',
        'price_per_liter',
        'total_cost',
        'odometer_reading',
        'fuel_station',
        'invoice_no',
        'created_by',
        'remarks',
    ];

    protected $casts = [
        'fuel_date' => 'date',
        'quantity' => 'decimal:2',
        'price_per_liter' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

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

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('fuel_date', now()->month)
                     ->whereYear('fuel_date', now()->year);
    }

    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    // ===================== METHODS =====================

    public static function generateFuelNo(): string
    {
        $prefix = 'FUEL';
        $date = now()->format('Ymd');
        $count = self::whereDate('fuel_date', now()->toDateString())->count() + 1;
        return sprintf('%s-%s-%02d', $prefix, $date, $count);
    }

    public static function fuelTypes(): array
    {
        return [
            'diesel' => 'Diesel',
            'petrol' => 'Petrol',
            'octane' => 'Octane',
            'cng' => 'CNG',
            'electric' => 'Electric',
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->fuel_no)) {
                $model->fuel_no = self::generateFuelNo();
            }
            if (empty($model->total_cost) && $model->quantity && $model->price_per_liter) {
                $model->total_cost = $model->quantity * $model->price_per_liter;
            }
        });
    }
}
