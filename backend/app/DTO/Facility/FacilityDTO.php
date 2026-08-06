<?php

declare(strict_types=1);

namespace App\DTO\Facility;

use Illuminate\Http\Request;

final class FacilityDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $name,
        public readonly ?string $name_bn,
        public readonly string $facility_type_uuid,
        public readonly string $code,
        public readonly ?string $location,
        public readonly int $capacity,
        public readonly ?string $equipment,
        public readonly ?string $available_from,
        public readonly ?string $available_to,
        public readonly ?string $description,
        public readonly ?string $photo,
        public readonly string $status = 'available',
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            name: $request->input('name'),
            name_bn: $request->input('name_bn'),
            facility_type_uuid: $request->input('facility_type_uuid'),
            code: $request->input('code'),
            location: $request->input('location'),
            capacity: (int) $request->input('capacity', 1),
            equipment: $request->input('equipment'),
            available_from: $request->input('available_from'),
            available_to: $request->input('available_to'),
            description: $request->input('description'),
            photo: $request->input('photo'),
            status: $request->input('status', 'available'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'name_bn' => $this->name_bn,
            'facility_type_uuid' => $this->facility_type_uuid,
            'code' => $this->code,
            'location' => $this->location,
            'capacity' => $this->capacity,
            'equipment' => $this->equipment,
            'available_from' => $this->available_from,
            'available_to' => $this->available_to,
            'description' => $this->description,
            'photo' => $this->photo,
            'status' => $this->status,
        ];
    }
}
