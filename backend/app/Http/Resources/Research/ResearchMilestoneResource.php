<?php

declare(strict_types=1);

namespace App\Http\Resources\Research;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResearchMilestoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'milestone_name' => $this->milestone_name,
            'description' => $this->description,
            'order' => $this->order,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'actual_completion_date' => $this->actual_completion_date,
            'status' => $this->status,
            'status_label' => \App\Models\Research\ResearchMilestone::statuses()[$this->status] ?? $this->status,
            'progress_percentage' => $this->progress_percentage,
            'deliverables' => $this->deliverables,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
