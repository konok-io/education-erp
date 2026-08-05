<?php

declare(strict_types=1);

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product?->uuid,
            'product' => $this->product ? [
                'id' => $this->product->uuid,
                'name' => $this->product->name,
                'sku' => $this->product->sku,
            ] : null,
            'product_name' => $this->product_name,
            'specifications' => $this->specifications,
            'ordered_quantity' => $this->ordered_quantity,
            'received_quantity' => $this->received_quantity,
            'remaining_quantity' => $this->remainingQuantity(),
            'rejected_quantity' => $this->rejected_quantity,
            'unit_price' => $this->unit_price,
            'discount_percent' => $this->discount_percent,
            'discount_amount' => $this->discount_amount,
            'vat_percent' => $this->vat_percent,
            'vat_amount' => $this->vat_amount,
            'total' => $this->total,
            'remarks' => $this->remarks,
        ];
    }
}
