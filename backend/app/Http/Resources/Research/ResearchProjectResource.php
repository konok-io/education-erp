<?php

declare(strict_types=1);

namespace App\Http\Resources\Research;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResearchProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'project_code' => $this->project_code,
            'project_title' => $this->project_title,
            'abstract' => $this->abstract,
            'objectives' => $this->objectives,
            'expected_outcome' => $this->expected_outcome,
            'category' => $this->category,
            'research_type' => $this->research_type,
            'research_type_label' => \App\Models\Research\ResearchProject::researchTypes()[$this->research_type] ?? $this->research_type,
            'department' => $this->department,
            'keywords' => $this->keywords,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'status_label' => \App\Models\Research\ResearchProject::statuses()[$this->status] ?? $this->status,
            'priority' => $this->priority,
            'priority_label' => \App\Models\Research\ResearchProject::priorities()[$this->priority] ?? $this->priority,
            'budget' => $this->budget,
            'budget_currency' => $this->budget_currency,
            'ethics_approval' => $this->ethics_approval,
            'principal_investigator' => $this->whenLoaded('principalInvestigator', function () {
                return [
                    'id' => $this->principalInvestigator->uuid,
                    'name' => $this->principalInvestigator->name,
                    'email' => $this->principalInvestigator->email,
                ];
            }),
            'teams' => $this->whenLoaded('teams', function () {
                return ResearchTeamResource::collection($this->teams);
            }),
            'milestones' => $this->whenLoaded('milestones', function () {
                return ResearchMilestoneResource::collection($this->milestones);
            }),
            'progress_percentage' => $this->getProgressPercentage(),
            'is_featured' => $this->is_featured,
            'is_public' => $this->is_public,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
