<?php

declare(strict_types=1);

namespace App\Http\Resources\Research;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResearchGrantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'grant_number' => $this->grant_number,
            'grant_title' => $this->grant_title,
            'description' => $this->description,
            'grant_amount' => $this->grant_amount,
            'currency' => $this->currency,
            'application_date' => $this->application_date,
            'approval_date' => $this->approval_date,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'status_label' => \App\Models\Research\ResearchGrant::statuses()[$this->status] ?? $this->status,
            'budget_breakdown' => $this->budget_breakdown,
            'released_amount' => $this->released_amount,
            'remaining_amount' => $this->getRemainingAmount(),
            'terms_conditions' => $this->terms_conditions,
            'agreement_document' => $this->agreement_document,
            'funding_agency' => $this->whenLoaded('fundingAgency', function () {
                return [
                    'id' => $this->fundingAgency->uuid,
                    'agency_name' => $this->fundingAgency->agency_name,
                    'agency_type' => $this->fundingAgency->agency_type,
                ];
            }),
            'project' => $this->whenLoaded('project', function () {
                return [
                    'id' => $this->project->uuid,
                    'project_code' => $this->project->project_code,
                    'project_title' => $this->project->project_title,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
