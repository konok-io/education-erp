<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'payrolls';

    protected $fillable = [
        'uuid',
        'payroll_no',
        'employee_id',
        'payroll_month',
        'payroll_year',
        'basic_salary',
        'gross_salary',
        'total_allowance',
        'total_deduction',
        'tax_amount',
        'pf_amount',
        'loan_deduction',
        'advance_deduction',
        'overtime_amount',
        'bonus_amount',
        'net_salary',
        'working_days',
        'present_days',
        'absent_days',
        'late_days',
        'status',
        'processed_by',
        'processed_at',
        'approved_by',
        'approved_at',
        'paid_at',
        'accounting_entry_id',
        'remarks',
    ];

    protected $casts = [
        'payroll_month' => 'integer',
        'payroll_year' => 'integer',
        'basic_salary' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'total_allowance' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'pf_amount' => 'decimal:2',
        'loan_deduction' => 'decimal:2',
        'advance_deduction' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'working_days' => 'integer',
        'present_days' => 'integer',
        'absent_days' => 'integer',
        'late_days' => 'integer',
        'processed_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'processed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PayrollDetail::class, 'payroll_id');
    }

    // ===================== METHODS =====================

    public static function generatePayrollNo(int $month, int $year): string
    {
        $prefix = 'PR';
        $count = self::where('payroll_month', $month)
            ->where('payroll_year', $year)
            ->count() + 1;
        return sprintf('%s/%s/%s/%04d', $prefix, $year, str_pad((string) $month, 2, '0', STR_PAD_LEFT), $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PROCESSED => 'Processed',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PAID => 'Paid',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function calculateNetSalary(): float
    {
        return $this->gross_salary 
            + $this->overtime_amount 
            + $this->bonus_amount 
            - $this->total_deduction 
            - $this->tax_amount 
            - $this->pf_amount 
            - $this->loan_deduction 
            - $this->advance_deduction;
    }
}
