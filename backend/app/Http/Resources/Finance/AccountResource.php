<?php

declare(strict_types=1);

namespace App\Http\Resources\Finance;

use App\Http\Resources\BaseResource;

class AccountResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'account_code' => $this->account_code,
            'account_name' => $this->account_name,
            'account_name_bn' => $this->account_name_bn,
            'account_type' => $this->account_type,
            'account_group' => $this->account_group,
            'opening_balance' => $this->opening_balance,
            'current_balance' => $this->current_balance,
            'dr_cr' => $this->dr_cr,
            'is_bank' => $this->is_bank,
            'is_cash' => $this->is_cash,
            'is_active' => $this->is_active,
            'is_system' => $this->is_system,

            'parent' => $this->whenLoaded('parent', fn() => [
                'id' => $this->parent?->uuid,
                'name' => $this->parent?->account_name,
            ]),

            'children' => $this->whenLoaded('children', fn() =>
                $this->children->map(fn($c) => [
                    'id' => $c->uuid,
                    'name' => $c->account_name,
                    'code' => $c->account_code,
                ])
            ),

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
