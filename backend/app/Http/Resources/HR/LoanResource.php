<?php

declare(strict_types=1);

namespace App\Http\Resources\HR;

use App\Http\Resources\BaseResource;

class LoanResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'loan_no' => $this->loan_no,
            'loan_type' => $this->loan_type,
            'principal_amount' => $this->principal_amount,
            'interest_rate' => $this->interest_rate,
            'total_amount' => $this->total_amount,
            'monthly_installment' => $this->monthly_installment,
            'installment_count' => $this->installment_count,
            'paid_installments' => $this->paid_installments,
            'remaining_amount' => $this->remaining_amount,
            'loan_date' => $this->loan_date?->toDateString(),
            'status' => $this->status,
            'purpose' => $this->purpose,
            'approved_at' => $this->approved_at?->toIso8601String(),

            'employee' => $this->whenLoaded('employee', fn() => [
                'id' => $this->employee?->uuid,
                'employee_no' => $this->employee?->employee_no,
                'name' => $this->employee?->profile?->full_name,
            ]),

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
