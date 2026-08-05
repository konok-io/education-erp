<?php

declare(strict_types=1);

namespace App\Http\Resources\Student;

use App\Http\Resources\BaseResource;

class StudentMedicalResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'height' => $this->height,
            'weight' => $this->weight,
            'blood_group' => $this->blood_group,
            'allergy' => $this->allergy,
            'allergy_details' => $this->allergy_details,
            'chronic_disease' => $this->chronic_disease,
            'chronic_disease_details' => $this->chronic_disease_details,
            'disability' => $this->disability,
            'disability_details' => $this->disability_details,
            'medication' => $this->medication,
            'medical_note' => $this->medical_note,
            'last_checkup_date' => $this->last_checkup_date?->toDateString(),
            'doctor_name' => $this->doctor_name,
            'doctor_phone' => $this->doctor_phone,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
