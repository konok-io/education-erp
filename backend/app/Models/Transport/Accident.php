<?php

declare(strict_types=1);

namespace App\Models\Transport;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Accident extends Model
{
    use HasUuid;

    protected $table = 'accidents';

    protected $fillable = [
        'uuid',
        'accident_no',
        'vehicle_id',
        'driver_id',
        'accident_date',
        'accident_time',
        'location',
        'description',
        'police_station',
        'fir_number',
        'casualties',
        'damage_cost',
        'insurance_claim',
        'claim_status',
        'status',
        'reported_by',
        'remarks',
    ];

    protected $casts = [
        'accident_date' => 'date',
        'accident_time' => 'datetime:H:i:s',
        'damage_cost' => 'decimal:2',
        'insurance_claim' => 'decimal:2',
        'casualties' => 'integer',
    ];

    // ===================== STATUS =====================
    public const STATUS_REPORTED = 'reported';
    public const STATUS_INVESTIGATION = 'investigation';
    public const STATUS_SETTLED = 'settled';
    public const STATUS_CLOSED = 'closed';

    // ===================== CLAIM STATUS =====================
    public const CLAIM_NONE = 'none';
    public const CLAIM_PENDING = 'pending';
    public const CLAIM_APPROVED = 'approved';
    public const CLAIM_REJECTED = 'rejected';
    public const CLAIM_SETTLED = 'settled';

    // ===================== RELATIONSHIPS =====================

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reported_by');
    }

    // ===================== SCOPES =====================

    public function scopeThisYear($query)
    {
        return $query->whereYear('accident_date', now()->year);
    }

    // ===================== METHODS =====================

    public static function generateAccidentNo(): string
    {
        $prefix = 'ACC';
        $year = now()->format('Y');
        $count = self::whereYear('accident_date', now()->year)->count() + 1;
        return sprintf('%s/%s/%04d', $prefix, $year, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_REPORTED => 'Reported',
            self::STATUS_INVESTIGATION => 'Under Investigation',
            self::STATUS_SETTLED => 'Settled',
            self::STATUS_CLOSED => 'Closed',
        ];
    }

    public static function claimStatuses(): array
    {
        return [
            self::CLAIM_NONE => 'No Claim',
            self::CLAIM_PENDING => 'Claim Pending',
            self::CLAIM_APPROVED => 'Claim Approved',
            self::CLAIM_REJECTED => 'Claim Rejected',
            self::CLAIM_SETTLED => 'Claim Settled',
        ];
    }

    public function close(): void
    {
        $this->update(['status' => self::STATUS_CLOSED]);
    }

    public function settle(): void
    {
        $this->update(['status' => self::STATUS_SETTLED]);
    }
}
