<?php

declare(strict_types=1);

namespace App\DTO\Hostel;

use Illuminate\Http\Request;

final class BuildingDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $building_code,
        public readonly string $name,
        public readonly ?string $name_bn,
        public readonly ?string $campus_uuid,
        public readonly ?string $gender,
        public readonly int $total_floors,
        public readonly int $total_rooms,
        public readonly int $total_beds,
        public readonly ?string $address,
        public readonly ?string $description,
        public readonly string $status = 'active',
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            building_code: $request->input('building_code'),
            name: $request->input('name'),
            name_bn: $request->input('name_bn'),
            campus_uuid: $request->input('campus_uuid'),
            gender: $request->input('gender'),
            total_floors: (int) $request->input('total_floors', 1),
            total_rooms: (int) $request->input('total_rooms', 0),
            total_beds: (int) $request->input('total_beds', 0),
            address: $request->input('address'),
            description: $request->input('description'),
            status: $request->input('status', 'active'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'building_code' => $this->building_code,
            'name' => $this->name,
            'name_bn' => $this->name_bn,
            'campus_uuid' => $this->campus_uuid,
            'gender' => $this->gender,
            'total_floors' => $this->total_floors,
            'total_rooms' => $this->total_rooms,
            'total_beds' => $this->total_beds,
            'address' => $this->address,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }
}
