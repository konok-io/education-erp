<?php

declare(strict_types=1);

namespace App\Services\Inventory;

use App\Models\Inventory\Asset;
use App\Models\Inventory\AssetMaintenance;
use App\Models\Inventory\AssetTransfer;
use App\Models\Inventory\GoodsReceivedNote;
use App\Models\Inventory\GoodsReceivedNoteItem;
use App\Models\Inventory\Product;
use App\Models\Inventory\ProductBrand;
use App\Models\Inventory\ProductCategory;
use App\Models\Inventory\ProductUnit;
use App\Models\Inventory\PurchaseOrder;
use App\Models\Inventory\PurchaseOrderItem;
use App\Models\Inventory\PurchaseRequest;
use App\Models\Inventory\PurchaseRequestItem;
use App\Models\Inventory\StockMovement;
use App\Models\Inventory\Supplier;
use App\Models\Inventory\Warehouse;
use App\Models\Inventory\StockTransfer;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    // ===================== PRODUCT METHODS =====================

    public function getProducts(array $filters = []): LengthAwarePaginator
    {
        $query = Product::with(['category', 'brand', 'unit']);

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (!empty($filters['is_low_stock'])) {
            $query->lowStock();
        }

        if (!empty($filters['is_out_of_stock'])) {
            $query->outOfStock();
        }

        $query->active();

        return $query->orderBy('name')->paginate($filters['per_page'] ?? 20);
    }

    public function createProduct(array $data): Product
    {
        $data['sku'] = Product::generateSKU();
        $data['barcode'] = Product::generateBarcode();
        $data['qr_code'] = Product::generateQRCode();

        return Product::create($data);
    }

    public function updateProduct(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh();
    }

    public function deleteProduct(Product $product): void
    {
        $product->delete();
    }

    // ===================== WAREHOUSE METHODS =====================

    public function getWarehouses(array $filters = []): LengthAwarePaginator
    {
        $query = Warehouse::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        $query->where('is_active', true);

        return $query->orderBy('name')->paginate($filters['per_page'] ?? 20);
    }

    public function createWarehouse(array $data): Warehouse
    {
        return Warehouse::create($data);
    }

    public function updateWarehouse(Warehouse $warehouse, array $data): Warehouse
    {
        $warehouse->update($data);
        return $warehouse->fresh();
    }

    public function deleteWarehouse(Warehouse $warehouse): void
    {
        $warehouse->delete();
    }

    // ===================== SUPPLIER METHODS =====================

    public function getSuppliers(array $filters = []): LengthAwarePaginator
    {
        $query = Supplier::query();

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        $query->where('is_active', true);

        return $query->orderBy('company_name')->paginate($filters['per_page'] ?? 20);
    }

    public function createSupplier(array $data): Supplier
    {
        $data['code'] = 'SUP' . str_pad(Supplier::count() + 1, 4, '0', STR_PAD_LEFT);
        return Supplier::create($data);
    }

    public function updateSupplier(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);
        return $supplier->fresh();
    }

    // ===================== PURCHASE REQUEST METHODS =====================

    public function createPurchaseRequest(array $data): PurchaseRequest
    {
        $data['pr_no'] = PurchaseRequest::generatePRNo();

        return DB::transaction(function () use ($data) {
            $pr = PurchaseRequest::create($data);

            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $item['purchase_request_id'] = $pr->id;
                    $item['estimated_amount'] = $item['quantity'] * ($item['estimated_rate'] ?? 0);
                    PurchaseRequestItem::create($item);
                }

                $pr->update(['estimated_total' => $pr->items->sum('estimated_amount')]);
            }

            return $pr->load('items');
        });
    }

    public function approvePurchaseRequest(PurchaseRequest $pr, int $userId): PurchaseRequest
    {
        $pr->approve($userId);
        return $pr->fresh();
    }

    // ===================== PURCHASE ORDER METHODS =====================

    public function createPurchaseOrder(array $data): PurchaseOrder
    {
        $data['po_no'] = PurchaseOrder::generatePONo();

        return DB::transaction(function () use ($data) {
            $po = PurchaseOrder::create($data);

            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $item['purchase_order_id'] = $po->id;
                    $item['total'] = $item['ordered_quantity'] * $item['unit_price'];
                    PurchaseOrderItem::create($item);
                }
            }

            $po->calculateTotals();

            return $po->load('items');
        });
    }

    public function updatePurchaseOrder(PurchaseOrder $po, array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($po, $data) {
            $po->update($data);

            if (!empty($data['items'])) {
                $po->items()->delete();
                foreach ($data['items'] as $item) {
                    $item['purchase_order_id'] = $po->id;
                    $item['total'] = $item['ordered_quantity'] * $item['unit_price'];
                    PurchaseOrderItem::create($item);
                }
            }

            $po->calculateTotals();

            return $po->fresh()->load('items');
        });
    }

    public function approvePurchaseOrder(PurchaseOrder $po, int $userId): PurchaseOrder
    {
        $po->approve($userId);
        return $po->fresh();
    }

    // ===================== GOODS RECEIVED NOTE METHODS =====================

    public function createGoodsReceivedNote(array $data): GoodsReceivedNote
    {
        $data['grn_no'] = GoodsReceivedNote::generateGRNNo();

        return DB::transaction(function () use ($data) {
            $grn = GoodsReceivedNote::create($data);

            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $item['goods_received_note_id'] = $grn->id;
                    $item['total'] = $item['accepted_quantity'] * $item['unit_price'];
                    GoodsReceivedNoteItem::create($item);

                    // Update product stock
                    if ($item['product_id'] && $item['accepted_quantity'] > 0) {
                        $product = Product::find($item['product_id']);
                        $product->addStock((float) $item['accepted_quantity']);

                        // Create stock movement
                        $this->createStockMovement([
                            'product_id' => $item['product_id'],
                            'warehouse_id' => $data['warehouse_id'],
                            'movement_type' => StockMovement::TYPE_PURCHASE,
                            'quantity' => $item['accepted_quantity'],
                            'unit_cost' => $item['unit_price'],
                            'total_cost' => $item['accepted_quantity'] * $item['unit_price'],
                            'movement_date' => $data['received_date'],
                            'reference_type' => GoodsReceivedNote::class,
                            'reference_id' => $grn->id,
                        ]);
                    }

                    // Update PO item received quantity
                    if (!empty($item['purchase_order_item_id'])) {
                        $poItem = PurchaseOrderItem::find($item['purchase_order_item_id']);
                        $poItem->received_quantity += $item['accepted_quantity'];
                        $poItem->rejected_quantity += $item['rejected_quantity'];
                        $poItem->save();
                    }
                }
            }

            // Update GRN total
            $grn->update(['total' => $grn->items->sum('total')]);

            return $grn->load('items');
        });
    }

    // ===================== STOCK MOVEMENT METHODS =====================

    public function createStockMovement(array $data): StockMovement
    {
        $data['movement_no'] = StockMovement::generateMovementNo();

        if (empty($data['created_by']) && auth()->check()) {
            $data['created_by'] = auth()->id();
        }

        $movement = StockMovement::create($data);

        // Update product stock
        if ($data['product_id']) {
            $product = Product::find($data['product_id']);
            
            if ($movement->isIncoming()) {
                $data['opening_stock'] = $product->current_stock;
                $product->addStock((float) $data['quantity']);
                $data['closing_stock'] = $product->fresh()->current_stock;
            } else {
                $data['opening_stock'] = $product->current_stock;
                $product->removeStock((float) $data['quantity']);
                $data['closing_stock'] = $product->fresh()->current_stock;
            }

            $movement->update([
                'opening_stock' => $data['opening_stock'],
                'closing_stock' => $data['closing_stock'],
            ]);
        }

        return $movement;
    }

    public function transferStock(int $productId, int $fromWarehouseId, int $toWarehouseId, float $quantity, string $remarks = ''): array
    {
        return DB::transaction(function () use ($productId, $fromWarehouseId, $toWarehouseId, $quantity, $remarks) {
            $product = Product::findOrFail($productId);

            // Create transfer out movement
            $this->createStockMovement([
                'product_id' => $productId,
                'from_warehouse_id' => $fromWarehouseId,
                'to_warehouse_id' => $toWarehouseId,
                'movement_type' => StockMovement::TYPE_TRANSFER_OUT,
                'quantity' => $quantity,
                'movement_date' => now(),
                'remarks' => $remarks,
            ]);

            // Create transfer in movement
            $this->createStockMovement([
                'product_id' => $productId,
                'from_warehouse_id' => $fromWarehouseId,
                'to_warehouse_id' => $toWarehouseId,
                'movement_type' => StockMovement::TYPE_TRANSFER_IN,
                'quantity' => $quantity,
                'movement_date' => now(),
                'remarks' => $remarks,
            ]);

            return [
                'success' => true,
                'message' => 'Stock transferred successfully',
            ];
        });
    }

    public function adjustStock(int $productId, int $warehouseId, float $quantity, string $type, string $remarks = ''): array
    {
        $movementType = $type === 'increase' 
            ? StockMovement::TYPE_ADJUSTMENT_IN 
            : StockMovement::TYPE_ADJUSTMENT_OUT;

        $this->createStockMovement([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'movement_type' => $movementType,
            'quantity' => abs($quantity),
            'movement_date' => now(),
            'remarks' => $remarks,
        ]);

        return [
            'success' => true,
            'message' => 'Stock adjusted successfully',
        ];
    }

    public function getStockMovements(array $filters = []): LengthAwarePaginator
    {
        $query = StockMovement::with(['product', 'warehouse', 'fromWarehouse', 'toWarehouse']);

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (!empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        if (!empty($filters['movement_type'])) {
            $query->where('movement_type', $filters['movement_type']);
        }

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->byDateRange($filters['date_from'], $filters['date_to']);
        }

        return $query->orderByDesc('movement_date')->paginate($filters['per_page'] ?? 20);
    }

    // ===================== ASSET METHODS =====================

    public function getAssets(array $filters = []): LengthAwarePaginator
    {
        $query = Asset::with(['warehouse']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('asset_name', 'like', "%{$filters['search']}%")
                  ->orWhere('asset_code', 'like', "%{$filters['search']}%")
                  ->orWhere('serial_number', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->active();

        return $query->orderBy('asset_name')->paginate($filters['per_page'] ?? 20);
    }

    public function createAsset(array $data): Asset
    {
        $data['asset_code'] = Asset::generateAssetCode();
        $data['barcode'] = Asset::generateBarcode();
        $data['qr_code'] = Asset::generateQRCode();

        return Asset::create($data);
    }

    public function allocateAsset(Asset $asset, string $holderType, int $holderId, string $holderName): Asset
    {
        $asset->allocate($holderType, $holderId, $holderName);
        return $asset->fresh();
    }

    public function transferAsset(Asset $asset, array $data): AssetTransfer
    {
        return DB::transaction(function () use ($asset, $data) {
            $data['transfer_no'] = AssetTransfer::generateTransferNo();
            $data['asset_id'] = $asset->id;
            $data['from_holder_type'] = $asset->assigned_to_type;
            $data['from_holder_id'] = $asset->assigned_to_id;
            $data['from_holder_name'] = $asset->assigned_to_name;
            $data['from_location'] = $asset->location;
            $data['requested_by'] = auth()->id();

            return AssetTransfer::create($data);
        });
    }

    public function completeAssetTransfer(AssetTransfer $transfer): void
    {
        $transfer->complete(auth()->id());
    }

    // ===================== ASSET MAINTENANCE METHODS =====================

    public function createMaintenance(array $data): AssetMaintenance
    {
        $data['maintenance_no'] = AssetMaintenance::generateMaintenanceNo();
        $data['created_by'] = auth()->id();

        return AssetMaintenance::create($data);
    }

    public function completeMaintenance(AssetMaintenance $maintenance, string $workDone): AssetMaintenance
    {
        $maintenance->complete($workDone);
        
        if ($maintenance->cost) {
            // Could integrate with accounting here
        }

        return $maintenance->fresh();
    }

    // ===================== DASHBOARD METHODS =====================

    public function getDashboardData(): array
    {
        return [
            'total_products' => Product::active()->count(),
            'total_warehouses' => Warehouse::active()->count(),
            'total_suppliers' => Supplier::active()->count(),
            'total_assets' => Asset::active()->count(),
            'low_stock_products' => Product::active()->lowStock()->count(),
            'out_of_stock_products' => Product::active()->outOfStock()->count(),
            'pending_purchase_orders' => PurchaseOrder::active()->where('status', PurchaseOrder::STATUS_PENDING)->count(),
            'pending_asset_transfers' => AssetTransfer::pending()->count(),
            'scheduled_maintenances' => AssetMaintenance::scheduled()->count(),
            'upcoming_warranty_expiry' => Asset::active()
                ->where('warranty_expiry', '>=', now())
                ->where('warranty_expiry', '<=', now()->addDays(30))
                ->count(),
        ];
    }

    // ===================== REPORT METHODS =====================

    public function getInventoryReport(array $filters = []): array
    {
        $query = Product::with(['category', 'brand']);

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['warehouse_id'])) {
            // Filter by warehouse-specific stock if tracked
        }

        $products = $query->active()->get();

        return [
            'products' => $products,
            'total_value' => $products->sum(fn($p) => $p->current_stock * $p->cost_price),
            'low_stock_count' => $products->filter(fn($p) => $p->isLowStock())->count(),
            'out_of_stock_count' => $products->filter(fn($p) => $p->isOutOfStock())->count(),
        ];
    }

    public function getStockLedger(int $productId, array $filters = []): array
    {
        $query = StockMovement::where('product_id', $productId);

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->byDateRange($filters['date_from'], $filters['date_to']);
        }

        $movements = $query->orderBy('movement_date')->get();

        $openingBalance = Product::find($productId)->current_stock;
        foreach ($movements as $movement) {
            if ($movement->isIncoming()) {
                $movement->running_balance = $openingBalance + $movement->quantity;
                $openingBalance = $movement->running_balance;
            } else {
                $movement->running_balance = $openingBalance - $movement->quantity;
                $openingBalance = $movement->running_balance;
            }
        }

        return [
            'product' => Product::find($productId),
            'movements' => $movements,
            'opening_stock' => $movements->first()?->opening_stock ?? 0,
            'closing_stock' => Product::find($productId)->current_stock,
        ];
    }

    // ===================== TRANSFER METHODS =====================

    public function getTransfers(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'requestedBy']);

        if (!empty($filters['from_location_id'])) {
            $query->where('from_warehouse_id', $filters['from_location_id']);
        }

        if (!empty($filters['to_location_id'])) {
            $query->where('to_warehouse_id', $filters['to_location_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('transfer_no', 'like', "%{$filters['search']}%")
                  ->orWhereHas('fromWarehouse', fn($wq) => $wq->where('name', 'like', "%{$filters['search']}%"))
                  ->orWhereHas('toWarehouse', fn($wq) => $wq->where('name', 'like', "%{$filters['search']}%"));
            });
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function createTransfer(array $data): StockTransfer
    {
        $data['transfer_no'] = StockTransfer::generateTransferNo();
        $data['requested_by'] = auth()->id();

        return StockTransfer::create($data);
    }

    public function approveTransfer(StockTransfer $transfer, int $userId): StockTransfer
    {
        $transfer->approve($userId);
        return $transfer->fresh();
    }

    public function completeTransfer(StockTransfer $transfer, int $userId): StockTransfer
    {
        $transfer->complete($userId);
        return $transfer->fresh();
    }
}
