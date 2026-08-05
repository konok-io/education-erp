<?php

declare(strict_types=1);

namespace App\Http\Resources\Inventory;

use App\Models\Inventory\Asset;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'asset_code' => $this->asset_code,
            'product_id' => $this->product?->uuid,
            'product' => $this->product ? [
                'id' => $this->product->uuid,
                'name' => $this->product->name,
                'sku' => $this->product->sku,
            ] : null,
            'asset_name' => $this->asset_name,
            'serial_number' => $this->serial_number,
            'barcode' => $this->barcode,
            'qr_code' => $this->qr_code,
            'category' => $this->category,
            'category_label' => Asset::categories()[$this->category] ?? $this->category,
            'warehouse_id' => $this->warehouse?->uuid,
            'warehouse' => $this->warehouse ? [
                'id' => $this->warehouse->uuid,
                'name' => $this->warehouse->name,
            ] : null,
            'assigned_to_type' => $this->assigned_to_type,
            'assigned_to_id' => $this->assigned_to_id,
            'assigned_to_name' => $this->assigned_to_name,
            'purchase_date' => $this->purchase_date,
            'purchase_cost' => $this->purchase_cost,
            'warranty_expiry' => $this->warranty_expiry,
            'is_under_warranty' => $this->isUnderWarranty(),
            'supplier' => $this->supplier,
            'location' => $this->location,
            'condition' => $this->condition,
            'status' => $this->status,
            'status_label' => Asset::statuses()[$this->status] ?? $this->status,
            'description' => $this->description,
            'notes' => $this->notes,
            'depreciation_rate' => $this->depreciation_rate,
            'current_value' => $this->current_value,
            'disposal_date' => $this->disposal_date,
            'disposal_value' => $this->disposal_value,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
