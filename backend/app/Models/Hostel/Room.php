<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'rooms';

    protected $fillable = [
        'uuid',
        'hostel_id',
        'building_id',
        'floor_id',
        'room_number',
        'room_code',
        'room_type',
        'floor_number',
        'capacity',
        'occupied',
        'monthly_fee',
        'security_deposit',
        'location',
        'description',
        'status',
        'is_active',
    ];

    protected $casts = [
        'floor_number' => 'integer',
        'capacity' => 'integer',
        'occupied' => 'integer',
        'monthly_fee' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_FULL = 'full';
    public const STATUS_MAINTENANCE = 'maintenance';

    // ===================== TYPES =====================
    public const TYPE_SINGLE = 'single';
    public const TYPE_DOUBLE = 'double';
    public const TYPE_TRIPLE = 'triple';
    public const TYPE_FOUR_SHARING = 'four_sharing';
    public const TYPE_DORMITORY = 'dormitory';
    public const TYPE_VIP = 'vip';
    public const TYPE_GUEST = 'guest';

    // ===================== RELATIONSHIPS =====================

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class, 'floor_id');
    }

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class, 'room_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(HostelAllocation::class, 'room_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'maintenance')->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->whereRaw('occupied < capacity')->where('status', '!=', 'maintenance');
    }

    // ===================== METHODS =====================

    public static function roomTypes(): array
    {
        return [
            self::TYPE_SINGLE => 'Single',
            self::TYPE_DOUBLE => 'Double',
            self::TYPE_TRIPLE => 'Triple',
            self::TYPE_FOUR_SHARING => 'Four Sharing',
            self::TYPE_DORMITORY => 'Dormitory',
            self::TYPE_VIP => 'VIP Room',
            self::TYPE_GUEST => 'Guest Room',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_PARTIAL => 'Partially Occupied',
            self::STATUS_FULL => 'Full',
            self::STATUS_MAINTENANCE => 'Under Maintenance',
        ];
    }

    public function getAvailableBedsAttribute(): int
    {
        return $this->capacity - $this->occupied;
    }

    public function isAvailable(): bool
    {
        return $this->occupied < $this->capacity && $this->status !== self::STATUS_MAINTENANCE;
    }

    public function updateOccupancy(): void
    {
        $this->occupied = $this->beds()->where('status', 'occupied')->count();
        $this->status = $this->occupied == 0 ? self::STATUS_AVAILABLE :
                        ($this->occupied >= $this->capacity ? self::STATUS_FULL : self::STATUS_PARTIAL);
        $this->save();
    }
}
