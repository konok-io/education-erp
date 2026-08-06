<?php

declare(strict_types=1);

namespace App\DTO\Certificate;

use App\Enums\Certificate\CertificateStatus;
use App\Enums\Certificate\CertificateType;
use App\Enums\Certificate\VerificationStatus;
use Illuminate\Http\Request;

final class CertificateDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $certificate_number,
        public readonly CertificateType $type,
        public readonly string $student_uuid,
        public readonly CertificateStatus $status = CertificateStatus::DRAFT,
        public readonly ?string $template_uuid = null,
        public readonly ?string $issued_date = null,
        public readonly ?string $valid_until = null,
        public readonly ?string $signature_uuid = null,
        public readonly ?string $qr_code = null,
        public readonly ?string $digital_signature = null,
        public readonly ?string $document_path = null,
        public readonly ?string $remarks = null,
        public readonly ?string $issued_by = null,
        public readonly VerificationStatus $verification_status = VerificationStatus::PENDING,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            certificate_number: $request->input('certificate_number'),
            type: CertificateType::from($request->input('type')),
            student_uuid: $request->input('student_uuid'),
            status: CertificateStatus::tryFrom($request->input('status', 'draft')) ?? CertificateStatus::DRAFT,
            template_uuid: $request->input('template_uuid'),
            issued_date: $request->input('issued_date'),
            valid_until: $request->input('valid_until'),
            signature_uuid: $request->input('signature_uuid'),
            qr_code: $request->input('qr_code'),
            digital_signature: $request->input('digital_signature'),
            document_path: $request->input('document_path'),
            remarks: $request->input('remarks'),
            issued_by: $request->input('issued_by'),
            verification_status: VerificationStatus::tryFrom($request->input('verification_status', 'pending')) ?? VerificationStatus::PENDING,
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'certificate_number' => $this->certificate_number,
            'type' => $this->type->value,
            'student_uuid' => $this->student_uuid,
            'status' => $this->status->value,
            'template_uuid' => $this->template_uuid,
            'issued_date' => $this->issued_date,
            'valid_until' => $this->valid_until,
            'signature_uuid' => $this->signature_uuid,
            'qr_code' => $this->qr_code,
            'digital_signature' => $this->digital_signature,
            'document_path' => $this->document_path,
            'remarks' => $this->remarks,
            'issued_by' => $this->issued_by,
            'verification_status' => $this->verification_status->value,
        ];
    }
}
