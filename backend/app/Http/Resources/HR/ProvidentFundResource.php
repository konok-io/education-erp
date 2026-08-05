<?php

declare(strict_types=1);

namespace App\Http\Resources\HR;

use App\Http\Resources\BaseResource;

class ProvidentFundResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'pf_no' => $this->pf_no,
            'employee_contribution' => $this->employee_contribution,
            'employer_contribution' => $this->employer_contribution,
            'total_contribution' => $this->total_contribution,
            'interest_earned' => $this->interest_earned,
            'total_balance' => $this->total_balance,
            'withdrawn_amount' => $this->withdrawn_amount,
            'status' => $this->status,
            'activation_date' => $this->activation_date?->toDateString(),
            'closing_date' => $this->closing_date?->toDateString(),
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
