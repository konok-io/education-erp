<?php

declare(strict_types=1);

namespace App\Http\Resources\Student;

use App\Http\Resources\BaseResource;

class GuardianResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'guardian_type' => $this->guardian_type,
            'name' => $this->name,
            'name_bn' => $this->name_bn,
            'relation' => $this->relation,
            'occupation' => $this->occupation,
            'organization' => $this->organization,
            'designation' => $this->designation,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'nid' => $this->nid,
            'annual_income' => $this->annual_income,
            'photo' => $this->photo,
            'address' => $this->address,
            'is_emergency_contact' => $this->is_emergency_contact,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
