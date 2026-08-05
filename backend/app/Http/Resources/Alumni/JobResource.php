<?php

declare(strict_types=1);

namespace App\Http\Resources\Alumni;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'job_number' => $this->job_number,
            'employer' => $this->whenLoaded('employer', function () {
                return [
                    'id' => $this->employer->uuid,
                    'company_name' => $this->employer->company_name,
                    'logo' => $this->employer->logo,
                    'industry' => $this->employer->industry,
                    'website' => $this->employer->website,
                ];
            }),
            'job_title' => $this->job_title,
            'description' => $this->description,
            'job_type' => $this->job_type,
            'job_type_label' => \App\Models\Alumni\Job::jobTypes()[$this->job_type] ?? $this->job_type,
            'department' => $this->department,
            'designation' => $this->designation,
            'location' => $this->location,
            'country' => $this->country,
            'city' => $this->city,
            'work_type' => $this->work_type,
            'work_type_label' => \App\Models\Alumni\Job::workTypes()[$this->work_type] ?? $this->work_type,
            'vacancy' => $this->vacancy,
            'requirements' => $this->requirements,
            'responsibilities' => $this->responsibilities,
            'benefits' => $this->benefits,
            'experience_required' => $this->experience_required,
            'education_required' => $this->education_required,
            'min_salary' => $this->min_salary,
            'max_salary' => $this->max_salary,
            'salary_currency' => $this->salary_currency,
            'salary_frequency' => $this->salary_frequency,
            'application_deadline' => $this->application_deadline,
            'start_date' => $this->start_date,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'status' => $this->status,
            'status_label' => \App\Models\Alumni\Job::statuses()[$this->status] ?? $this->status,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
