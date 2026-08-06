<?php

declare(strict_types=1);

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportStop extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transport_stops';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'route_id',
        'address',
        'latitude',
        'longitude',
        'pickup_time',
        'drop_time',
        'extra_fee',
        'stop_order',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'extra_fee' => 'decimal:2',
        'stop_order' => 'integer',
        'pickup_time' => 'datetime',
        'drop_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }

    public function pickupAllocations(): HasMany
    {
        return $this->hasMany(TransportAllocation::class, 'pickup_stop_id');
    }

    public function dropAllocations(): HasMany
    {
        return $this->hasMany(TransportAllocation::class, 'drop_stop_id');
    }
}
