<?php

declare(strict_types=1);

namespace App\Http\Resources\Examination;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamHallResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'hall_name' => $this->hall_name,
            'hall_code' => $this->hall_code,
            'building' => $this->building,
            'floor' => $this->floor,
            'room_no' => $this->room_no,
            'capacity' => $this->capacity,
            'rows' => $this->rows,
            'columns' => $this->columns,
            'total_seats' => $this->total_seats,
            'description' => $this->description,
            'status' => $this->status,
            'status_label' => \App\Models\Examination\ExamHall::statuses()[$this->status] ?? $this->status,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
