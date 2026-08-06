<?php

declare(strict_types=1);

namespace App\DTO\Exam;

use App\Enums\Exam\ExamStatus;
use App\Enums\Exam\ExamType;
use Illuminate\Http\Request;

final class ExamDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $title,
        public readonly ExamType $type,
        public readonly string $academic_year_uuid,
        public readonly ?string $semester_uuid,
        public readonly ?string $subject_uuid,
        public readonly ?string $class_uuid,
        public readonly \DateTimeInterface $exam_date,
        public readonly int $duration_minutes,
        public readonly float $full_marks,
        public readonly float $pass_marks,
        public readonly float $practical_marks = 0,
        public readonly float $theory_marks = 0,
        public readonly ExamStatus $status = ExamStatus::DRAFT,
        public readonly ?string $description = null,
        public readonly bool $is_online = false,
        public readonly bool $negative_marking = false,
        public readonly ?float $negative_mark_value = null,
        public readonly bool $randomize_questions = false,
        public readonly bool $randomize_options = false,
        public readonly ?int $total_questions = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            title: $request->input('title'),
            type: ExamType::from($request->input('type')),
            academic_year_uuid: $request->input('academic_year_uuid'),
            semester_uuid: $request->input('semester_uuid'),
            subject_uuid: $request->input('subject_uuid'),
            class_uuid: $request->input('class_uuid'),
            exam_date: new \DateTime($request->input('exam_date')),
            duration_minutes: (int) $request->input('duration_minutes', 60),
            full_marks: (float) $request->input('full_marks'),
            pass_marks: (float) $request->input('pass_marks'),
            practical_marks: (float) $request->input('practical_marks', 0),
            theory_marks: (float) $request->input('theory_marks', 0),
            status: ExamStatus::tryFrom($request->input('status', 'draft')) ?? ExamStatus::DRAFT,
            description: $request->input('description'),
            is_online: (bool) $request->input('is_online', false),
            negative_marking: (bool) $request->input('negative_marking', false),
            negative_mark_value: $request->input('negative_mark_value') ? (float) $request->input('negative_mark_value') : null,
            randomize_questions: (bool) $request->input('randomize_questions', false),
            randomize_options: (bool) $request->input('randomize_options', false),
            total_questions: $request->input('total_questions') ? (int) $request->input('total_questions') : null,
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'type' => $this->type->value,
            'academic_year_uuid' => $this->academic_year_uuid,
            'semester_uuid' => $this->semester_uuid,
            'subject_uuid' => $this->subject_uuid,
            'class_uuid' => $this->class_uuid,
            'exam_date' => $this->exam_date->format('Y-m-d'),
            'duration_minutes' => $this->duration_minutes,
            'full_marks' => $this->full_marks,
            'pass_marks' => $this->pass_marks,
            'practical_marks' => $this->practical_marks,
            'theory_marks' => $this->theory_marks,
            'status' => $this->status->value,
            'description' => $this->description,
            'is_online' => $this->is_online,
            'negative_marking' => $this->negative_marking,
            'negative_mark_value' => $this->negative_mark_value,
            'randomize_questions' => $this->randomize_questions,
            'randomize_options' => $this->randomize_options,
            'total_questions' => $this->total_questions,
        ];
    }
}
