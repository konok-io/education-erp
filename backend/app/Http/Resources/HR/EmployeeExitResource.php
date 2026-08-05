<?php

declare(strict_types=1);

namespace App\Http\Resources\HR;

use App\Http\Resources\BaseResource;

class EmployeeExitResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'exit_no' => $this->exit_no,
            'exit_type' => $this->exit_type,
            'exit_type_label' => $this->exit_type ? \App\Models\HR\EmployeeExit::exitTypes()[$this->exit_type] ?? $this->exit_type : null,
            'notice_date' => $this->notice_date?->toDateString(),
            'last_working_date' => $this->last_working_date?->toDateString(),
            'salary_amount' => $this->salary_amount,
            'bonus_amount' => $this->bonus_amount,
            'leave_encashment' => $this->leave_encashment,
            'pf_balance' => $this->pf_balance,
            'gratuity' => $this->gratuity,
            'tax_deduction' => $this->tax_deduction,
            'loan_adjustment' => $this->loan_adjustment,
            'advance_adjustment' => $this->advance_adjustment,
            'other_deduction' => $this->other_deduction,
            'net_payable' => $this->net_payable,
            'status' => $this->status,
            'approved_at' => $this->approved_at?->toIso8601String(),
            'processed_at' => $this->processed_at?->toIso8601String(),
            'paid_at' => $this->paid_at?->toIso8601String(),
            'reason' => $this->reason,
            'remarks' => $this->remarks,
            
            'employee' => $this->whenLoaded('employee', fn() => [
                'id' => $this->employee?->uuid,
                'employee_no' => $this->employee?->employee_no,
                'name' => $this->employee?->profile?->full_name,
            ]),
            
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
