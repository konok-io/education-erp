<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GatePass extends Model
{
    use HasUuid;

    protected $table = 'gate_passes';

    protected $fillable = [
        'uuid',
        'pass_no',
        'passable_type',
        'passable_id',
        'pass_type',
        'hostel_id',
        'issue_date',
        'valid_from',
        'valid_until',
        'exit_time',
        'return_time',
        'destination',
        'reason',
        'guardian_name',
        'guardian_phone',
        'remarks',
        'status',
        'issued_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'exit_time' => 'datetime:H:i',
        'return_time' => 'datetime:H:i',
        'approved_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_USED = 'used';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== TYPES =====================
    public const TYPE_LEAVE = 'leave';
    public const TYPE_TEMPORARY = 'temporary';
    public const TYPE_MEDICAL = 'medical';
    public const TYPE_OFFICIAL = 'official';
    public const TYPE_EMERGENCY = 'emergency';

    // ===================== RELATIONSHIPS =====================

    public function passable(): MorphTo
    {
        return $this->morphTo();
    }

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'issued_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', 'approved')
            ->where('valid_until', '>=', now()->toDateString());
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // ===================== METHODS =====================

    public static function generatePassNo(): string
    {
        $prefix = 'GP';
        $date = now()->format('Ymd');
        $count = self::whereDate('issue_date', now()->toDateString())->count() + 1;
        return sprintf('%s-%s-%04d', $prefix, $date, $count);
    }

    public static function passTypes(): array
    {
        return [
            self::TYPE_LEAVE => 'Leave',
            self::TYPE_TEMPORARY => 'Temporary Exit',
            self::TYPE_MEDICAL => 'Medical Leave',
            self::TYPE_OFFICIAL => 'Official Work',
            self::TYPE_EMERGENCY => 'Emergency Exit',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_USED => 'Used',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function isValid(): bool
    {
        return $this->status === self::STATUS_APPROVED &&
               $this->valid_until >= now()->toDateString();
    }
}
