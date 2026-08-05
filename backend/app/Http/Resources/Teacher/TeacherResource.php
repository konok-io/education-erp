<?php

declare(strict_types=1);

namespace App\Http\Resources\Teacher;

use App\Http\Resources\BaseResource;

class TeacherResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'teacher_no' => $this->teacher_no,
            'employment_type' => $this->employment_type,
            'joining_date' => $this->joining_date?->toDateString(),
            'status' => $this->status,
            'remarks' => $this->remarks,

            'profile' => $this->whenLoaded('profile', fn() => new TeacherProfileResource($this->profile)),

            'department' => $this->whenLoaded('department', fn() => [
                'id' => $this->department?->uuid,
                'name' => $this->department?->name,
            ]),

            'qualifications' => $this->whenLoaded('qualifications'),
            'experiences' => $this->whenLoaded('experiences'),
            'documents' => $this->whenLoaded('documents'),

            'subject_assignments' => $this->whenLoaded('subjectAssignments', fn() =>
                $this->subjectAssignments->map(fn($a) => [
                    'id' => $a->uuid,
                    'subject' => ['id' => $a->subject?->uuid, 'name' => $a->subject?->subject_name],
                    'program' => ['id' => $a->program?->uuid, 'name' => $a->program?->name],
                    'session' => ['id' => $a->session?->uuid, 'title' => $a->session?->title],
                    'is_class_teacher' => $a->is_class_teacher,
                ])
            ),

            'class_assignments' => $this->whenLoaded('classAssignments', fn() =>
                $this->classAssignments->map(fn($a) => [
                    'id' => $a->uuid,
                    'class' => ['id' => $a->class?->uuid, 'name' => $a->class?->name],
                    'section' => ['id' => $a->section?->uuid, 'name' => $a->section?->name],
                    'session' => ['id' => $a->session?->uuid, 'title' => $a->session?->title],
                    'is_primary_teacher' => $a->is_primary_teacher,
                    'weekly_classes' => $a->weekly_classes,
                ])
            ),

            'salary' => $this->whenLoaded('salary'),
            'campus' => $this->whenLoaded('campus', fn() => [
                'id' => $this->campus?->uuid,
                'name' => $this->campus?->name,
            ]),

            'full_name' => $this->when(!$this->relationLoaded('profile'), fn() => $this->profile?->full_name),
            'photo_url' => $this->when(!$this->relationLoaded('profile'), fn() => $this->profile?->photo_url),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
