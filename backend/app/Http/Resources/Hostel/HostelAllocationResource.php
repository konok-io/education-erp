<?php

declare(strict_types=1);

namespace App\Http\Resources\Hostel;

use App\Models\Hostel\HostelAllocation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HostelAllocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'allocation_no' => $this->allocation_no,
            'allocatable_type' => $this->allocatable_type,
            'allocatable_id' => $this->allocatable_id,
            'hostel_id' => $this->hostel?->uuid,
            'hostel' => $this->hostel ? [
                'id' => $this->hostel->uuid,
                'hostel_name' => $this->hostel->hostel_name,
            ] : null,
            'building_id' => $this->building?->uuid,
            'building' => $this->building ? [
                'id' => $this->building->uuid,
                'building_name' => $this->building->building_name,
            ] : null,
            'room_id' => $this->room?->uuid,
            'room' => $this->room ? [
                'id' => $this->room->uuid,
                'room_number' => $this->room->room_number,
                'room_code' => $this->room->room_code,
            ] : null,
            'bed_id' => $this->bed?->uuid,
            'bed' => $this->bed ? [
                'id' => $this->bed->uuid,
                'bed_number' => $this->bed->bed_number,
                'bed_code' => $this->bed->bed_code,
            ] : null,
            'check_in_date' => $this->check_in_date,
            'expected_checkout' => $this->expected_checkout,
            'actual_checkout' => $this->actual_checkout,
            'monthly_fee' => $this->monthly_fee,
            'security_deposit' => $this->security_deposit,
            'total_paid' => $this->total_paid,
            'status' => $this->status,
            'status_label' => HostelAllocation::statuses()[$this->status] ?? $this->status,
            'remarks' => $this->remarks,
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
