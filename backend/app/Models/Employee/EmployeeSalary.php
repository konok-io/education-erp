<?php

declare(strict_types=1);

namespace App\Models\Employee;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSalary extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'employee_salary';

    protected $fillable = [
        'uuid',
        'employeeable_type',
        'employeeable_id',
        'basic_salary',
        'house_rent',
        'medical_allowance',
        'transport_allowance',
        'special_allowance',
        'gross_salary',
        'provident_fund',
        'tax_deduction',
        'other_deduction',
        'total_deduction',
        'net_salary',
        'effective_date',
        'end_date',
        'is_current',
        'payment_method',
        'bank_name',
        'account_number',
        'remarks',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'house_rent' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'special_allowance' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'provident_fund' => 'decimal:2',
        'tax_deduction' => 'decimal:2',
        'other_deduction' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function employeeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function calculateNetSalary(): void
    {
        $this->gross_salary = (float) $this->basic_salary 
            + (float) $this->house_rent 
            + (float) $this->medical_allowance 
            + (float) $this->transport_allowance 
            + (float) $this->special_allowance;

        $this->total_deduction = (float) $this->provident_fund 
            + (float) $this->tax_deduction 
            + (float) $this->other_deduction;

        $this->net_salary = $this->gross_salary - $this->total_deduction;
    }
}
