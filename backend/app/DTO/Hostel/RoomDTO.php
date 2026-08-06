<?php

declare(strict_types=1);

namespace App\DTO\Hostel;

use App\Enums\Hostel\RoomType;
use Illuminate\Http\Request;

final class RoomDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $room_number,
        public readonly string $building_uuid,
        public readonly int $floor,
        public readonly RoomType $type = RoomType::DOUBLE,
        public readonly int $capacity,
        public readonly int $current_occupancy = 0,
        public readonly ?float $rent,
        public readonly ?string $description,
        public readonly string $status = 'available',
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            room_number: $request->input('room_number'),
            building_uuid: $request->input('building_uuid'),
            floor: (int) $request->input('floor', 1),
            type: RoomType::tryFrom($request->input('type', 'double')) ?? RoomType::DOUBLE,
            capacity: (int) $request->input('capacity', 2),
            current_occupancy: (int) $request->input('current_occupancy', 0),
            rent: $request->input('rent') ? (float) $request->input('rent') : null,
            description: $request->input('description'),
            status: $request->input('status', 'available'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'room_number' => $this->room_number,
            'building_uuid' => $this->building_uuid,
            'floor' => $this->floor,
            'type' => $this->type->value,
            'capacity' => $this->capacity,
            'current_occupancy' => $this->current_occupancy,
            'rent' => $this->rent,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }
}
