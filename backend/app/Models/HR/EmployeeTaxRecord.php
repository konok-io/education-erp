<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeTaxRecord extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'employee_tax_records';

    protected $fillable = [
        'uuid',
        'employee_id',
        'fiscal_year',
        'gross_salary',
        'exempted_allowances',
        'taxable_income',
        'annual_tax',
        'monthly_tax',
        'tax_paid',
        'adjustment',
        'remaining_tax',
        'status',
        'remarks',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'gross_salary' => 'decimal:2',
        'exempted_allowances' => 'decimal:2',
        'taxable_income' => 'decimal:2',
        'annual_tax' => 'decimal:2',
        'monthly_tax' => 'decimal:2',
        'tax_paid' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'remaining_tax' => 'decimal:2',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_CALCULATED = 'calculated';
    public const STATUS_ADJUSTED = 'adjusted';
    public const STATUS_PAID = 'paid';

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // ===================== METHODS =====================

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CALCULATED => 'Calculated',
            self::STATUS_ADJUSTED => 'Adjusted',
            self::STATUS_PAID => 'Paid',
        ];
    }

    public function calculateTax(): void
    {
        $this->taxable_income = $this->gross_salary - $this->exempted_allowances;
        $this->annual_tax = TaxSlab::calculateAnnualTax($this->taxable_income, $this->fiscal_year);
        $this->monthly_tax = $this->annual_tax / 12;
        $this->remaining_tax = $this->annual_tax - $this->tax_paid - $this->adjustment;
        $this->status = self::STATUS_CALCULATED;
        $this->save();
    }
}
