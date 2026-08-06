<?php

declare(strict_types=1);

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportDriver extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transport_drivers';

    const STATUS_ACTIVE = 'active';
    const STATUS_ON_LEAVE = 'on_leave';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_TERMINATED = 'terminated';

    protected $fillable = [
        'uuid',
        'driver_id',
        'name',
        'name_bn',
        'father_name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'present_address',
        'permanent_address',
        'license_no',
        'license_type',
        'license_expiry',
        'experience_years',
        'joining_date',
        'salary',
        'photo',
        'status',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'license_expiry' => 'date',
        'experience_years' => 'integer',
        'salary' => 'decimal:2',
    ];

    public function routes(): HasMany
    {
        return $this->hasMany(TransportRoute::class, 'driver_id');
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isLicenseValid(): bool
    {
        return $this->license_expiry && $this->license_expiry >= now();
    }

    public function isLicenseExpiringSoon(): bool
    {
        return $this->license_expiry && $this->license_expiry->diffInDays(now()) <= 30;
    }
}
