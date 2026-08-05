<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxSlab extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'tax_slabs';

    protected $fillable = [
        'uuid',
        'name',
        'fiscal_year',
        'min_income',
        'max_income',
        'rate_percent',
        'fixed_amount',
        'is_active',
        'description',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'min_income' => 'decimal:2',
        'max_income' => 'decimal:2',
        'rate_percent' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function taxRecords(): HasMany
    {
        return $this->hasMany(EmployeeTaxRecord::class, 'fiscal_year', 'fiscal_year');
    }

    // ===================== METHODS =====================

    public function calculateTax(float $income): float
    {
        if ($income < $this->min_income) {
            return 0;
        }

        $taxableAmount = $this->max_income 
            ? min($income, $this->max_income) - $this->min_income 
            : $income - $this->min_income;

        return $this->fixed_amount + ($taxableAmount * $this->rate_percent / 100);
    }

    public static function getActiveSlabs(int $fiscalYear): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('fiscal_year', $fiscalYear)
            ->where('is_active', true)
            ->orderBy('min_income', 'asc')
            ->get();
    }

    public static function calculateAnnualTax(float $annualIncome, int $fiscalYear): float
    {
        $slabs = self::getActiveSlabs($fiscalYear);
        $totalTax = 0;
        $remainingIncome = $annualIncome;

        foreach ($slabs as $slab) {
            if ($remainingIncome <= 0) {
                break;
            }

            $taxableInSlab = $slab->max_income 
                ? min($remainingIncome, $slab->max_income - $slab->min_income)
                : $remainingIncome;

            if ($taxableInSlab > 0) {
                $totalTax += $slab->calculateTax(min($remainingIncome, $slab->min_income + $taxableInSlab)) 
                    - $slab->calculateTax($slab->min_income);
                $remainingIncome -= $taxableInSlab;
            }
        }

        return $totalTax;
    }
}
