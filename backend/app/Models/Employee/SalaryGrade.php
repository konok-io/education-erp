<?php

declare(strict_types=1);

namespace App\Models\Employee;

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
        'grade_code',
        'basic_salary',
        'house_rent',
        'medical_allowance',
        'transport_allowance',
        'special_allowance',
        'provident_fund_rate',
        'tax_percentage',
        'description',
        'status',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'house_rent' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'special_allowance' => 'decimal:2',
        'provident_fund_rate' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'salary_grade_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getGrossSalaryAttribute(): float
    {
        return (float) $this->basic_salary 
            + (float) $this->house_rent 
            + (float) $this->medical_allowance 
            + (float) $this->transport_allowance 
            + (float) $this->special_allowance;
    }
}
