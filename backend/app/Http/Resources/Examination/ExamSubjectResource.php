<?php

declare(strict_types=1);

namespace App\Http\Resources\Examination;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamSubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'exam_id' => $this->exam?->uuid,
            'exam' => $this->exam ? [
                'id' => $this->exam->uuid,
                'exam_name' => $this->exam->exam_name,
            ] : null,
            'subject_id' => $this->subject_id,
            'subject_code' => $this->subject_code,
            'subject_name' => $this->subject_name,
            'exam_date' => $this->exam_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'duration_minutes' => $this->duration_minutes,
            'duration' => $this->duration,
            'full_marks' => $this->full_marks,
            'pass_marks' => $this->pass_marks,
            'practical_marks' => $this->practical_marks,
            'theory_marks' => $this->theory_marks,
            'exam_mode' => $this->exam_mode,
            'exam_mode_label' => \App\Models\Examination\ExamSubject::examModes()[$this->exam_mode] ?? $this->exam_mode,
            'syllabus' => $this->syllabus,
            'status' => $this->status,
            'status_label' => \App\Models\Examination\ExamSubject::statuses()[$this->status] ?? $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
