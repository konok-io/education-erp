<?php

declare(strict_types=1);

namespace App\DTO\SmartCampus;

use Illuminate\Http\Request;

final class AccessLogDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $device_uuid,
        public readonly string $user_uuid,
        public readonly string $user_type,
        public readonly string $access_type,
        public readonly \DateTimeInterface $access_time,
        public readonly string $status,
        public readonly ?string $temperature,
        public readonly ?string $humidity,
        public readonly ?string $image_path,
        public readonly ?string $qr_data,
        public readonly ?string $rfid_tag,
        public readonly ?string $remarks,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            device_uuid: $request->input('device_uuid'),
            user_uuid: $request->input('user_uuid'),
            user_type: $request->input('user_type'),
            access_type: $request->input('access_type'),
            access_time: new \DateTime($request->input('access_time')),
            status: $request->input('status', 'success'),
            temperature: $request->input('temperature'),
            humidity: $request->input('humidity'),
            image_path: $request->input('image_path'),
            qr_data: $request->input('qr_data'),
            rfid_tag: $request->input('rfid_tag'),
            remarks: $request->input('remarks'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'device_uuid' => $this->device_uuid,
            'user_uuid' => $this->user_uuid,
            'user_type' => $this->user_type,
            'access_type' => $this->access_type,
            'access_time' => $this->access_time->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'temperature' => $this->temperature,
            'humidity' => $this->humidity,
            'image_path' => $this->image_path,
            'qr_data' => $this->qr_data,
            'rfid_tag' => $this->rfid_tag,
            'remarks' => $this->remarks,
        ];
    }
}
