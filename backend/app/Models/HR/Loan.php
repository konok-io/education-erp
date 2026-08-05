<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'loans';

    protected $fillable = [
        'uuid',
        'loan_no',
        'employee_id',
        'loan_type',
        'principal_amount',
        'interest_rate',
        'total_interest',
        'total_amount',
        'monthly_installment',
        'installment_count',
        'paid_installments',
        'remaining_amount',
        'loan_date',
        'start_date',
        'end_date',
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
        'principal_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'total_interest' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'monthly_installment' => 'decimal:2',
        'paid_installments' => 'integer',
        'remaining_amount' => 'decimal:2',
        'loan_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // ===================== TYPES =====================
    public const TYPE_PERSONAL = 'personal';
    public const TYPE_HOUSE = 'house';
    public const TYPE_VEHICLE = 'vehicle';
    public const TYPE_EMERGENCY = 'emergency';
    public const TYPE_FESTIVAL = 'festival';

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

    public function repayments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class, 'loan_id');
    }

    // ===================== METHODS =====================

    public static function generateLoanNo(): string
    {
        $prefix = 'LN';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%04d', $prefix, $year, $count);
    }

    public static function loanTypes(): array
    {
        return [
            self::TYPE_PERSONAL => 'Personal Loan',
            self::TYPE_HOUSE => 'House Building Loan',
            self::TYPE_VEHICLE => 'Vehicle Loan',
            self::TYPE_EMERGENCY => 'Emergency Loan',
            self::TYPE_FESTIVAL => 'Festival Loan',
        ];
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
        $paid = $this->repayments()->where('status', 'paid')->sum('amount');
        return (float) ($this->total_amount - $paid);
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
