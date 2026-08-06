<?php

declare(strict_types=1);

namespace App\DTO\Transport;

use Illuminate\Http\Request;

final class RouteDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $route_code,
        public readonly string $name,
        public readonly ?string $name_bn,
        public readonly float $distance,
        public readonly string $distance_unit = 'km',
        public readonly ?int $estimated_time,
        public readonly ?string $vehicle_uuid,
        public readonly ?string $driver_uuid,
        public readonly string $status = 'active',
        public readonly ?string $description,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            route_code: $request->input('route_code'),
            name: $request->input('name'),
            name_bn: $request->input('name_bn'),
            distance: (float) $request->input('distance', 0),
            distance_unit: $request->input('distance_unit', 'km'),
            estimated_time: $request->input('estimated_time') ? (int) $request->input('estimated_time') : null,
            vehicle_uuid: $request->input('vehicle_uuid'),
            driver_uuid: $request->input('driver_uuid'),
            status: $request->input('status', 'active'),
            description: $request->input('description'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'route_code' => $this->route_code,
            'name' => $this->name,
            'name_bn' => $this->name_bn,
            'distance' => $this->distance,
            'distance_unit' => $this->distance_unit,
            'estimated_time' => $this->estimated_time,
            'vehicle_uuid' => $this->vehicle_uuid,
            'driver_uuid' => $this->driver_uuid,
            'status' => $this->status,
            'description' => $this->description,
        ];
    }
}
