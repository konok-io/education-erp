<?php

declare(strict_types=1);

namespace App\Http\Resources\HR;

use App\Http\Resources\BaseResource;

class PayrollResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'payroll_no' => $this->payroll_no,
            'month' => $this->payroll_month,
            'year' => $this->payroll_year,
            'basic_salary' => $this->basic_salary,
            'gross_salary' => $this->gross_salary,
            'total_allowance' => $this->total_allowance,
            'total_deduction' => $this->total_deduction,
            'tax_amount' => $this->tax_amount,
            'pf_amount' => $this->pf_amount,
            'overtime_amount' => $this->overtime_amount,
            'bonus_amount' => $this->bonus_amount,
            'net_salary' => $this->net_salary,
            'status' => $this->status,
            'working_days' => $this->working_days,
            'present_days' => $this->present_days,
            'absent_days' => $this->absent_days,
            'paid_at' => $this->paid_at?->toIso8601String(),

            'employee' => $this->whenLoaded('employee', fn() => [
                'id' => $this->employee?->uuid,
                'employee_no' => $this->employee?->employee_no,
                'name' => $this->employee?->profile?->full_name,
                'department' => $this->employee?->department?->name,
                'designation' => $this->employee?->designation?->name,
            ]),

            'details' => $this->whenLoaded('details', fn() =>
                $this->details->map(fn($d) => [
                    'type' => $d->component_type,
                    'name' => $d->component_name,
                    'amount' => $d->amount,
                    'is_earning' => $d->is_earning,
                ])
            ),

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
