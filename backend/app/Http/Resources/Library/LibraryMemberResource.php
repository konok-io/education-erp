<?php

declare(strict_types=1);

namespace App\Http\Resources\Library;

use App\Http\Resources\BaseResource;

class LibraryMemberResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'member_no' => $this->member_no,
            'member_type' => $this->member_type,
            'member_type_label' => $this->member_type ? \App\Models\Library\LibraryMember::memberTypes()[$this->member_type] ?? $this->member_type : null,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'photo' => $this->photo,
            'department' => $this->department,
            'student_id' => $this->student_id,
            'employee_id' => $this->employee_id,
            'joining_date' => $this->joining_date?->toDateString(),
            'expiry_date' => $this->expiry_date?->toDateString(),
            'status' => $this->status,
            'max_books' => $this->max_books,
            'max_days' => $this->max_days,
            'fine_rate' => $this->fine_rate,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            
            'issued_books_count' => $this->when(isset($this->issued_books_count), fn() => $this->issued_books_count),
            'unpaid_fines' => $this->when(isset($this->unpaid_fines), fn() => $this->unpaid_fines),
            
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
