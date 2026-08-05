<?php

declare(strict_types=1);

namespace App\Http\Resources\Hostel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HostelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'hostel_name' => $this->hostel_name,
            'hostel_code' => $this->hostel_code,
            'hostel_type' => $this->hostel_type,
            'hostel_type_label' => \App\Models\Hostel\Hostel::hostelTypes()[$this->hostel_type] ?? $this->hostel_type,
            'gender' => $this->gender,
            'manager_name' => $this->manager_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'total_buildings' => $this->total_buildings,
            'total_rooms' => $this->total_rooms,
            'total_beds' => $this->total_beds,
            'occupied_beds' => $this->occupied_beds,
            'available_beds' => $this->available_beds,
            'occupancy_rate' => $this->occupancy_rate,
            'description' => $this->description,
            'notes' => $this->notes,
            'status' => $this->status,
            'is_active' => $this->is_active,
            'buildings' => BuildingResource::collection($this->whenLoaded('buildings')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
