<?php

declare(strict_types=1);

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'code' => $this->code,
            'company_name' => $this->company_name,
            'contact_person' => $this->contact_person,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'trade_license' => $this->trade_license,
            'tin' => $this->tin,
            'bin' => $this->bin,
            'vat_number' => $this->vat_number,
            'website' => $this->website,
            'bank_name' => $this->bank_name,
            'bank_account' => $this->bank_account,
            'credit_limit' => $this->credit_limit,
            'payment_days' => $this->payment_days,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
