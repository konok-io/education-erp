<?php

declare(strict_types=1);

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'type_label' => Warehouse::types()[$this->type] ?? $this->type,
            'address' => $this->address,
            'building' => $this->building,
            'floor' => $this->floor,
            'manager_name' => $this->manager_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
