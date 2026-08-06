<?php

declare(strict_types=1);

namespace App\DTO\SmartCampus;

use App\Enums\SmartCampus\DeviceStatus;
use App\Enums\SmartCampus\DeviceType;
use App\Enums\SmartCampus\IoTStatus;
use Illuminate\Http\Request;

final class IoTDeviceDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $device_code,
        public readonly string $name,
        public readonly DeviceType $type,
        public readonly string $location_uuid,
        public readonly ?string $location_name,
        public readonly ?string $manufacturer,
        public readonly ?string $model,
        public readonly ?string $firmware_version,
        public readonly ?string $mac_address,
        public readonly ?string $ip_address,
        public readonly IoTStatus $status = IoTStatus::ONLINE,
        public readonly DeviceStatus $device_status = DeviceStatus::ACTIVE,
        public readonly ?string $last_maintenance,
        public readonly ?string $next_maintenance,
        public readonly ?string $installation_date,
        public readonly ?string $photo,
        public readonly ?array $specifications,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            device_code: $request->input('device_code'),
            name: $request->input('name'),
            type: DeviceType::from($request->input('type')),
            location_uuid: $request->input('location_uuid'),
            location_name: $request->input('location_name'),
            manufacturer: $request->input('manufacturer'),
            model: $request->input('model'),
            firmware_version: $request->input('firmware_version'),
            mac_address: $request->input('mac_address'),
            ip_address: $request->input('ip_address'),
            status: IoTStatus::tryFrom($request->input('status', 'online')) ?? IoTStatus::ONLINE,
            device_status: DeviceStatus::tryFrom($request->input('device_status', 'active')) ?? DeviceStatus::ACTIVE,
            last_maintenance: $request->input('last_maintenance'),
            next_maintenance: $request->input('next_maintenance'),
            installation_date: $request->input('installation_date'),
            photo: $request->input('photo'),
            specifications: $request->input('specifications'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'device_code' => $this->device_code,
            'name' => $this->name,
            'type' => $this->type->value,
            'location_uuid' => $this->location_uuid,
            'location_name' => $this->location_name,
            'manufacturer' => $this->manufacturer,
            'model' => $this->model,
            'firmware_version' => $this->firmware_version,
            'mac_address' => $this->mac_address,
            'ip_address' => $this->ip_address,
            'status' => $this->status->value,
            'device_status' => $this->device_status->value,
            'last_maintenance' => $this->last_maintenance,
            'next_maintenance' => $this->next_maintenance,
            'installation_date' => $this->installation_date,
            'photo' => $this->photo,
            'specifications' => $this->specifications,
        ];
    }
}
