<?php

declare(strict_types=1);

namespace App\Http\Resources\HR;

use App\Http\Resources\BaseResource;

class LeaveTypeResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'name_bn' => $this->name_bn,
            'code' => $this->code,
            'short_code' => $this->short_code,
            'leave_days' => $this->leave_days,
            'is_paid' => $this->is_paid,
            'is_encashable' => $this->is_encashable,
            'is_carry_forward' => $this->is_carry_forward,
            'max_consecutive_days' => $this->max_consecutive_days,
            'max_carry_forward_days' => $this->max_carry_forward_days,
            'requires_approval' => $this->requires_approval,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
