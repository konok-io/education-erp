<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeRecord extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'overtime_records';

    protected $fillable = [
        'uuid',
        'employee_id',
        'overtime_date',
        'hours',
        'rate',
        'amount',
        'overtime_type',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'processed_by',
        'processed_at',
        'remarks',
    ];

    protected $casts = [
        'overtime_date' => 'date',
        'hours' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    // ===================== TYPES =====================
    public const TYPE_NORMAL = 'normal';
    public const TYPE_WEEKEND = 'weekend';
    public const TYPE_HOLIDAY = 'holiday';
    public const TYPE_NIGHT = 'night';

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_REJECTED = 'rejected';

    // ===================== RATES =====================
    public const RATE_NORMAL = 1.5;
    public const RATE_WEEKEND = 2.0;
    public const RATE_HOLIDAY = 2.5;
    public const RATE_NIGHT = 1.75;

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'processed_by');
    }

    // ===================== METHODS =====================

    public static function overtimeTypes(): array
    {
        return [
            self::TYPE_NORMAL => 'Normal Overtime',
            self::TYPE_WEEKEND => 'Weekend Overtime',
            self::TYPE_HOLIDAY => 'Holiday Overtime',
            self::TYPE_NIGHT => 'Night Shift',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PROCESSED => 'Processed',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public static function getRateForType(string $type): float
    {
        return match ($type) {
            self::TYPE_WEEKEND => self::RATE_WEEKEND,
            self::TYPE_HOLIDAY => self::RATE_HOLIDAY,
            self::TYPE_NIGHT => self::RATE_NIGHT,
            default => self::RATE_NORMAL,
        };
    }

    public function calculateAmount(float $hourlyRate): float
    {
        $rate = $this->rate ?? self::getRateForType($this->overtime_type);
        return $this->hours * $hourlyRate * $rate;
    }
}
