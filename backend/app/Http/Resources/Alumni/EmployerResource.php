<?php

declare(strict_types=1);

namespace App\Http\Resources\Alumni;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'company_name' => $this->company_name,
            'company_code' => $this->company_code,
            'industry' => $this->industry,
            'description' => $this->description,
            'website' => $this->website,
            'logo' => $this->logo,
            'contact_person' => $this->contact_person,
            'contact_designation' => $this->contact_designation,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'city' => $this->city,
            'address' => $this->address,
            'company_size' => $this->company_size,
            'company_type' => $this->company_type,
            'founded_year' => $this->founded_year,
            'social_links' => $this->social_links,
            'is_verified' => $this->is_verified,
            'is_featured' => $this->is_featured,
            'status' => $this->status,
            'verified_at' => $this->verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
