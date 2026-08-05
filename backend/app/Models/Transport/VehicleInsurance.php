<?php

declare(strict_types=1);

namespace App\Models\Transport;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleInsurance extends Model
{
    use HasUuid;

    protected $table = 'vehicle_insurances';

    protected $fillable = [
        'uuid',
        'vehicle_id',
        'policy_number',
        'insurance_type',
        'company_name',
        'start_date',
        'expiry_date',
        'premium_amount',
        'coverage_amount',
        'agent_name',
        'agent_phone',
        'document',
        'status',
        'remarks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
        'premium_amount' => 'decimal:2',
        'coverage_amount' => 'decimal:2',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_RENEWED = 'renewed';

    // ===================== INSURANCE TYPES =====================
    public const TYPE_COMPREHENSIVE = 'comprehensive';
    public const TYPE_THIRD_PARTY = 'third_party';
    public const TYPE_FIRE_THEFT = 'fire_theft';

    // ===================== RELATIONSHIPS =====================

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('expiry_date', '>=', now());
    }

    public function scopeExpiring($query, $days = 30)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }

    // ===================== METHODS =====================

    public static function insuranceTypes(): array
    {
        return [
            self::TYPE_COMPREHENSIVE => 'Comprehensive',
            self::TYPE_THIRD_PARTY => 'Third Party',
            self::TYPE_FIRE_THEFT => 'Fire & Theft',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_RENEWED => 'Renewed',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expiry_date < now();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date && 
               $this->expiry_date >= now() && 
               $this->expiry_date <= now()->addDays($days);
    }

    public function daysUntilExpiry(): int
    {
        return $this->expiry_date ? max(0, now()->diffInDays($this->expiry_date, false)) : 0;
    }
}
