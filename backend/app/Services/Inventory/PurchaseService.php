<?php

declare(strict_types=1);

namespace App\Services\Inventory;

use App\Models\Inventory\PurchaseRequisition;
use App\Models\Inventory\PurchaseRequisitionItem;
use App\Models\Inventory\PurchaseOrder;
use App\Models\Inventory\PurchaseOrderItem;
use App\Models\Inventory\GoodsReceivedNote;
use App\Models\Inventory\GoodsReceivedNoteItem;
use App\Models\Inventory\Vendor;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseService
{
    // ===================== PURCHASE REQUISITION =====================

    public function getRequisitions(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = PurchaseRequisition::with(['requester', 'department']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['requester_id'])) {
            $query->where('requester_id', $filters['requester_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createRequisition(array $data): PurchaseRequisition
    {
        return DB::transaction(function () use ($data) {
            $requisition = PurchaseRequisition::create([
                'uuid' => (string) Str::uuid(),
                'requisition_no' => PurchaseRequisition::generateRequisitionNo(),
                'requester_id' => $data['requester_id'],
                'department_id' => $data['department_id'] ?? null,
                'priority' => $data['priority'] ?? PurchaseRequisition::PRIORITY_MEDIUM,
                'status' => PurchaseRequisition::STATUS_DRAFT,
                'purpose' => $data['purpose'] ?? null,
                'notes' => $data['notes'] ?? null,
                'estimated_total' => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $itemData) {
                $itemTotal = $itemData['quantity'] * $itemData['estimated_price'];
                $total += $itemTotal;

                $requisition->items()->create([
                    'uuid' => (string) Str::uuid(),
                    'item_id' => $itemData['item_id'] ?? null,
                    'item_name' => $itemData['item_name'],
                    'specifications' => $itemData['specifications'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'] ?? 'pcs',
                    'estimated_price' => $itemData['estimated_price'],
                    'total' => $itemTotal,
                ]);
            }

            $requisition->update(['estimated_total' => $total]);

            return $requisition;
        });
    }

    public function submitRequisition(string $uuid): PurchaseRequisition
    {
        $requisition = PurchaseRequisition::where('uuid', $uuid)->firstOrFail();
        $requisition->update(['status' => PurchaseRequisition::STATUS_SUBMITTED]);
        return $requisition->fresh();
    }

    public function approveRequisition(string $uuid, int $approverId): PurchaseRequisition
    {
        $requisition = PurchaseRequisition::where('uuid', $uuid)->firstOrFail();
        $requisition->approve($approverId);
        return $requisition->fresh();
    }

    public function convertToPurchaseOrder(string $uuid, array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($uuid, $data) {
            $requisition = PurchaseRequisition::where('uuid', $uuid)->firstOrFail();

            $purchaseOrder = PurchaseOrder::create([
                'uuid' => (string) Str::uuid(),
                'order_no' => PurchaseOrder::generateOrderNo(),
                'vendor_id' => $data['vendor_id'],
                'requisition_id' => $requisition->id,
                'created_by' => $data['created_by'],
                'order_date' => $data['order_date'] ?? now(),
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'status' => PurchaseOrder::STATUS_DRAFT,
                'subtotal' => $requisition->estimated_total,
                'discount_percent' => $data['discount_percent'] ?? 0,
                'discount_amount' => 0,
                'tax_percent' => $data['tax_percent'] ?? 0,
                'tax_amount' => 0,
                'shipping_cost' => $data['shipping_cost'] ?? 0,
                'total' => $requisition->estimated_total,
                'shipping_address' => $data['shipping_address'] ?? null,
                'terms_conditions' => $data['terms_conditions'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($requisition->items as $reqItem) {
                $purchaseOrder->items()->create([
                    'uuid' => (string) Str::uuid(),
                    'item_id' => $reqItem->item_id,
                    'item_name' => $reqItem->item_name,
                    'description' => $reqItem->specifications,
                    'quantity' => $reqItem->quantity,
                    'ordered_quantity' => $reqItem->quantity,
                    'received_quantity' => 0,
                    'rejected_quantity' => 0,
                    'unit' => $reqItem->unit,
                    'unit_price' => $reqItem->estimated_price,
                    'total' => $reqItem->total,
                    'tax_percent' => $data['tax_percent'] ?? 0,
                    'tax_amount' => 0,
                ]);
            }

            $purchaseOrder->calculateTotals();
            $requisition->update(['status' => PurchaseRequisition::STATUS_CONVERTED]);

            return $purchaseOrder;
        });
    }

    // ===================== PURCHASE ORDER =====================

    public function getPurchaseOrders(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = PurchaseOrder::with(['vendor', 'creator']);

        if (!empty($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function receiveGoods(string $uuid, array $data): GoodsReceivedNote
    {
        return DB::transaction(function () use ($uuid, $data) {
            $order = PurchaseOrder::where('uuid', $uuid)->firstOrFail();

            $grn = GoodsReceivedNote::create([
                'uuid' => (string) Str::uuid(),
                'grn_no' => GoodsReceivedNote::generateGrnNo(),
                'purchase_order_id' => $order->id,
                'vendor_id' => $order->vendor_id,
                'received_by' => $data['received_by'],
                'warehouse_id' => $data['warehouse_id'],
                'received_date' => $data['received_date'] ?? now(),
                'status' => GoodsReceivedNote::STATUS_PENDING,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $itemData) {
                $grnItem = $grn->items()->create([
                    'uuid' => (string) Str::uuid(),
                    'item_id' => $itemData['item_id'] ?? null,
                    'item_name' => $itemData['item_name'],
                    'quantity' => $itemData['quantity'],
                    'accepted_quantity' => $itemData['accepted_quantity'] ?? $itemData['quantity'],
                    'rejected_quantity' => $itemData['rejected_quantity'] ?? 0,
                    'unit' => $itemData['unit'] ?? 'pcs',
                    'unit_price' => $itemData['unit_price'] ?? 0,
                    'total' => ($itemData['accepted_quantity'] ?? $itemData['quantity']) * ($itemData['unit_price'] ?? 0),
                    'notes' => $itemData['notes'] ?? null,
                ]);

                // Update purchase order item received quantity
                $poItem = $order->items()->where('item_name', $itemData['item_name'])->first();
                if ($poItem) {
                    $poItem->increment('received_quantity', $grnItem->accepted_quantity);
                }

                // Update inventory stock
                if ($itemData['item_id']) {
                    $inventoryItem = \App\Models\Inventory\InventoryItem::find($itemData['item_id']);
                    if ($inventoryItem) {
                        $inventoryItem->updateStock($grnItem->accepted_quantity, 'purchase');
                    }
                }
            }

            // Check if all items received
            $allReceived = $order->items()->whereRaw('received_quantity < quantity')->count() === 0;
            if ($allReceived) {
                $grn->update(['status' => GoodsReceivedNote::STATUS_COMPLETED]);
                $order->update([
                    'status' => PurchaseOrder::STATUS_DELIVERED,
                    'actual_delivery_date' => now(),
                ]);
            } else {
                $grn->update(['status' => GoodsReceivedNote::STATUS_PARTIAL]);
                $order->update(['status' => PurchaseOrder::STATUS_PARTIAL]);
            }

            return $grn;
        });
    }

    // ===================== VENDORS =====================

    public function getVendors(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Vendor::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['vendor_type'])) {
            $query->where('vendor_type', $filters['vendor_type']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('vendor_code', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createVendor(array $data): Vendor
    {
        return Vendor::create([
            'uuid' => (string) Str::uuid(),
            'vendor_code' => Vendor::generateVendorCode(),
            'name' => $data['name'],
            'name_bn' => $data['name_bn'] ?? null,
            'contact_person' => $data['contact_person'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'website' => $data['website'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'district' => $data['district'] ?? null,
            'country' => $data['country'] ?? null,
            'tax_id' => $data['tax_id'] ?? null,
            'trade_license' => $data['trade_license'] ?? null,
            'vendor_type' => $data['vendor_type'] ?? Vendor::TYPE_SUPPLIER,
            'status' => Vendor::STATUS_ACTIVE,
            'rating' => $data['rating'] ?? 0,
            'payment_terms' => $data['payment_terms'] ?? null,
            'credit_limit' => $data['credit_limit'] ?? 0,
            'credit_days' => $data['credit_days'] ?? 0,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function getPurchaseStats(): array
    {
        return [
            'total_orders' => PurchaseOrder::count(),
            'pending_orders' => PurchaseOrder::whereIn('status', [
                PurchaseOrder::STATUS_DRAFT,
                PurchaseOrder::STATUS_SENT,
                PurchaseOrder::STATUS_CONFIRMED,
            ])->count(),
            'delivered_orders' => PurchaseOrder::where('status', PurchaseOrder::STATUS_DELIVERED)->count(),
            'total_value' => PurchaseOrder::sum('total'),
            'total_vendors' => Vendor::where('status', Vendor::STATUS_ACTIVE)->count(),
            'pending_requisitions' => PurchaseRequisition::whereIn('status', [
                PurchaseRequisition::STATUS_SUBMITTED,
                PurchaseRequisition::STATUS_APPROVED,
            ])->count(),
            'by_status' => PurchaseOrder::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
        ];
    }
}
