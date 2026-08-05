<?php

declare(strict_types=1);

namespace App\Http\Resources\Transport;

use App\Models\Transport\Driver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'driver_id' => $this->driver_id,
            'photo' => $this->photo,
            'full_name' => $this->full_name,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'blood_group' => $this->blood_group,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'present_address' => $this->present_address,
            'permanent_address' => $this->permanent_address,
            'nid' => $this->nid,
            'license_number' => $this->license_number,
            'license_type' => $this->license_type,
            'license_expiry' => $this->license_expiry,
            'license_expiring_soon' => $this->isLicenseExpiringSoon(),
            'license_expired' => $this->isLicenseExpired(),
            'joining_date' => $this->joining_date,
            'emergency_contact' => $this->emergency_contact,
            'emergency_phone' => $this->emergency_phone,
            'status' => $this->status,
            'status_label' => Driver::statuses()[$this->status] ?? $this->status,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'is_available' => $this->isAvailable(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
