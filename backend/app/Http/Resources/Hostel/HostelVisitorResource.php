<?php

declare(strict_types=1);

namespace App\Http\Resources\Hostel;

use App\Models\Hostel\HostelVisitor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HostelVisitorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'visitor_no' => $this->visitor_no,
            'visitor_name' => $this->visitor_name,
            'nid' => $this->nid,
            'phone' => $this->phone,
            'relation' => $this->relation,
            'purpose' => $this->purpose,
            'purpose_label' => HostelVisitor::purposes()[$this->purpose] ?? $this->purpose,
            'hostel_id' => $this->hostel?->uuid,
            'hostel' => $this->hostel ? [
                'id' => $this->hostel->uuid,
                'hostel_name' => $this->hostel->hostel_name,
            ] : null,
            'student_id' => $this->student_id,
            'student_name' => $this->student_name,
            'student_class' => $this->student_class,
            'student_roll' => $this->student_roll,
            'visit_date' => $this->visit_date,
            'check_in_time' => $this->check_in_time,
            'check_out_time' => $this->check_out_time,
            'remarks' => $this->remarks,
            'status' => $this->status,
            'status_label' => HostelVisitor::statuses()[$this->status] ?? $this->status,
            'approved_by' => $this->approver ? [
                'id' => $this->approver->id,
                'name' => $this->approver->name,
            ] : null,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
