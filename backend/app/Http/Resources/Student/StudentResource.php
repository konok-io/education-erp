<?php

declare(strict_types=1);

namespace App\Http\Resources\Student;

use App\Http\Resources\BaseResource;

class StudentResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'student_no' => $this->student_no,
            'status' => $this->status,
            'admission_date' => $this->admission_date?->toDateString(),
            'remarks' => $this->remarks,

            // Profile
            'profile' => $this->whenLoaded('profile', fn() => new StudentProfileResource($this->profile)),

            // Guardian
            'guardian' => $this->whenLoaded('guardian', fn() => new GuardianResource($this->guardian)),

            // Medical
            'medical' => $this->whenLoaded('medical', fn() => new StudentMedicalResource($this->medical)),

            // Documents
            'documents' => $this->whenLoaded('documents', fn() => StudentDocumentResource::collection($this->documents)),

            // Academic
            'session' => $this->whenLoaded('session', fn() => [
                'id' => $this->session?->uuid,
                'title' => $this->session?->title,
            ]),
            'academic_level' => $this->whenLoaded('academicLevel', fn() => [
                'id' => $this->academicLevel?->uuid,
                'name' => $this->academicLevel?->name,
            ]),
            'faculty' => $this->whenLoaded('faculty', fn() => [
                'id' => $this->faculty?->uuid,
                'name' => $this->faculty?->name,
            ]),
            'department' => $this->whenLoaded('department', fn() => [
                'id' => $this->department?->uuid,
                'name' => $this->department?->name,
            ]),
            'program' => $this->whenLoaded('program', fn() => [
                'id' => $this->program?->uuid,
                'name' => $this->program?->name,
                'code' => $this->program?->code,
            ]),
            'semester' => $this->whenLoaded('semester', fn() => [
                'id' => $this->semester?->uuid,
                'title' => $this->semester?->title,
            ]),
            'class' => $this->whenLoaded('class', fn() => [
                'id' => $this->class?->uuid,
                'name' => $this->class?->name,
            ]),
            'section' => $this->whenLoaded('section', fn() => [
                'id' => $this->section?->uuid,
                'name' => $this->section?->name,
            ]),
            'group' => $this->whenLoaded('group', fn() => [
                'id' => $this->group?->uuid,
                'name' => $this->group?->name,
            ]),

            // Campus
            'campus' => $this->whenLoaded('campus', fn() => [
                'id' => $this->campus?->uuid,
                'name' => $this->campus?->name,
                'code' => $this->campus?->code,
            ]),

            // Computed
            'full_name' => $this->when(!$this->relationLoaded('profile'), fn() => $this->profile?->full_name),
            'photo_url' => $this->when(!$this->relationLoaded('profile'), fn() => $this->profile?->photo_url),

            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
