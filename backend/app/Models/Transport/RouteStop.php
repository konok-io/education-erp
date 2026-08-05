<?php

declare(strict_types=1);

namespace App\Models\Transport;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteStop extends Model
{
    use HasUuid;

    protected $table = 'route_stops';

    protected $fillable = [
        'uuid',
        'route_id',
        'stop_name',
        'latitude',
        'longitude',
        'address',
        'arrival_time',
        'departure_time',
        'sequence',
        'distance_from_school',
        'monthly_fee',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'distance_from_school' => 'decimal:2',
        'monthly_fee' => 'decimal:2',
        'sequence' => 'integer',
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function pickupAssignments()
    {
        return $this->hasMany(TransportAssignment::class, 'pickup_stop_id');
    }

    public function dropAssignments()
    {
        return $this->hasMany(TransportAssignment::class, 'drop_stop_id');
    }
}
