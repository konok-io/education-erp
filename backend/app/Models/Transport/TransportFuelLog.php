<?php

declare(strict_types=1);

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportFuelLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transport_fuel_logs';

    protected $fillable = [
        'uuid',
        'log_no',
        'vehicle_id',
        'date',
        'quantity',
        'fuel_type',
        'price_per_liter',
        'total_cost',
        'previous_reading',
        'current_reading',
        'mileage',
        'vendor',
        'invoice_no',
        'recorded_by',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'decimal:2',
        'price_per_liter' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'previous_reading' => 'decimal:2',
        'current_reading' => 'decimal:2',
        'mileage' => 'decimal:2',
    ];

    public static function generateLogNo(): string
    {
        $prefix = 'FL';
        $year = date('Y');
        $month = date('m');
        $last = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $last ? ((int) substr($last->log_no, -5)) + 1 : 1;
        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(TransportVehicle::class, 'vehicle_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by');
    }

    public function calculateMileage(): float
    {
        if (!$this->previous_reading || !$this->current_reading) {
            return 0;
        }
        $distance = $this->current_reading - $this->previous_reading;
        return $distance > 0 ? (float) ($this->quantity / $distance) : 0;
    }
}
