<?php

declare(strict_types=1);

namespace App\Http\Resources\HR;

use App\Http\Resources\BaseResource;

class PromotionResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'promotion_no' => $this->promotion_no,
            'promotion_date' => $this->promotion_date?->toDateString(),
            'effective_date' => $this->effective_date?->toDateString(),
            'status' => $this->status,
            'approved_at' => $this->approved_at?->toIso8601String(),
            'reason' => $this->reason,
            'remarks' => $this->remarks,
            
            'employee' => $this->whenLoaded('employee', fn() => [
                'id' => $this->employee?->uuid,
                'employee_no' => $this->employee?->employee_no,
                'name' => $this->employee?->profile?->full_name,
            ]),
            
            'previous_department' => $this->whenLoaded('previousDepartment', fn() => [
                'id' => $this->previousDepartment?->uuid,
                'name' => $this->previousDepartment?->name,
            ]),
            
            'new_department' => $this->whenLoaded('newDepartment', fn() => [
                'id' => $this->newDepartment?->uuid,
                'name' => $this->newDepartment?->name,
            ]),
            
            'previous_designation' => $this->whenLoaded('previousDesignation', fn() => [
                'id' => $this->previousDesignation?->uuid,
                'name' => $this->previousDesignation?->name,
            ]),
            
            'new_designation' => $this->whenLoaded('newDesignation', fn() => [
                'id' => $this->newDesignation?->uuid,
                'name' => $this->newDesignation?->name,
            ]),
            
            'previous_grade' => $this->whenLoaded('previousGrade', fn() => [
                'id' => $this->previousGrade?->uuid,
                'name' => $this->previousGrade?->grade_name,
            ]),
            
            'new_grade' => $this->whenLoaded('newGrade', fn() => [
                'id' => $this->newGrade?->uuid,
                'name' => $this->newGrade?->grade_name,
            ]),
            
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
