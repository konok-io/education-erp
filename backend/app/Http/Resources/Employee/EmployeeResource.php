<?php

declare(strict_types=1);

namespace App\Http\Resources\Employee;

use App\Http\Resources\BaseResource;

class EmployeeResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'employee_no' => $this->employee_no,
            'joining_date' => $this->joining_date?->toDateString(),
            'status' => $this->status,
            'remarks' => $this->remarks,

            'profile' => $this->whenLoaded('profile', fn() => new EmployeeProfileResource($this->profile)),

            'department' => $this->whenLoaded('department', fn() => [
                'id' => $this->department?->uuid,
                'name' => $this->department?->name,
            ]),

            'designation' => $this->whenLoaded('designation', fn() => [
                'id' => $this->designation?->uuid,
                'name' => $this->designation?->name,
            ]),

            'employment_type' => $this->whenLoaded('employmentType', fn() => [
                'id' => $this->employmentType?->uuid,
                'name' => $this->employmentType?->name,
            ]),

            'salary_grade' => $this->whenLoaded('salaryGrade', fn() => [
                'id' => $this->salaryGrade?->uuid,
                'name' => $this->salaryGrade?->grade_name,
            ]),

            'shift' => $this->whenLoaded('shift', fn() => [
                'id' => $this->shift?->uuid,
                'name' => $this->shift?->shift_name,
            ]),

            'documents' => $this->whenLoaded('documents'),
            'emergency_contacts' => $this->whenLoaded('emergencyContacts'),
            'salary' => $this->whenLoaded('salary'),

            'campus' => $this->whenLoaded('campus', fn() => [
                'id' => $this->campus?->uuid,
                'name' => $this->campus?->name,
            ]),

            'full_name' => $this->when(!$this->relationLoaded('profile'), fn() => $this->profile?->full_name),
            'photo_url' => $this->when(!$this->relationLoaded('profile'), fn() => $this->profile?->photo_url),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
