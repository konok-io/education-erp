<?php

declare(strict_types=1);

namespace App\DTO\LMS;

use App\Enums\LMS\EnrollmentStatus;
use Illuminate\Http\Request;

final class EnrollmentDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $course_uuid,
        public readonly string $student_uuid,
        public readonly EnrollmentStatus $status = EnrollmentStatus::PENDING,
        public readonly ?float $amount_paid,
        public readonly ?string $payment_id,
        public readonly string $payment_status = 'pending',
        public readonly ?\DateTimeInterface $enrolled_at,
        public readonly ?\DateTimeInterface $completed_at,
        public readonly ?float $progress_percentage,
        public readonly ?string $completion_certificate,
        public readonly ?string $notes,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            course_uuid: $request->input('course_uuid'),
            student_uuid: $request->input('student_uuid'),
            status: EnrollmentStatus::tryFrom($request->input('status', 'pending')) ?? EnrollmentStatus::PENDING,
            amount_paid: $request->input('amount_paid') ? (float) $request->input('amount_paid') : null,
            payment_id: $request->input('payment_id'),
            payment_status: $request->input('payment_status', 'pending'),
            enrolled_at: $request->input('enrolled_at') ? new \DateTime($request->input('enrolled_at')) : null,
            completed_at: $request->input('completed_at') ? new \DateTime($request->input('completed_at')) : null,
            progress_percentage: $request->input('progress_percentage') ? (float) $request->input('progress_percentage') : null,
            completion_certificate: $request->input('completion_certificate'),
            notes: $request->input('notes'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'course_uuid' => $this->course_uuid,
            'student_uuid' => $this->student_uuid,
            'status' => $this->status->value,
            'amount_paid' => $this->amount_paid,
            'payment_id' => $this->payment_id,
            'payment_status' => $this->payment_status,
            'enrolled_at' => $this->enrolled_at?->format('Y-m-d H:i:s'),
            'completed_at' => $this->completed_at?->format('Y-m-d H:i:s'),
            'progress_percentage' => $this->progress_percentage,
            'completion_certificate' => $this->completion_certificate,
            'notes' => $this->notes,
        ];
    }
}
