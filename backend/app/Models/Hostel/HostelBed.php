<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostelBed extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hostel_beds';

    const STATUS_AVAILABLE = 'available';
    const STATUS_RESERVED = 'reserved';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_BLOCKED = 'blocked';

    protected $fillable = [
        'uuid',
        'bed_number',
        'room_id',
        'position',
        'status',
    ];

    public static function bedStatuses(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_RESERVED => 'Reserved',
            self::STATUS_OCCUPIED => 'Occupied',
            self::STATUS_MAINTENANCE => 'Maintenance',
            self::STATUS_BLOCKED => 'Blocked',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(HostelRoom::class, 'room_id');
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function allocate(): void
    {
        $this->update([
            'status' => self::STATUS_OCCUPIED,
        ]);
        $this->room->increment('current_occupancy');
    }

    public function vacate(): void
    {
        $this->update([
            'status' => self::STATUS_AVAILABLE,
        ]);
        $this->room->decrement('current_occupancy');
    }
}
