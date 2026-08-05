<?php

declare(strict_types=1);

namespace App\Http\Resources\HR;

use App\Http\Resources\BaseResource;

class SalaryGradeResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'grade_name' => $this->grade_name,
            'basic_salary' => $this->basic_salary,
            'house_rent_percent' => $this->house_rent_percent,
            'medical_percent' => $this->medical_percent,
            'transport_percent' => $this->transport_percent,
            'mobile_allowance' => $this->mobile_allowance,
            'special_allowance' => $this->special_allowance,
            'other_allowance' => $this->other_allowance,
            'provident_fund_percent' => $this->provident_fund_percent,
            'tax_percent' => $this->tax_percent,
            'is_active' => $this->is_active,
            'gross_salary' => $this->calculateGrossSalary(),
            'net_salary' => $this->calculateNetSalary(),
            'description' => $this->description,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
