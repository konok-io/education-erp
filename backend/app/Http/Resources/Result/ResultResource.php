<?php

declare(strict_types=1);

namespace App\Http\Resources\Result;

use App\Http\Resources\BaseResource;

class ResultResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'result_no' => $this->result_no,
            'total_marks' => $this->total_marks,
            'obtained_marks' => $this->obtained_marks,
            'gpa' => $this->gpa,
            'grade' => $this->grade,
            'status' => $this->status,
            'is_published' => $this->is_published,
            'published_at' => $this->published_at?->toIso8601String(),
            'remarks' => $this->remarks,

            'student' => $this->when($this->relationLoaded('student'), fn() => [
                'id' => $this->student?->uuid,
                'student_no' => $this->student?->student_no,
                'name' => $this->student?->profile?->full_name,
            ]),

            'exam' => $this->when($this->relationLoaded('exam'), fn() => [
                'id' => $this->exam?->uuid,
                'name' => $this->exam?->exam_name,
                'type' => $this->exam?->exam_type,
            ]),

            'class' => $this->when($this->relationLoaded('class'), fn() => [
                'id' => $this->class?->uuid,
                'name' => $this->class?->name,
            ]),

            'section' => $this->when($this->relationLoaded('section'), fn() => [
                'id' => $this->section?->uuid,
                'name' => $this->section?->name,
            ]),

            'session' => $this->when($this->relationLoaded('session'), fn() => [
                'id' => $this->session?->uuid,
                'title' => $this->session?->title,
            ]),

            'details' => $this->when($this->relationLoaded('details'), fn() =>
                $this->details->map(fn($d) => [
                    'id' => $d->uuid,
                    'subject' => $d->subject?->subject_name,
                    'total_marks' => $d->total_marks,
                    'obtained_marks' => $d->obtained_marks,
                    'grade' => $d->grade,
                    'grade_point' => $d->grade_point,
                    'credit' => $d->credit,
                    'is_pass' => $d->is_pass,
                ])
            ),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
