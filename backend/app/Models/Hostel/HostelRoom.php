<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HostelRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hostel_rooms';

    const TYPE_SINGLE = 'single';
    const TYPE_DOUBLE = 'double';
    const TYPE_TRIPLE = 'triple';
    const TYPE_FOUR_SEAT = 'four_seat';
    const TYPE_DORMITORY = 'dormitory';
    const TYPE_VIP = 'vip';
    const TYPE_GUEST = 'guest';

    protected $fillable = [
        'uuid',
        'room_number',
        'building_id',
        'floor',
        'room_type',
        'capacity',
        'current_occupancy',
        'rent',
        'description',
        'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'current_occupancy' => 'integer',
        'rent' => 'decimal:2',
    ];

    public static function roomTypes(): array
    {
        return [
            self::TYPE_SINGLE => 'Single',
            self::TYPE_DOUBLE => 'Double',
            self::TYPE_TRIPLE => 'Triple',
            self::TYPE_FOUR_SEAT => 'Four Seat',
            self::TYPE_DORMITORY => 'Dormitory',
            self::TYPE_VIP => 'VIP',
            self::TYPE_GUEST => 'Guest Room',
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(HostelBuilding::class, 'building_id');
    }

    public function beds(): HasMany
    {
        return $this->hasMany(HostelBed::class, 'room_id');
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(HostelAdmission::class, 'room_id');
    }

    public function availableBeds(): HasMany
    {
        return $this->beds()->where('status', 'available');
    }

    public function isFull(): bool
    {
        return $this->current_occupancy >= $this->capacity;
    }
}
