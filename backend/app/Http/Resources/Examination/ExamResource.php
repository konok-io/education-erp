<?php

declare(strict_types=1);

namespace App\Http\Resources\Examination;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'exam_name' => $this->exam_name,
            'exam_code' => $this->exam_code,
            'exam_type' => $this->exam_type,
            'exam_type_label' => \App\Models\Examination\Exam::examTypes()[$this->exam_type] ?? $this->exam_type,
            'exam_session_id' => $this->session?->uuid,
            'exam_session' => $this->session ? [
                'id' => $this->session->uuid,
                'session_name' => $this->session->session_name,
            ] : null,
            'class_id' => $this->class_id,
            'section_id' => $this->section_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'result_publish_date' => $this->result_publish_date,
            'description' => $this->description,
            'instructions' => $this->instructions,
            'status' => $this->status,
            'status_label' => \App\Models\Examination\Exam::statuses()[$this->status] ?? $this->status,
            'is_published' => $this->is_published,
            'subjects_count' => $this->subjects()->count(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
