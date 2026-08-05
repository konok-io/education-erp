<?php

declare(strict_types=1);

namespace App\Http\Resources\HR;

use App\Http\Resources\BaseResource;

class TaxSlabResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'fiscal_year' => $this->fiscal_year,
            'min_income' => $this->min_income,
            'max_income' => $this->max_income,
            'rate_percent' => $this->rate_percent,
            'fixed_amount' => $this->fixed_amount,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
