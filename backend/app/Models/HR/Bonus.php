<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bonus extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'bonuses';

    protected $fillable = [
        'uuid',
        'bonus_no',
        'employee_id',
        'bonus_type',
        'name',
        'amount',
        'percentage',
        'bonus_date',
        'status',
        'approved_by',
        'approved_at',
        'paid_at',
        'reason',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'bonus_date' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // ===================== TYPES =====================
    public const TYPE_FESTIVAL = 'festival';
    public const TYPE_PERFORMANCE = 'performance';
    public const TYPE_YEARLY = 'yearly';
    public const TYPE_SPECIAL = 'special';

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // ===================== METHODS =====================

    public static function generateBonusNo(): string
    {
        $prefix = 'BN';
        $year = now()->format('Y');
        $month = now()->format('m');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $count);
    }

    public static function bonusTypes(): array
    {
        return [
            self::TYPE_FESTIVAL => 'Festival Bonus',
            self::TYPE_PERFORMANCE => 'Performance Bonus',
            self::TYPE_YEARLY => 'Yearly Bonus',
            self::TYPE_SPECIAL => 'Special Bonus',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PAID => 'Paid',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }
}
