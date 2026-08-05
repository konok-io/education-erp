<?php

declare(strict_types=1);

namespace App\Http\Resources\HR;

use App\Http\Resources\BaseResource;

class LeaveResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'leave_no' => $this->leave_no,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'total_days' => $this->total_days,
            'reason' => $this->reason,
            'status' => $this->status,
            'applied_at' => $this->applied_at?->toIso8601String(),
            'approved_at' => $this->approved_at?->toIso8601String(),
            'rejection_reason' => $this->rejection_reason,

            'employee' => $this->whenLoaded('employee', fn() => [
                'id' => $this->employee?->uuid,
                'employee_no' => $this->employee?->employee_no,
                'name' => $this->employee?->profile?->full_name,
            ]),

            'leave_type' => $this->whenLoaded('leaveType', fn() => [
                'id' => $this->leaveType?->uuid,
                'name' => $this->leaveType?->name,
                'code' => $this->leaveType?->code,
            ]),

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
