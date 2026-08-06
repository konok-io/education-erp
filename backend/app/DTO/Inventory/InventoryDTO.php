<?php

declare(strict_types=1);

namespace App\DTO\Inventory;

use App\Enums\Inventory\InventoryType;
use App\Enums\Inventory\StockStatus;
use Illuminate\Http\Request;

final class InventoryDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $item_code,
        public readonly string $name,
        public readonly InventoryType $type = InventoryType::OTHER,
        public readonly ?string $description,
        public readonly ?string $brand,
        public readonly ?string $model,
        public readonly ?string $supplier_uuid,
        public readonly int $quantity = 0,
        public readonly int $min_quantity = 0,
        public readonly int $max_quantity = 0,
        public readonly ?float $unit_price,
        public readonly ?string $unit,
        public readonly StockStatus $stock_status = StockStatus::IN_STOCK,
        public readonly ?string $location,
        public readonly ?string $warranty_expiry,
        public readonly ?string $photo,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            item_code: $request->input('item_code'),
            name: $request->input('name'),
            type: InventoryType::tryFrom($request->input('type', 'other')) ?? InventoryType::OTHER,
            description: $request->input('description'),
            brand: $request->input('brand'),
            model: $request->input('model'),
            supplier_uuid: $request->input('supplier_uuid'),
            quantity: (int) $request->input('quantity', 0),
            min_quantity: (int) $request->input('min_quantity', 0),
            max_quantity: (int) $request->input('max_quantity', 0),
            unit_price: $request->input('unit_price') ? (float) $request->input('unit_price') : null,
            unit: $request->input('unit'),
            stock_status: StockStatus::tryFrom($request->input('stock_status', 'in_stock')) ?? StockStatus::IN_STOCK,
            location: $request->input('location'),
            warranty_expiry: $request->input('warranty_expiry'),
            photo: $request->input('photo'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'item_code' => $this->item_code,
            'name' => $this->name,
            'type' => $this->type->value,
            'description' => $this->description,
            'brand' => $this->brand,
            'model' => $this->model,
            'supplier_uuid' => $this->supplier_uuid,
            'quantity' => $this->quantity,
            'min_quantity' => $this->min_quantity,
            'max_quantity' => $this->max_quantity,
            'unit_price' => $this->unit_price,
            'unit' => $this->unit,
            'stock_status' => $this->stock_status->value,
            'location' => $this->location,
            'warranty_expiry' => $this->warranty_expiry,
            'photo' => $this->photo,
        ];
    }
}
