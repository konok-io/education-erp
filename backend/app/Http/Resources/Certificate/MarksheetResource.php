<?php

declare(strict_types=1);

namespace App\Http\Resources\Certificate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarksheetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'marksheet_number' => $this->marksheet_number,
            'student_id' => $this->student_id,
            'student_name' => $this->student_name,
            'student_roll' => $this->student_roll,
            'registration_no' => $this->registration_no,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'department' => $this->department,
            'class_name' => $this->class_name,
            'session' => $this->session,
            'semester' => $this->semester,
            'subject_marks' => $this->subject_marks,
            'total_marks' => $this->total_marks,
            'obtained_marks' => $this->obtained_marks,
            'grade' => $this->grade,
            'gpa' => $this->gpa,
            'result_status' => $this->result_status,
            'result_status_label' => \App\Models\Certificate\Marksheet::resultStatuses()[$this->result_status] ?? $this->result_status,
            'remarks' => $this->remarks,
            'verification_url' => url("/api/v1/marksheets/verify/{$this->verification_token}"),
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
            'status_label' => \App\Models\Certificate\Marksheet::statuses()[$this->status] ?? $this->status,
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
