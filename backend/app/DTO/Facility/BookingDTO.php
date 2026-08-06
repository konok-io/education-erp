<?php

declare(strict_types=1);

namespace App\DTO\Facility;

use Illuminate\Http\Request;

final class BookingDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $booking_no,
        public readonly string $facility_uuid,
        public readonly string $booked_by,
        public readonly ?string $organizer_name,
        public readonly ?string $event_name,
        public readonly ?string $description,
        public readonly string $booking_date,
        public readonly string $start_time,
        public readonly string $end_time,
        public readonly int $expected_attendees,
        public readonly string $status = 'pending',
        public readonly ?string $approval_remarks,
        public readonly ?string $approved_by,
        public readonly ?float $rental_fee,
        public readonly ?float $security_deposit,
        public readonly string $payment_status = 'unpaid',
        public readonly ?string $cancellation_reason,
        public readonly ?string $notes,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            booking_no: $request->input('booking_no'),
            facility_uuid: $request->input('facility_uuid'),
            booked_by: $request->input('booked_by'),
            organizer_name: $request->input('organizer_name'),
            event_name: $request->input('event_name'),
            description: $request->input('description'),
            booking_date: $request->input('booking_date'),
            start_time: $request->input('start_time'),
            end_time: $request->input('end_time'),
            expected_attendees: (int) $request->input('expected_attendees', 1),
            status: $request->input('status', 'pending'),
            approval_remarks: $request->input('approval_remarks'),
            approved_by: $request->input('approved_by'),
            rental_fee: $request->input('rental_fee') ? (float) $request->input('rental_fee') : null,
            security_deposit: $request->input('security_deposit') ? (float) $request->input('security_deposit') : null,
            payment_status: $request->input('payment_status', 'unpaid'),
            cancellation_reason: $request->input('cancellation_reason'),
            notes: $request->input('notes'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'booking_no' => $this->booking_no,
            'facility_uuid' => $this->facility_uuid,
            'booked_by' => $this->booked_by,
            'organizer_name' => $this->organizer_name,
            'event_name' => $this->event_name,
            'description' => $this->description,
            'booking_date' => $this->booking_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'expected_attendees' => $this->expected_attendees,
            'status' => $this->status,
            'approval_remarks' => $this->approval_remarks,
            'approved_by' => $this->approved_by,
            'rental_fee' => $this->rental_fee,
            'security_deposit' => $this->security_deposit,
            'payment_status' => $this->payment_status,
            'cancellation_reason' => $this->cancellation_reason,
            'notes' => $this->notes,
        ];
    }
}
