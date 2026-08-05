<?php

declare(strict_types=1);

namespace App\Http\Resources\Hostel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'building_name' => $this->building_name,
            'building_code' => $this->building_code,
            'hostel_id' => $this->hostel?->uuid,
            'hostel' => $this->hostel ? [
                'id' => $this->hostel->uuid,
                'hostel_name' => $this->hostel->hostel_name,
            ] : null,
            'campus' => $this->campus,
            'address' => $this->address,
            'total_floors' => $this->total_floors,
            'total_rooms' => $this->total_rooms,
            'total_beds' => $this->total_beds,
            'occupied_beds' => $this->occupied_beds,
            'available_beds' => $this->available_beds,
            'description' => $this->description,
            'status' => $this->status,
            'is_active' => $this->is_active,
            'floors' => FloorResource::collection($this->whenLoaded('floors')),
            'rooms' => RoomResource::collection($this->whenLoaded('rooms')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
