<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeExit extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'employee_exits';

    protected $fillable = [
        'uuid',
        'exit_no',
        'employee_id',
        'exit_type',
        'notice_date',
        'last_working_date',
        'salary_amount',
        'bonus_amount',
        'leave_encashment',
        'pf_balance',
        'gratuity',
        'tax_deduction',
        'loan_adjustment',
        'advance_adjustment',
        'other_deduction',
        'net_payable',
        'status',
        'approved_by',
        'approved_at',
        'processed_by',
        'processed_at',
        'paid_at',
        'reason',
        'remarks',
    ];

    protected $casts = [
        'salary_amount' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'leave_encashment' => 'decimal:2',
        'pf_balance' => 'decimal:2',
        'gratuity' => 'decimal:2',
        'tax_deduction' => 'decimal:2',
        'loan_adjustment' => 'decimal:2',
        'advance_adjustment' => 'decimal:2',
        'other_deduction' => 'decimal:2',
        'net_payable' => 'decimal:2',
        'notice_date' => 'date',
        'last_working_date' => 'date',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // ===================== TYPES =====================
    public const TYPE_RESIGNATION = 'resignation';
    public const TYPE_TERMINATION = 'termination';
    public const TYPE_RETIREMENT = 'retirement';
    public const TYPE_DEATH = 'death';

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_COMPLETED = 'completed';

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

    public static function generateExitNo(): string
    {
        $prefix = 'EXT';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%04d', $prefix, $year, $count);
    }

    public static function exitTypes(): array
    {
        return [
            self::TYPE_RESIGNATION => 'Resignation',
            self::TYPE_TERMINATION => 'Termination',
            self::TYPE_RETIREMENT => 'Retirement',
            self::TYPE_DEATH => 'Death',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PROCESSED => 'Processed',
            self::STATUS_COMPLETED => 'Completed',
        ];
    }

    public function calculateNetPayable(): float
    {
        return $this->salary_amount 
            + $this->bonus_amount 
            + $this->leave_encashment 
            + $this->pf_balance 
            + $this->gratuity
            - $this->tax_deduction 
            - $this->loan_adjustment 
            - $this->advance_adjustment 
            - $this->other_deduction;
    }
}
