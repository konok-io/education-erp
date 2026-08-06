<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HostelFloor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hostel_floors';

    protected $fillable = [
        'uuid',
        'building_id',
        'floor_number',
        'floor_name',
        'total_rooms',
        'description',
        'is_active',
    ];

    protected $casts = [
        'floor_number' => 'integer',
        'total_rooms' => 'integer',
        'is_active' => 'boolean',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(HostelBuilding::class, 'building_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(HostelRoom::class, 'floor_id');
    }
}
