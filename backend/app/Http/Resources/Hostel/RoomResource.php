<?php

declare(strict_types=1);

namespace App\Http\Resources\Hostel;

use App\Models\Hostel\Room;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'room_number' => $this->room_number,
            'room_code' => $this->room_code,
            'room_type' => $this->room_type,
            'room_type_label' => Room::roomTypes()[$this->room_type] ?? $this->room_type,
            'hostel_id' => $this->hostel?->uuid,
            'hostel' => $this->hostel ? [
                'id' => $this->hostel->uuid,
                'hostel_name' => $this->hostel->hostel_name,
            ] : null,
            'building_id' => $this->building?->uuid,
            'building' => $this->building ? [
                'id' => $this->building->uuid,
                'building_name' => $this->building->building_name,
            ] : null,
            'floor_id' => $this->floor?->uuid,
            'floor' => $this->floor ? [
                'id' => $this->floor->uuid,
                'floor_number' => $this->floor->floor_number,
            ] : null,
            'floor_number' => $this->floor_number,
            'capacity' => $this->capacity,
            'occupied' => $this->occupied,
            'available_beds' => $this->available_beds,
            'is_available' => $this->isAvailable(),
            'monthly_fee' => $this->monthly_fee,
            'security_deposit' => $this->security_deposit,
            'location' => $this->location,
            'description' => $this->description,
            'status' => $this->status,
            'status_label' => Room::statuses()[$this->status] ?? $this->status,
            'is_active' => $this->is_active,
            'beds' => BedResource::collection($this->whenLoaded('beds')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
