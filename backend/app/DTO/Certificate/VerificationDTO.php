<?php

declare(strict_types=1);

namespace App\DTO\Certificate;

use App\Enums\Certificate\VerificationStatus;
use Illuminate\Http\Request;

final class VerificationDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $certificate_uuid,
        public readonly string $verification_code,
        public readonly string $applicant_name,
        public readonly ?string $applicant_email,
        public readonly ?string $applicant_phone,
        public readonly ?string $purpose,
        public readonly VerificationStatus $status = VerificationStatus::PENDING,
        public readonly ?string $verified_at = null,
        public readonly ?string $verified_by = null,
        public readonly ?string $remarks = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            certificate_uuid: $request->input('certificate_uuid'),
            verification_code: $request->input('verification_code'),
            applicant_name: $request->input('applicant_name'),
            applicant_email: $request->input('applicant_email'),
            applicant_phone: $request->input('applicant_phone'),
            purpose: $request->input('purpose'),
            status: VerificationStatus::tryFrom($request->input('status', 'pending')) ?? VerificationStatus::PENDING,
            verified_at: $request->input('verified_at'),
            verified_by: $request->input('verified_by'),
            remarks: $request->input('remarks'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'certificate_uuid' => $this->certificate_uuid,
            'verification_code' => $this->verification_code,
            'applicant_name' => $this->applicant_name,
            'applicant_email' => $this->applicant_email,
            'applicant_phone' => $this->applicant_phone,
            'purpose' => $this->purpose,
            'status' => $this->status->value,
            'verified_at' => $this->verified_at,
            'verified_by' => $this->verified_by,
            'remarks' => $this->remarks,
        ];
    }
}
