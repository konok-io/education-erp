<?php

declare(strict_types=1);

namespace App\Http\Resources\Finance;

use App\Http\Resources\BaseResource;

class JournalEntryResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'voucher_no' => $this->voucher_no,
            'voucher_type' => $this->voucher_type,
            'entry_date' => $this->entry_date?->toDateString(),
            'reference' => $this->reference,
            'description' => $this->description,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'is_posted' => $this->is_posted,
            'posted_at' => $this->posted_at?->toIso8601String(),
            'approved_at' => $this->approved_at?->toIso8601String(),
            'remarks' => $this->remarks,

            'fiscal_year' => $this->whenLoaded('fiscalYear', fn() => [
                'id' => $this->fiscalYear?->uuid,
                'name' => $this->fiscalYear?->name,
            ]),

            'details' => $this->whenLoaded('details', fn() =>
                $this->details->map(fn($d) => [
                    'id' => $d->uuid,
                    'account' => $d->account ? [
                        'id' => $d->account->uuid,
                        'name' => $d->account->account_name,
                        'code' => $d->account->account_code,
                    ] : null,
                    'dr_cr' => $d->dr_cr,
                    'amount' => $d->amount,
                    'narration' => $d->narration,
                ])
            ),

            'creator' => $this->whenLoaded('creator', fn() => [
                'id' => $this->creator?->uuid,
                'name' => $this->creator?->name,
            ]),

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
