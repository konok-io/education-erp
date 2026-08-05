<?php

declare(strict_types=1);

namespace App\Http\Resources\Examination;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamMarkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'exam_subject_id' => $this->subject?->uuid,
            'subject' => $this->subject ? [
                'id' => $this->subject->uuid,
                'subject_name' => $this->subject->subject_name,
                'full_marks' => $this->subject->full_marks,
                'pass_marks' => $this->subject->pass_marks,
            ] : null,
            'student_id' => $this->student_id,
            'student_name' => $this->student_name,
            'student_roll' => $this->student_roll,
            'theory_marks' => $this->theory_marks,
            'practical_marks' => $this->practical_marks,
            'total_marks' => $this->total_marks,
            'pass_marks' => $this->pass_marks,
            'result' => $this->result,
            'grade' => $this->grade,
            'teacher_remarks' => $this->teacher_remarks,
            'moderator_remarks' => $this->moderator_remarks,
            'status' => $this->status,
            'status_label' => \App\Models\Examination\ExamMark::statuses()[$this->status] ?? $this->status,
            'entered_by' => $this->entryBy ? [
                'id' => $this->entryBy->id,
                'name' => $this->entryBy->name,
            ] : null,
            'verified_by' => $this->verifier ? [
                'id' => $this->verifier->id,
                'name' => $this->verifier->name,
            ] : null,
            'approved_by' => $this->approver ? [
                'id' => $this->approver->id,
                'name' => $this->approver->name,
            ] : null,
            'entered_at' => $this->entered_at,
            'verified_at' => $this->verified_at,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
