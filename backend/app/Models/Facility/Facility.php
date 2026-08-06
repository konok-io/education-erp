<?php

declare(strict_types=1);

namespace App\Models\Facility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'facilities';

    const STATUS_AVAILABLE = 'available';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_UNAVAILABLE = 'unavailable';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'facility_type_id',
        'code',
        'location',
        'capacity',
        'equipment',
        'available_from',
        'available_to',
        'description',
        'photo',
        'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'equipment' => 'array',
    ];

    public function facilityType(): BelongsTo
    {
        return $this->belongsTo(FacilityType::class, 'facility_type_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(FacilityBooking::class, 'facility_id');
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }
}
