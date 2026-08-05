<?php

declare(strict_types=1);

namespace App\Http\Resources\Inventory;

use App\Models\Inventory\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'movement_no' => $this->movement_no,
            'product_id' => $this->product?->uuid,
            'product' => $this->product ? [
                'id' => $this->product->uuid,
                'name' => $this->product->name,
                'sku' => $this->product->sku,
            ] : null,
            'warehouse_id' => $this->warehouse?->uuid,
            'warehouse' => $this->warehouse ? [
                'id' => $this->warehouse->uuid,
                'name' => $this->warehouse->name,
            ] : null,
            'from_warehouse_id' => $this->fromWarehouse?->uuid,
            'from_warehouse' => $this->fromWarehouse ? [
                'id' => $this->fromWarehouse->uuid,
                'name' => $this->fromWarehouse->name,
            ] : null,
            'to_warehouse_id' => $this->toWarehouse?->uuid,
            'to_warehouse' => $this->toWarehouse ? [
                'id' => $this->toWarehouse->uuid,
                'name' => $this->toWarehouse->name,
            ] : null,
            'movement_type' => $this->movement_type,
            'movement_type_label' => $this->getMovementTypeLabel(),
            'is_incoming' => $this->isIncoming(),
            'quantity' => $this->quantity,
            'opening_stock' => $this->opening_stock,
            'closing_stock' => $this->closing_stock,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'movement_date' => $this->movement_date,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'remarks' => $this->remarks,
            'created_by' => $this->createdBy ? [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
            ] : null,
            'created_at' => $this->created_at,
        ];
    }

    protected function getMovementTypeLabel(): string
    {
        $labels = [
            StockMovement::TYPE_PURCHASE => 'Purchase',
            StockMovement::TYPE_SALE => 'Sale',
            StockMovement::TYPE_STOCK_IN => 'Stock In',
            StockMovement::TYPE_STOCK_OUT => 'Stock Out',
            StockMovement::TYPE_TRANSFER_IN => 'Transfer In',
            StockMovement::TYPE_TRANSFER_OUT => 'Transfer Out',
            StockMovement::TYPE_ADJUSTMENT_IN => 'Adjustment In',
            StockMovement::TYPE_ADJUSTMENT_OUT => 'Adjustment Out',
            StockMovement::TYPE_RETURN_IN => 'Return In',
            StockMovement::TYPE_RETURN_OUT => 'Return Out',
            StockMovement::TYPE_DAMAGE => 'Damage',
            StockMovement::TYPE_LOSS => 'Loss',
        ];

        return $labels[$this->movement_type] ?? $this->movement_type;
    }
}
