<?php

declare(strict_types=1);

namespace App\Http\Resources\Employee;

use App\Http\Resources\BaseResource;

class EmployeeProfileResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'first_name_bn' => $this->first_name_bn,
            'last_name_bn' => $this->last_name_bn,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'age' => $this->age,
            'blood_group' => $this->blood_group,
            'religion' => $this->religion,
            'nationality' => $this->nationality,
            'nid' => $this->nid,
            'passport' => $this->passport,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'alternate_mobile' => $this->alternate_mobile,
            'photo' => $this->photo,
            'photo_url' => $this->photo_url,
            'signature' => $this->signature,
            'signature_url' => $this->signature_url,
            'marital_status' => $this->marital_status,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'present_address' => $this->present_address,
            'permanent_address' => $this->permanent_address,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
