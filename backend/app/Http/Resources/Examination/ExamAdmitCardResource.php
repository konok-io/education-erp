<?php

declare(strict_types=1);

namespace App\Http\Resources\Examination;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamAdmitCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'admit_card_no' => $this->admit_card_no,
            'exam_id' => $this->exam?->uuid,
            'exam' => $this->exam ? [
                'id' => $this->exam->uuid,
                'exam_name' => $this->exam->exam_name,
                'exam_code' => $this->exam->exam_code,
            ] : null,
            'student_id' => $this->student_id,
            'student_name' => $this->student_name,
            'student_roll' => $this->student_roll,
            'registration_no' => $this->registration_no,
            'class_name' => $this->class_name,
            'section' => $this->section,
            'photo' => $this->photo,
            'signature' => $this->signature,
            'qr_code' => $this->qr_code,
            'barcode' => $this->barcode,
            'verification_url' => url("/api/v1/examinations/admit-card/verify/{$this->verification_token}"),
            'issue_date' => $this->issue_date,
            'valid_until' => $this->valid_until,
            'status' => $this->status,
            'status_label' => \App\Models\Examination\ExamAdmitCard::statuses()[$this->status] ?? $this->status,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
