<?php

declare(strict_types=1);

namespace App\Http\Resources\Library;

use App\Http\Resources\BaseResource;

class LibraryFineResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'fine_no' => $this->fine_no,
            'fine_type' => $this->fine_type,
            'fine_type_label' => $this->fine_type ? \App\Models\Library\LibraryFine::fineTypes()[$this->fine_type] ?? $this->fine_type : null,
            'reason' => $this->reason,
            'amount' => $this->amount,
            'paid_amount' => $this->paid_amount,
            'waived_amount' => $this->waived_amount,
            'remaining_amount' => $this->getRemainingAmount(),
            'fine_date' => $this->fine_date?->toDateString(),
            'due_date' => $this->due_date?->toDateString(),
            'paid_date' => $this->paid_date?->toDateString(),
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
            
            'member' => $this->whenLoaded('member', fn() => [
                'id' => $this->member?->uuid,
                'member_no' => $this->member?->member_no,
                'name' => $this->member?->name,
            ]),
            
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
