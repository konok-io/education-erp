<?php

declare(strict_types=1);

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'qr_code' => $this->qr_code,
            'name' => $this->name,
            'short_name' => $this->short_name,
            'name_bn' => $this->name_bn,
            'category_id' => $this->category?->uuid,
            'category' => [
                'id' => $this->category?->uuid,
                'name' => $this->category?->name,
            ],
            'brand_id' => $this->brand?->uuid,
            'brand' => [
                'id' => $this->brand?->uuid,
                'name' => $this->brand?->name,
            ],
            'unit_id' => $this->unit?->uuid,
            'unit' => [
                'id' => $this->unit?->uuid,
                'name' => $this->unit?->name,
                'short_code' => $this->unit?->short_code,
            ],
            'model' => $this->model,
            'description' => $this->description,
            'specifications' => $this->specifications,
            'image' => $this->image,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'min_stock' => $this->min_stock,
            'max_stock' => $this->max_stock,
            'reorder_level' => $this->reorder_level,
            'current_stock' => $this->current_stock,
            'is_low_stock' => $this->isLowStock(),
            'is_out_of_stock' => $this->isOutOfStock(),
            'weight' => $this->weight,
            'dimensions' => $this->dimensions,
            'color' => $this->color,
            'size' => $this->size,
            'is_trackable' => $this->is_trackable,
            'is_sellable' => $this->is_sellable,
            'is_purchasable' => $this->is_purchasable,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
