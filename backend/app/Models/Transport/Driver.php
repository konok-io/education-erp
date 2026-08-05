<?php

declare(strict_types=1);

namespace App\Models\Transport;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'drivers';

    protected $fillable = [
        'uuid',
        'driver_id',
        'photo',
        'full_name',
        'father_name',
        'mother_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'phone',
        'mobile',
        'email',
        'present_address',
        'permanent_address',
        'nid',
        'license_number',
        'license_type',
        'license_expiry',
        'joining_date',
        'emergency_contact',
        'emergency_phone',
        'status',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'license_expiry' => 'date',
        'joining_date' => 'date',
        'is_active' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_ON_LEAVE = 'on_leave';
    public const STATUS_SUSPENDED = 'suspended';

    // ===================== RELATIONSHIPS =====================

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TransportAssignment::class, 'driver_id');
    }

    public function accidents(): HasMany
    {
        return $this->hasMany(Accident::class, 'driver_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)->where('is_active', true);
    }

    public function scopeLicenseExpiring($query, $days = 30)
    {
        return $query->where('license_expiry', '<=', now()->addDays($days));
    }

    // ===================== METHODS =====================

    public static function generateDriverId(): string
    {
        $prefix = 'DRV';
        $year = now()->format('y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s%s%04d', $prefix, $year, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ON_LEAVE => 'On Leave',
            self::STATUS_SUSPENDED => 'Suspended',
        ];
    }

    public static function licenseTypes(): array
    {
        return [
            'general' => 'General',
            'professional' => 'Professional',
            'heavy' => 'Heavy Vehicle',
        ];
    }

    public function isLicenseExpiringSoon(int $days = 30): bool
    {
        return $this->license_expiry && $this->license_expiry->diffInDays(now()) <= $days;
    }

    public function isLicenseExpired(): bool
    {
        return $this->license_expiry && $this->license_expiry < now();
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_ACTIVE && $this->is_active && !$this->isLicenseExpired();
    }
}
