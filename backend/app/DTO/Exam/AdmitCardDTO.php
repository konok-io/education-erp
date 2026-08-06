<?php

declare(strict_types=1);

namespace App\DTO\Exam;

use Illuminate\Http\Request;

final class AdmitCardDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $admit_card_number,
        public readonly string $exam_session_uuid,
        public readonly string $student_uuid,
        public readonly ?string $exam_center_uuid,
        public readonly ?string $room_uuid,
        public readonly ?string $seat_number,
        public readonly ?string $student_photo,
        public readonly ?string $qr_code,
        public readonly ?\DateTimeInterface $issue_date,
        public readonly ?\DateTimeInterface $valid_until,
        public readonly bool $is_printed = false,
        public readonly ?string $printed_at = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            admit_card_number: $request->input('admit_card_number'),
            exam_session_uuid: $request->input('exam_session_uuid'),
            student_uuid: $request->input('student_uuid'),
            exam_center_uuid: $request->input('exam_center_uuid'),
            room_uuid: $request->input('room_uuid'),
            seat_number: $request->input('seat_number'),
            student_photo: $request->input('student_photo'),
            qr_code: $request->input('qr_code'),
            issue_date: $request->input('issue_date') ? new \DateTime($request->input('issue_date')) : null,
            valid_until: $request->input('valid_until') ? new \DateTime($request->input('valid_until')) : null,
            is_printed: (bool) $request->input('is_printed', false),
            printed_at: $request->input('printed_at'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'admit_card_number' => $this->admit_card_number,
            'exam_session_uuid' => $this->exam_session_uuid,
            'student_uuid' => $this->student_uuid,
            'exam_center_uuid' => $this->exam_center_uuid,
            'room_uuid' => $this->room_uuid,
            'seat_number' => $this->seat_number,
            'student_photo' => $this->student_photo,
            'qr_code' => $this->qr_code,
            'issue_date' => $this->issue_date?->format('Y-m-d'),
            'valid_until' => $this->valid_until?->format('Y-m-d'),
            'is_printed' => $this->is_printed,
            'printed_at' => $this->printed_at,
        ];
    }
}
