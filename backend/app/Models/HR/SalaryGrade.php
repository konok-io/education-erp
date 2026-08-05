<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryGrade extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'salary_grades';

    protected $fillable = [
        'uuid',
        'grade_name',
        'basic_salary',
        'house_rent_percent',
        'medical_percent',
        'transport_percent',
        'mobile_allowance',
        'special_allowance',
        'other_allowance',
        'provident_fund_percent',
        'tax_percent',
        'is_active',
        'description',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'house_rent_percent' => 'decimal:2',
        'medical_percent' => 'decimal:2',
        'transport_percent' => 'decimal:2',
        'mobile_allowance' => 'decimal:2',
        'special_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
        'provident_fund_percent' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(\App\Models\Employee\Employee::class, 'salary_grade_id');
    }

    public function salaryStructures(): HasMany
    {
        return $this->hasMany(SalaryStructure::class, 'grade_id');
    }

    public function calculateGrossSalary(): float
    {
        $houseRent = $this->basic_salary * ($this->house_rent_percent / 100);
        $medical = $this->basic_salary * ($this->medical_percent / 100);
        $transport = $this->basic_salary * ($this->transport_percent / 100);

        return $this->basic_salary + $houseRent + $medical + $transport 
            + $this->mobile_allowance + $this->special_allowance + $this->other_allowance;
    }

    public function calculateDeductions(): array
    {
        $gross = $this->calculateGrossSalary();
        
        return [
            'provident_fund' => $gross * ($this->provident_fund_percent / 100),
            'tax' => $gross * ($this->tax_percent / 100),
        ];
    }

    public function calculateNetSalary(): float
    {
        $gross = $this->calculateGrossSalary();
        $deductions = $this->calculateDeductions();
        
        return $gross - array_sum($deductions);
    }
}
