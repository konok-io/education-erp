<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hostel extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'hostels';

    protected $fillable = [
        'uuid',
        'hostel_name',
        'hostel_code',
        'hostel_type',
        'gender',
        'campus_id',
        'manager_name',
        'phone',
        'email',
        'address',
        'total_buildings',
        'total_rooms',
        'total_beds',
        'occupied_beds',
        'description',
        'notes',
        'status',
        'is_active',
    ];

    protected $casts = [
        'total_buildings' => 'integer',
        'total_rooms' => 'integer',
        'total_beds' => 'integer',
        'occupied_beds' => 'integer',
        'is_active' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    // ===================== TYPES =====================
    public const TYPE_BOYS = 'boys';
    public const TYPE_GIRLS = 'girls';
    public const TYPE_TEACHER = 'teacher';
    public const TYPE_GUEST = 'guest';
    public const TYPE_STAFF = 'staff';
    public const TYPE_RESEARCH = 'research';

    // ===================== RELATIONSHIPS =====================

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class, 'hostel_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'hostel_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(HostelAllocation::class, 'hostel_id');
    }

    public function visitors(): HasMany
    {
        return $this->hasMany(HostelVisitor::class, 'hostel_id');
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(HostelComplaint::class, 'hostel_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)->where('is_active', true);
    }

    // ===================== METHODS =====================

    public static function hostelTypes(): array
    {
        return [
            self::TYPE_BOYS => 'Boys Hostel',
            self::TYPE_GIRLS => 'Girls Hostel',
            self::TYPE_TEACHER => 'Teacher Hostel',
            self::TYPE_GUEST => 'Guest House',
            self::TYPE_STAFF => 'Staff Hostel',
            self::TYPE_RESEARCH => 'Research Hostel',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    public function getAvailableBedsAttribute(): int
    {
        return $this->total_beds - $this->occupied_beds;
    }

    public function getOccupancyRateAttribute(): float
    {
        if ($this->total_beds == 0) return 0;
        return round(($this->occupied_beds / $this->total_beds) * 100, 2);
    }

    public function updateStats(): void
    {
        $this->total_buildings = $this->buildings()->count();
        $this->total_rooms = $this->rooms()->count();
        $this->total_beds = $this->rooms()->sum('capacity');
        $this->occupied_beds = $this->allocations()->where('status', 'active')->count();
        $this->save();
    }
}
