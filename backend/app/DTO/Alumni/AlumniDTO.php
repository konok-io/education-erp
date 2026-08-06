<?php

declare(strict_types=1);

namespace App\DTO\Alumni;

use App\Enums\Alumni\AlumniStatus;
use App\Enums\Alumni\MembershipType;
use Illuminate\Http\Request;

final class AlumniDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $alumni_id,
        public readonly string $student_uuid,
        public readonly AlumniStatus $status = AlumniStatus::PENDING,
        public readonly MembershipType $membership_type = MembershipType::FREE,
        public readonly ?string $graduation_year,
        public readonly ?string $graduation_date,
        public readonly ?string $degree,
        public readonly ?string $department,
        public readonly ?string $current_company,
        public readonly ?string $current_designation,
        public readonly ?string $current_location,
        public readonly ?string $linkedin_url,
        public readonly ?string $facebook_url,
        public readonly ?string $twitter_url,
        public readonly ?string $website_url,
        public readonly ?string $employment_status,
        public readonly ?float $annual_income_range,
        public readonly bool $is_verified = false,
        public readonly bool $is_donatable = true,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            alumni_id: $request->input('alumni_id'),
            student_uuid: $request->input('student_uuid'),
            status: AlumniStatus::tryFrom($request->input('status', 'pending')) ?? AlumniStatus::PENDING,
            membership_type: MembershipType::tryFrom($request->input('membership_type', 'free')) ?? MembershipType::FREE,
            graduation_year: $request->input('graduation_year'),
            graduation_date: $request->input('graduation_date'),
            degree: $request->input('degree'),
            department: $request->input('department'),
            current_company: $request->input('current_company'),
            current_designation: $request->input('current_designation'),
            current_location: $request->input('current_location'),
            linkedin_url: $request->input('linkedin_url'),
            facebook_url: $request->input('facebook_url'),
            twitter_url: $request->input('twitter_url'),
            website_url: $request->input('website_url'),
            employment_status: $request->input('employment_status'),
            annual_income_range: $request->input('annual_income_range') ? (float) $request->input('annual_income_range') : null,
            is_verified: (bool) $request->input('is_verified', false),
            is_donatable: (bool) $request->input('is_donatable', true),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'alumni_id' => $this->alumni_id,
            'student_uuid' => $this->student_uuid,
            'status' => $this->status->value,
            'membership_type' => $this->membership_type->value,
            'graduation_year' => $this->graduation_year,
            'graduation_date' => $this->graduation_date,
            'degree' => $this->degree,
            'department' => $this->department,
            'current_company' => $this->current_company,
            'current_designation' => $this->current_designation,
            'current_location' => $this->current_location,
            'linkedin_url' => $this->linkedin_url,
            'facebook_url' => $this->facebook_url,
            'twitter_url' => $this->twitter_url,
            'website_url' => $this->website_url,
            'employment_status' => $this->employment_status,
            'annual_income_range' => $this->annual_income_range,
            'is_verified' => $this->is_verified,
            'is_donatable' => $this->is_donatable,
        ];
    }
}
