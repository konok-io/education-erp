<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProvidentFund extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'provident_funds';

    protected $fillable = [
        'uuid',
        'pf_no',
        'employee_id',
        'employee_contribution',
        'employer_contribution',
        'total_contribution',
        'interest_earned',
        'total_balance',
        'withdrawn_amount',
        'status',
        'activation_date',
        'closing_date',
        'remarks',
    ];

    protected $casts = [
        'employee_contribution' => 'decimal:2',
        'employer_contribution' => 'decimal:2',
        'total_contribution' => 'decimal:2',
        'interest_earned' => 'decimal:2',
        'total_balance' => 'decimal:2',
        'withdrawn_amount' => 'decimal:2',
        'activation_date' => 'date',
        'closing_date' => 'date',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_FROZEN = 'frozen';

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(PFContribution::class, 'pf_id');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(PFWithdrawal::class, 'pf_id');
    }

    // ===================== METHODS =====================

    public static function generatePFNo(): string
    {
        $prefix = 'PF';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%04d', $prefix, $year, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_FROZEN => 'Frozen',
        ];
    }

    public function calculateBalance(): float
    {
        return (float) ($this->total_contribution + $this->interest_earned - $this->withdrawn_amount);
    }

    public function addContribution(float $employeeAmount, float $employerAmount, float $interest = 0): void
    {
        $this->employee_contribution += $employeeAmount;
        $this->employer_contribution += $employerAmount;
        $this->total_contribution += ($employeeAmount + $employerAmount);
        $this->interest_earned += $interest;
        $this->total_balance = $this->calculateBalance();
        $this->save();
    }

    public function withdraw(float $amount): void
    {
        $this->withdrawn_amount += $amount;
        $this->total_balance = $this->calculateBalance();
        $this->save();
    }
}
