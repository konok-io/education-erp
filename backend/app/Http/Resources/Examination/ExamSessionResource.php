<?php

declare(strict_types=1);

namespace App\Http\Resources\Examination;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'session_name' => $this->session_name,
            'academic_year' => $this->academic_year,
            'semester' => $this->semester,
            'term' => $this->term,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'description' => $this->description,
            'status' => $this->status,
            'status_label' => \App\Models\Examination\ExamSession::statuses()[$this->status] ?? $this->status,
            'is_current' => $this->is_current,
            'exams_count' => $this->exams()->count(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
