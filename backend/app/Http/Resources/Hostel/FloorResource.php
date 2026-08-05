<?php

declare(strict_types=1);

namespace App\Http\Resources\Hostel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FloorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'floor_number' => $this->floor_number,
            'floor_name' => $this->floor_name,
            'building_id' => $this->building?->uuid,
            'building' => $this->building ? [
                'id' => $this->building->uuid,
                'building_name' => $this->building->building_name,
            ] : null,
            'total_rooms' => $this->total_rooms,
            'total_beds' => $this->total_beds,
            'occupied_beds' => $this->occupied_beds,
            'available_beds' => $this->available_beds,
            'description' => $this->description,
            'status' => $this->status,
            'is_active' => $this->is_active,
            'rooms' => RoomResource::collection($this->whenLoaded('rooms')),
        ];
    }
}
