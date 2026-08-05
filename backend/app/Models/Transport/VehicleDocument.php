<?php

declare(strict_types=1);

namespace App\Models\Transport;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleDocument extends Model
{
    use HasUuid;

    protected $table = 'vehicle_documents';

    protected $fillable = [
        'uuid',
        'vehicle_id',
        'document_type',
        'document_number',
        'issue_date',
        'expiry_date',
        'document_file',
        'status',
        'remarks',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_RENEWED = 'renewed';

    // ===================== DOCUMENT TYPES =====================
    public const TYPE_REGISTRATION = 'registration';
    public const TYPE_FITNESS = 'fitness';
    public const TYPE_TAX_TOKEN = 'tax_token';
    public const TYPE_INSURANCE = 'insurance';
    public const TYPE_ROUTE_PERMIT = 'route_permit';
    public const TYPE_POLLUTION = 'pollution';
    public const TYPE_OTHER = 'other';

    // ===================== RELATIONSHIPS =====================

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    // ===================== SCOPES =====================

    public function scopeExpiring($query, $days = 30)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }

    // ===================== METHODS =====================

    public static function documentTypes(): array
    {
        return [
            self::TYPE_REGISTRATION => 'Registration',
            self::TYPE_FITNESS => 'Fitness Certificate',
            self::TYPE_TAX_TOKEN => 'Tax Token',
            self::TYPE_INSURANCE => 'Insurance',
            self::TYPE_ROUTE_PERMIT => 'Route Permit',
            self::TYPE_POLLUTION => 'Pollution Certificate',
            self::TYPE_OTHER => 'Other',
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
        return $this->expiry_date && $this->expiry_date < now();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date && 
               $this->expiry_date >= now() && 
               $this->expiry_date <= now()->addDays($days);
    }
}
