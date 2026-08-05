<?php

declare(strict_types=1);

namespace App\Http\Resources\Alumni;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'event_number' => $this->event_number,
            'event_title' => $this->event_title,
            'description' => $this->description,
            'event_type' => $this->event_type,
            'event_type_label' => \App\Models\Alumni\AlumniEvent::eventTypes()[$this->event_type] ?? $this->event_type,
            'banner_image' => $this->banner_image,
            'event_date' => $this->event_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'venue' => $this->venue,
            'city' => $this->city,
            'country' => $this->country,
            'address' => $this->address,
            'agenda' => $this->agenda,
            'speakers' => $this->speakers,
            'max_participants' => $this->max_participants,
            'registered_count' => $this->registered_count,
            'registration_fee' => $this->registration_fee,
            'is_free' => $this->is_free,
            'is_virtual' => $this->is_virtual,
            'meeting_link' => $this->meeting_link,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'status' => $this->status,
            'status_label' => \App\Models\Alumni\AlumniEvent::statuses()[$this->status] ?? $this->status,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
