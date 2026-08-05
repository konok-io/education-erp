<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Floor extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'floors';

    protected $fillable = [
        'uuid',
        'building_id',
        'floor_number',
        'floor_name',
        'total_rooms',
        'total_beds',
        'occupied_beds',
        'description',
        'status',
        'is_active',
    ];

    protected $casts = [
        'floor_number' => 'integer',
        'total_rooms' => 'integer',
        'total_beds' => 'integer',
        'occupied_beds' => 'integer',
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'floor_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_active', true);
    }

    // ===================== METHODS =====================

    public function getAvailableBedsAttribute(): int
    {
        return $this->total_beds - $this->occupied_beds;
    }
}
