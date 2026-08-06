<?php

declare(strict_types=1);

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryReadingRoomSeat extends Model
{
    use HasFactory;

    protected $table = 'library_reading_room_seats';

    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_GROUP = 'group';
    const TYPE_COMPUTER = 'computer';
    const TYPE_SILENT = 'silent';

    const STATUS_AVAILABLE = 'available';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_RESERVED = 'reserved';

    protected $fillable = [
        'uuid',
        'seat_no',
        'floor',
        'zone',
        'seat_type',
        'has_power',
        'has_lamp',
        'status',
        'description',
    ];

    protected $casts = [
        'has_power' => 'boolean',
        'has_lamp' => 'boolean',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(LibraryReadingRoomBooking::class, 'seat_id');
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }
}
