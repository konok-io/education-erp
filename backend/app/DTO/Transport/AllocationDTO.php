<?php

declare(strict_types=1);

namespace App\DTO\Transport;

use Illuminate\Http\Request;

final class AllocationDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $allocation_no,
        public readonly string $student_uuid,
        public readonly string $route_uuid,
        public readonly string $pickup_stop_uuid,
        public readonly string $drop_stop_uuid,
        public readonly ?string $seat_number,
        public readonly float $monthly_fee,
        public readonly ?\DateTimeInterface $start_date,
        public readonly ?\DateTimeInterface $end_date,
        public readonly string $status = 'pending',
        public readonly ?string $payment_status,
        public readonly ?string $notes,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            allocation_no: $request->input('allocation_no'),
            student_uuid: $request->input('student_uuid'),
            route_uuid: $request->input('route_uuid'),
            pickup_stop_uuid: $request->input('pickup_stop_uuid'),
            drop_stop_uuid: $request->input('drop_stop_uuid'),
            seat_number: $request->input('seat_number'),
            monthly_fee: (float) $request->input('monthly_fee'),
            start_date: $request->input('start_date') ? new \DateTime($request->input('start_date')) : null,
            end_date: $request->input('end_date') ? new \DateTime($request->input('end_date')) : null,
            status: $request->input('status', 'pending'),
            payment_status: $request->input('payment_status'),
            notes: $request->input('notes'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'allocation_no' => $this->allocation_no,
            'student_uuid' => $this->student_uuid,
            'route_uuid' => $this->route_uuid,
            'pickup_stop_uuid' => $this->pickup_stop_uuid,
            'drop_stop_uuid' => $this->drop_stop_uuid,
            'seat_number' => $this->seat_number,
            'monthly_fee' => $this->monthly_fee,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'notes' => $this->notes,
        ];
    }
}
