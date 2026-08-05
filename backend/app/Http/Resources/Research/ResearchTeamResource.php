<?php

declare(strict_types=1);

namespace App\Http\Resources\Research;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResearchTeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'member_name' => $this->member_name,
            'member_email' => $this->member_email,
            'designation' => $this->designation,
            'department' => $this->department,
            'institution' => $this->institution,
            'role' => $this->role,
            'role_label' => \App\Models\Research\ResearchTeam::roles()[$this->role] ?? $this->role,
            'responsibilities' => $this->responsibilities,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
