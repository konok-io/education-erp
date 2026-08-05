<?php

declare(strict_types=1);

namespace App\Http\Resources\HR;

use App\Http\Resources\BaseResource;

class AdvanceSalaryResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'advance_no' => $this->advance_no,
            'requested_amount' => $this->requested_amount,
            'approved_amount' => $this->approved_amount,
            'monthly_deduction' => $this->monthly_deduction,
            'installment_count' => $this->installment_count,
            'paid_installments' => $this->paid_installments,
            'remaining_amount' => $this->remaining_amount,
            'request_date' => $this->request_date?->toDateString(),
            'deduction_start_date' => $this->deduction_start_date?->toDateString(),
            'status' => $this->status,
            'approved_at' => $this->approved_at?->toIso8601String(),
            'rejected_at' => $this->rejected_at?->toIso8601String(),
            'rejection_reason' => $this->rejection_reason,
            'purpose' => $this->purpose,
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
