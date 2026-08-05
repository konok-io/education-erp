<?php

declare(strict_types=1);

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'po_no' => $this->po_no,
            'supplier_id' => $this->supplier?->uuid,
            'supplier' => $this->supplier ? [
                'id' => $this->supplier->uuid,
                'code' => $this->supplier->code,
                'name' => $this->supplier->company_name,
            ] : null,
            'warehouse_id' => $this->warehouse?->uuid,
            'warehouse' => $this->warehouse ? [
                'id' => $this->warehouse->uuid,
                'name' => $this->warehouse->name,
            ] : null,
            'purchase_request_id' => $this->purchaseRequest?->uuid,
            'order_date' => $this->order_date,
            'expected_delivery_date' => $this->expected_delivery_date,
            'actual_delivery_date' => $this->actual_delivery_date,
            'payment_terms' => $this->payment_terms,
            'delivery_terms' => $this->delivery_terms,
            'subtotal' => $this->subtotal,
            'discount_percent' => $this->discount_percent,
            'discount_amount' => $this->discount_amount,
            'vat_percent' => $this->vat_percent,
            'vat_amount' => $this->vat_amount,
            'shipping_cost' => $this->shipping_cost,
            'total' => $this->total,
            'notes' => $this->notes,
            'status' => $this->status,
            'created_by' => $this->createdBy ? [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
            ] : null,
            'approved_by' => $this->approvedBy ? [
                'id' => $this->approvedBy->id,
                'name' => $this->approvedBy->name,
            ] : null,
            'approved_at' => $this->approved_at,
            'items' => PurchaseOrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
