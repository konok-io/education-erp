<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Increment extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'increments';

    protected $fillable = [
        'uuid',
        'increment_no',
        'employee_id',
        'increment_type',
        'previous_basic',
        'new_basic',
        'increment_amount',
        'percentage',
        'effective_date',
        'previous_grade_id',
        'new_grade_id',
        'status',
        'approved_by',
        'approved_at',
        'reason',
        'remarks',
    ];

    protected $casts = [
        'previous_basic' => 'decimal:2',
        'new_basic' => 'decimal:2',
        'increment_amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'effective_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // ===================== TYPES =====================
    public const TYPE_ANNUAL = 'annual';
    public const TYPE_PERFORMANCE = 'performance';
    public const TYPE_PROMOTION = 'promotion';
    public const TYPE_MANUAL = 'manual';

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function previousGrade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class, 'previous_grade_id');
    }

    public function newGrade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class, 'new_grade_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // ===================== METHODS =====================

    public static function generateIncrementNo(): string
    {
        $prefix = 'INC';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%04d', $prefix, $year, $count);
    }

    public static function incrementTypes(): array
    {
        return [
            self::TYPE_ANNUAL => 'Annual Increment',
            self::TYPE_PERFORMANCE => 'Performance Increment',
            self::TYPE_PROMOTION => 'Promotion Increment',
            self::TYPE_MANUAL => 'Manual Increment',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }
}
