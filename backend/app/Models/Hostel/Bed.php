<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Bed extends Model
{
    use HasUuid;

    protected $table = 'beds';

    protected $fillable = [
        'uuid',
        'room_id',
        'bed_number',
        'bed_code',
        'position',
        'status',
        'assignable_type',
        'assignable_id',
        'allocation_date',
        'checkout_date',
        'notes',
    ];

    protected $casts = [
        'allocation_date' => 'date',
        'checkout_date' => 'date',
    ];

    // ===================== STATUS =====================
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_OCCUPIED = 'occupied';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_BLOCKED = 'blocked';

    // ===================== POSITIONS =====================
    public const POSITION_TOP_LEFT = 'top_left';
    public const POSITION_TOP_RIGHT = 'top_right';
    public const POSITION_BOTTOM_LEFT = 'bottom_left';
    public const POSITION_BOTTOM_RIGHT = 'bottom_right';

    // ===================== RELATIONSHIPS =====================

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }

    // ===================== SCOPES =====================

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', self::STATUS_OCCUPIED);
    }

    // ===================== METHODS =====================

    public static function statuses(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_OCCUPIED => 'Occupied',
            self::STATUS_RESERVED => 'Reserved',
            self::STATUS_MAINTENANCE => 'Maintenance',
            self::STATUS_BLOCKED => 'Blocked',
        ];
    }

    public static function positions(): array
    {
        return [
            self::POSITION_TOP_LEFT => 'Top Left',
            self::POSITION_TOP_RIGHT => 'Top Right',
            self::POSITION_BOTTOM_LEFT => 'Bottom Left',
            self::POSITION_BOTTOM_RIGHT => 'Bottom Right',
        ];
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function allocate(string $type, int $id): void
    {
        $this->update([
            'status' => self::STATUS_OCCUPIED,
            'assignable_type' => $type,
            'assignable_id' => $id,
            'allocation_date' => now(),
            'checkout_date' => null,
        ]);
    }

    public function checkout(): void
    {
        $this->update([
            'status' => self::STATUS_AVAILABLE,
            'assignable_type' => null,
            'assignable_id' => null,
            'checkout_date' => now(),
        ]);
    }
}
