<?php

declare(strict_types=1);

namespace App\Http\Resources\HR;

use App\Http\Resources\BaseResource;

class IncrementResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'increment_no' => $this->increment_no,
            'increment_type' => $this->increment_type,
            'increment_type_label' => $this->increment_type ? \App\Models\HR\Increment::incrementTypes()[$this->increment_type] ?? $this->increment_type : null,
            'previous_basic' => $this->previous_basic,
            'new_basic' => $this->new_basic,
            'increment_amount' => $this->increment_amount,
            'percentage' => $this->percentage,
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
