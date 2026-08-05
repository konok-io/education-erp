<?php

declare(strict_types=1);

namespace App\Http\Resources\HR;

use App\Http\Resources\BaseResource;

class OvertimeResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'overtime_date' => $this->overtime_date?->toDateString(),
            'hours' => $this->hours,
            'rate' => $this->rate,
            'amount' => $this->amount,
            'overtime_type' => $this->overtime_type,
            'reason' => $this->reason,
            'status' => $this->status,
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
