<?php

declare(strict_types=1);

namespace App\DTO\Exam;

use App\Enums\Exam\ResultStatus;
use Illuminate\Http\Request;

final class ExamResultDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $exam_uuid,
        public readonly string $student_uuid,
        public readonly float $obtained_marks = 0,
        public readonly float $practical_marks = 0,
        public readonly float $theory_marks = 0,
        public readonly float $negative_marks = 0,
        public readonly float $total_marks = 0,
        public readonly float $percentage = 0,
        public readonly ?string $grade = null,
        public readonly ResultStatus $status = ResultStatus::PENDING,
        public readonly ?\DateTimeInterface $evaluated_at = null,
        public readonly ?string $evaluated_by = null,
        public readonly ?string $remarks = null,
        public readonly bool $is_locked = false,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            exam_uuid: $request->input('exam_uuid'),
            student_uuid: $request->input('student_uuid'),
            obtained_marks: (float) $request->input('obtained_marks', 0),
            practical_marks: (float) $request->input('practical_marks', 0),
            theory_marks: (float) $request->input('theory_marks', 0),
            negative_marks: (float) $request->input('negative_marks', 0),
            total_marks: (float) $request->input('total_marks', 0),
            percentage: (float) $request->input('percentage', 0),
            grade: $request->input('grade'),
            status: ResultStatus::tryFrom($request->input('status', 'pending')) ?? ResultStatus::PENDING,
            evaluated_at: $request->input('evaluated_at') ? new \DateTime($request->input('evaluated_at')) : null,
            evaluated_by: $request->input('evaluated_by'),
            remarks: $request->input('remarks'),
            is_locked: (bool) $request->input('is_locked', false),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'exam_uuid' => $this->exam_uuid,
            'student_uuid' => $this->student_uuid,
            'obtained_marks' => $this->obtained_marks,
            'practical_marks' => $this->practical_marks,
            'theory_marks' => $this->theory_marks,
            'negative_marks' => $this->negative_marks,
            'total_marks' => $this->total_marks,
            'percentage' => $this->percentage,
            'grade' => $this->grade,
            'status' => $this->status->value,
            'evaluated_at' => $this->evaluated_at?->format('Y-m-d H:i:s'),
            'evaluated_by' => $this->evaluated_by,
            'remarks' => $this->remarks,
            'is_locked' => $this->is_locked,
        ];
    }
}
