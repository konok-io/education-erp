<?php

declare(strict_types=1);

namespace App\Http\Resources\Routine;

use App\Http\Resources\BaseResource;

class RoutineResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'routine_code' => $this->routine_code,
            'routine_type' => $this->routine_type,
            'day_of_week' => $this->day_of_week,
            'day_name' => $this->day_of_week !== null ? \App\Models\Routine\Routine::days()[$this->day_of_week] : null,
            'version' => $this->version,
            'is_published' => $this->is_published,
            'published_at' => $this->published_at?->toIso8601String(),
            'status' => $this->status,
            'remarks' => $this->remarks,

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
                'code' => $this->subject?->subject_code,
            ]),

            'teacher' => $this->whenLoaded('teacher', fn() => [
                'id' => $this->teacher?->uuid,
                'name' => $this->teacher?->profile?->full_name,
                'teacher_no' => $this->teacher?->teacher_no,
            ]),

            'room' => $this->whenLoaded('room', fn() => [
                'id' => $this->room?->uuid,
                'room_number' => $this->room?->room_number,
                'room_name' => $this->room?->room_name,
                'building' => $this->room?->building,
            ]),

            'time_slot' => $this->whenLoaded('timeSlot', fn() => [
                'id' => $this->timeSlot?->uuid,
                'name' => $this->timeSlot?->slot_name,
                'start_time' => $this->timeSlot?->start_time?->format('H:i'),
                'end_time' => $this->timeSlot?->end_time?->format('H:i'),
                'duration' => $this->timeSlot?->duration_minutes,
            ]),

            'period' => $this->whenLoaded('period', fn() => [
                'id' => $this->period?->uuid,
                'name' => $this->period?->period_name,
                'number' => $this->period?->period_number,
            ]),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
