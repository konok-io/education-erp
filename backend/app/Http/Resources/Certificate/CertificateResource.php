<?php

declare(strict_types=1);

namespace App\Http\Resources\Certificate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'certificate_number' => $this->certificate_number,
            'certificate_type' => $this->certificate_type,
            'certificate_type_label' => \App\Models\Certificate\Certificate::certificateTypes()[$this->certificate_type] ?? $this->certificate_type,
            'template' => $this->template ? [
                'id' => $this->template->uuid,
                'template_name' => $this->template->template_name,
            ] : null,
            'student_id' => $this->student_id,
            'student_name' => $this->student_name,
            'student_roll' => $this->student_roll,
            'registration_no' => $this->registration_no,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'department' => $this->department,
            'class_name' => $this->class_name,
            'section' => $this->section,
            'session' => $this->session,
            'semester' => $this->semester,
            'academic_year' => $this->academic_year,
            'content' => $this->content,
            'metadata' => $this->metadata,
            'verification_url' => url("/api/v1/certificates/verify/{$this->verification_token}"),
            'pdf_path' => $this->pdf_path,
            'signature' => $this->signature ? [
                'id' => $this->signature->uuid,
                'signatory_name' => $this->signature->signatory_name,
                'designation' => $this->signature->designation,
            ] : null,
            'seal' => $this->seal ? [
                'id' => $this->seal->uuid,
                'seal_name' => $this->seal->seal_name,
            ] : null,
            'issue_date' => $this->issue_date,
            'valid_until' => $this->valid_until,
            'reason' => $this->reason,
            'conduct' => $this->conduct,
            'status' => $this->status,
            'status_label' => \App\Models\Certificate\Certificate::statuses()[$this->status] ?? $this->status,
            'approver' => $this->approver ? [
                'id' => $this->approver->id,
                'name' => $this->approver->name,
            ] : null,
            'approved_at' => $this->approved_at,
            'issuer' => $this->issuer ? [
                'id' => $this->issuer->id,
                'name' => $this->issuer->name,
            ] : null,
            'issued_at' => $this->issued_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
