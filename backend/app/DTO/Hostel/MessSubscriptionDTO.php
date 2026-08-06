<?php

declare(strict_types=1);

namespace App\DTO\Hostel;

use Illuminate\Http\Request;

final class MessSubscriptionDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $subscription_id,
        public readonly string $student_uuid,
        public readonly string $mess_plan_uuid,
        public readonly ?\DateTimeInterface $start_date,
        public readonly ?\DateTimeInterface $end_date,
        public readonly float $monthly_fee,
        public readonly string $subscription_type,
        public readonly string $status = 'active',
        public readonly ?string $payment_status,
        public readonly ?string $remarks,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            subscription_id: $request->input('subscription_id'),
            student_uuid: $request->input('student_uuid'),
            mess_plan_uuid: $request->input('mess_plan_uuid'),
            start_date: $request->input('start_date') ? new \DateTime($request->input('start_date')) : null,
            end_date: $request->input('end_date') ? new \DateTime($request->input('end_date')) : null,
            monthly_fee: (float) $request->input('monthly_fee'),
            subscription_type: $request->input('subscription_type', 'monthly'),
            status: $request->input('status', 'active'),
            payment_status: $request->input('payment_status'),
            remarks: $request->input('remarks'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'subscription_id' => $this->subscription_id,
            'student_uuid' => $this->student_uuid,
            'mess_plan_uuid' => $this->mess_plan_uuid,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'monthly_fee' => $this->monthly_fee,
            'subscription_type' => $this->subscription_type,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'remarks' => $this->remarks,
        ];
    }
}
