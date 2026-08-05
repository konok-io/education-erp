<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'buildings';

    protected $fillable = [
        'uuid',
        'hostel_id',
        'building_name',
        'building_code',
        'campus',
        'address',
        'total_floors',
        'total_rooms',
        'total_beds',
        'occupied_beds',
        'description',
        'status',
        'is_active',
    ];

    protected $casts = [
        'total_floors' => 'integer',
        'total_rooms' => 'integer',
        'total_beds' => 'integer',
        'occupied_beds' => 'integer',
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class, 'building_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'building_id');
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
