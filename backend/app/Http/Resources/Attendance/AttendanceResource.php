<?php

declare(strict_types=1);

namespace App\Http\Resources\Attendance;

use App\Http\Resources\BaseResource;

class AttendanceResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'attendance_no' => $this->attendance_no,
            'attendance_type' => $this->attendance_type,
            'attendance_date' => $this->attendance_date?->toDateString(),
            'attendance_time' => $this->attendance_time?->toTimeString(),
            'status' => $this->status,
            'late_minutes' => $this->late_minutes,
            'entry_method' => $this->entry_method,
            'remarks' => $this->remarks,
            'is_approved' => $this->is_approved,
            'approved_at' => $this->approved_at?->toIso8601String(),

            'student' => $this->when($this->attendance_type === 'student' && $this->relationLoaded('student'), fn() => [
                'id' => $this->student?->uuid,
                'student_no' => $this->student?->student_no,
                'name' => $this->student?->profile?->full_name,
            ]),

            'teacher' => $this->when($this->attendance_type === 'teacher' && $this->relationLoaded('teacher'), fn() => [
                'id' => $this->teacher?->uuid,
                'teacher_no' => $this->teacher?->teacher_no,
                'name' => $this->teacher?->profile?->full_name,
            ]),

            'employee' => $this->when($this->attendance_type === 'employee' && $this->relationLoaded('employee'), fn() => [
                'id' => $this->employee?->uuid,
                'employee_no' => $this->employee?->employee_no,
                'name' => $this->employee?->profile?->full_name,
            ]),

            'session' => $this->whenLoaded('session', fn() => [
                'id' => $this->session?->uuid,
                'title' => $this->session?->title,
            ]),

            'class' => $this->whenLoaded('class', fn() => [
                'id' => $this->class?->uuid,
                'name' => $this->class?->name,
            ]),

            'section' => $this->whenLoaded('section', fn() => [
                'id' => $this->section?->uuid,
                'name' => $this->section?->name,
            ]),

            'subject' => $this->whenLoaded('subject', fn() => [
                'id' => $this->subject?->uuid,
                'name' => $this->subject?->subject_name,
            ]),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
