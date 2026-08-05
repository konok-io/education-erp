<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdvanceSalary extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'advance_salaries';

    protected $fillable = [
        'uuid',
        'advance_no',
        'employee_id',
        'requested_amount',
        'approved_amount',
        'monthly_deduction',
        'installment_count',
        'paid_installments',
        'remaining_amount',
        'request_date',
        'deduction_start_date',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'purpose',
        'remarks',
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'monthly_deduction' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'request_date' => 'date',
        'deduction_start_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REJECTED = 'rejected';
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

    public static function generateAdvanceNo(): string
    {
        $prefix = 'ADV';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%04d', $prefix, $year, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function calculateRemainingAmount(): float
    {
        return (float) ($this->remaining_amount ?? $this->approved_amount);
    }

    public function updateStatus(): void
    {
        $remaining = $this->calculateRemainingAmount();
        
        if ($remaining <= 0) {
            $this->update(['status' => self::STATUS_COMPLETED]);
        } elseif ($this->status === self::STATUS_APPROVED) {
            $this->update(['status' => self::STATUS_ACTIVE]);
        }
    }
}
