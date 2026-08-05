<?php

declare(strict_types=1);

namespace App\Http\Resources\Certificate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TranscriptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'transcript_number' => $this->transcript_number,
            'student_id' => $this->student_id,
            'student_name' => $this->student_name,
            'student_roll' => $this->student_roll,
            'registration_no' => $this->registration_no,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'department' => $this->department,
            'program' => $this->program,
            'session' => $this->session,
            'duration' => $this->duration,
            'semester_results' => $this->semester_results,
            'total_credits' => $this->total_credits,
            'cgpa' => $this->cgpa,
            'gpa' => $this->gpa,
            'result_status' => $this->result_status,
            'result_status_label' => \App\Models\Certificate\Transcript::resultStatuses()[$this->result_status] ?? $this->result_status,
            'remarks' => $this->remarks,
            'verification_url' => url("/api/v1/transcripts/verify/{$this->verification_token}"),
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
            'status' => $this->status,
            'status_label' => \App\Models\Certificate\Transcript::statuses()[$this->status] ?? $this->status,
            'approver' => $this->approver ? [
                'id' => $this->approver->id,
                'name' => $this->approver->name,
            ] : null,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
