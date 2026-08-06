<?php

declare(strict_types=1);

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportRoute extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transport_routes';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'uuid',
        'route_code',
        'name',
        'name_bn',
        'distance',
        'distance_unit',
        'estimated_time',
        'vehicle_id',
        'driver_id',
        'status',
        'description',
    ];

    protected $casts = [
        'distance' => 'decimal:2',
        'estimated_time' => 'integer',
    ];

    public static function generateRouteCode(): string
    {
        $prefix = 'R';
        $last = self::orderBy('id', 'desc')->first();
        $sequence = $last ? ((int) substr($last->route_code, 1)) + 1 : 1;
        return sprintf('%s%03d', $prefix, $sequence);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(TransportVehicle::class, 'vehicle_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(TransportDriver::class, 'driver_id');
    }

    public function stops(): HasMany
    {
        return $this->hasMany(TransportStop::class, 'route_id')->orderBy('stop_order');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(TransportAllocation::class, 'route_id');
    }

    public function activeAllocations(): HasMany
    {
        return $this->allocations()->where('status', 'active');
    }
}
