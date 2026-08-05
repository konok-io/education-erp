<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\HR\SalaryGrade;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryStructure extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'salary_structures';

    protected $fillable = [
        'uuid',
        'grade_id',
        'employee_id',
        'component_type',
        'component_name',
        'amount',
        'is_allowance',
        'is_taxable',
        'effective_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_allowance' => 'boolean',
        'is_taxable' => 'boolean',
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Component Types
    public const COMPONENT_BASIC = 'basic';
    public const COMPONENT_HOUSE_RENT = 'house_rent';
    public const COMPONENT_MEDICAL = 'medical';
    public const COMPONENT_TRANSPORT = 'transport';
    public const COMPONENT_MOBILE = 'mobile';
    public const COMPONENT_SPECIAL = 'special';
    public const COMPONENT_OTHER = 'other';
    public const COMPONENT_OVERTIME = 'overtime';
    public const COMPONENT_BONUS = 'bonus';
    public const COMPONENT_PF = 'pf';
    public const COMPONENT_TAX = 'tax';
    public const COMPONENT_LOAN = 'loan';
    public const COMPONENT_ADVANCE = 'advance';

    public function grade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class, 'grade_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Employee\Employee::class, 'employee_id');
    }

    public static function componentTypes(): array
    {
        return [
            self::COMPONENT_BASIC => 'Basic Salary',
            self::COMPONENT_HOUSE_RENT => 'House Rent',
            self::COMPONENT_MEDICAL => 'Medical Allowance',
            self::COMPONENT_TRANSPORT => 'Transport Allowance',
            self::COMPONENT_MOBILE => 'Mobile Allowance',
            self::COMPONENT_SPECIAL => 'Special Allowance',
            self::COMPONENT_OTHER => 'Other Allowance',
            self::COMPONENT_OVERTIME => 'Overtime',
            self::COMPONENT_BONUS => 'Bonus',
            self::COMPONENT_PF => 'Provident Fund',
            self::COMPONENT_TAX => 'Tax',
            self::COMPONENT_LOAN => 'Loan Deduction',
            self::COMPONENT_ADVANCE => 'Advance Deduction',
        ];
    }
}
