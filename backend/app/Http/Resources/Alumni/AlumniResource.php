<?php

declare(strict_types=1);

namespace App\Http\Resources\Alumni;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlumniResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'membership_number' => $this->membership_number,
            'student_id' => $this->student_id,
            'registration_number' => $this->registration_number,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'photo' => $this->photo,
            'passing_year' => $this->passing_year,
            'department' => $this->department,
            'program' => $this->program,
            'current_occupation' => $this->current_occupation,
            'current_organization' => $this->current_organization,
            'designation' => $this->designation,
            'country' => $this->country,
            'city' => $this->city,
            'address' => $this->address,
            'linkedin' => $this->linkedin,
            'twitter' => $this->twitter,
            'facebook' => $this->facebook,
            'website' => $this->website,
            'bio' => $this->bio,
            'skills' => $this->skills,
            'education' => $this->education,
            'experience' => $this->experience,
            'achievements' => $this->achievements,
            'employment_status' => $this->employment_status,
            'employment_status_label' => \App\Models\Alumni\AlumniProfile::employmentStatuses()[$this->employment_status] ?? $this->employment_status,
            'current_salary' => $this->current_salary,
            'salary_currency' => $this->salary_currency,
            'membership_type' => $this->membership_type,
            'membership_type_label' => \App\Models\Alumni\AlumniProfile::membershipTypes()[$this->membership_type] ?? $this->membership_type,
            'membership_start_date' => $this->membership_start_date,
            'membership_end_date' => $this->membership_end_date,
            'is_verified' => $this->is_verified,
            'is_active' => $this->is_active,
            'status' => $this->status,
            'status_label' => \App\Models\Alumni\AlumniProfile::statuses()[$this->status] ?? $this->status,
            'verified_at' => $this->verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
