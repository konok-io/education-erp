<?php

declare(strict_types=1);

namespace App\DTO\Hostel;

use Illuminate\Http\Request;

final class AdmissionDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $admission_no,
        public readonly string $student_uuid,
        public readonly string $building_uuid,
        public readonly string $room_uuid,
        public readonly string $bed_uuid,
        public readonly ?\DateTimeInterface $admission_date,
        public readonly ?\DateTimeInterface $checkout_date,
        public readonly string $status = 'pending',
        public readonly ?float $security_deposit,
        public readonly ?string $payment_status,
        public readonly ?string $remarks,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            admission_no: $request->input('admission_no'),
            student_uuid: $request->input('student_uuid'),
            building_uuid: $request->input('building_uuid'),
            room_uuid: $request->input('room_uuid'),
            bed_uuid: $request->input('bed_uuid'),
            admission_date: $request->input('admission_date') ? new \DateTime($request->input('admission_date')) : null,
            checkout_date: $request->input('checkout_date') ? new \DateTime($request->input('checkout_date')) : null,
            status: $request->input('status', 'pending'),
            security_deposit: $request->input('security_deposit') ? (float) $request->input('security_deposit') : null,
            payment_status: $request->input('payment_status'),
            remarks: $request->input('remarks'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'admission_no' => $this->admission_no,
            'student_uuid' => $this->student_uuid,
            'building_uuid' => $this->building_uuid,
            'room_uuid' => $this->room_uuid,
            'bed_uuid' => $this->bed_uuid,
            'admission_date' => $this->admission_date?->format('Y-m-d'),
            'checkout_date' => $this->checkout_date?->format('Y-m-d'),
            'status' => $this->status,
            'security_deposit' => $this->security_deposit,
            'payment_status' => $this->payment_status,
            'remarks' => $this->remarks,
        ];
    }
}
