<?php

declare(strict_types=1);

namespace App\Http\Resources\Hostel;

use App\Models\Hostel\Bed;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'bed_number' => $this->bed_number,
            'bed_code' => $this->bed_code,
            'position' => $this->position,
            'position_label' => Bed::positions()[$this->position] ?? $this->position,
            'room_id' => $this->room?->uuid,
            'room' => $this->room ? [
                'id' => $this->room->uuid,
                'room_number' => $this->room->room_number,
                'room_code' => $this->room->room_code,
            ] : null,
            'status' => $this->status,
            'status_label' => Bed::statuses()[$this->status] ?? $this->status,
            'is_available' => $this->isAvailable(),
            'allocation_date' => $this->allocation_date,
            'checkout_date' => $this->checkout_date,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
