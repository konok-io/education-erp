<?php

declare(strict_types=1);

namespace App\DTO\Exam;

use Illuminate\Http\Request;

final class SeatPlanDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $exam_session_uuid,
        public readonly string $exam_center_uuid,
        public readonly string $room_uuid,
        public readonly string $student_uuid,
        public readonly ?string $seat_number,
        public readonly ?string $qr_code,
        public readonly bool $is_allocated = true,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            exam_session_uuid: $request->input('exam_session_uuid'),
            exam_center_uuid: $request->input('exam_center_uuid'),
            room_uuid: $request->input('room_uuid'),
            student_uuid: $request->input('student_uuid'),
            seat_number: $request->input('seat_number'),
            qr_code: $request->input('qr_code'),
            is_allocated: (bool) $request->input('is_allocated', true),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'exam_session_uuid' => $this->exam_session_uuid,
            'exam_center_uuid' => $this->exam_center_uuid,
            'room_uuid' => $this->room_uuid,
            'student_uuid' => $this->student_uuid,
            'seat_number' => $this->seat_number,
            'qr_code' => $this->qr_code,
            'is_allocated' => $this->is_allocated,
        ];
    }
}
