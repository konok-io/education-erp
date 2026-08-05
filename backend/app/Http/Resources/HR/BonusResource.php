<?php

declare(strict_types=1);

namespace App\Http\Resources\HR;

use App\Http\Resources\BaseResource;

class BonusResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'bonus_no' => $this->bonus_no,
            'bonus_type' => $this->bonus_type,
            'bonus_type_label' => $this->bonus_type ? \App\Models\HR\Bonus::bonusTypes()[$this->bonus_type] ?? $this->bonus_type : null,
            'name' => $this->name,
            'amount' => $this->amount,
            'percentage' => $this->percentage,
            'bonus_date' => $this->bonus_date?->toDateString(),
            'status' => $this->status,
            'approved_at' => $this->approved_at?->toIso8601String(),
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
