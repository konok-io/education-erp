<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Inventory;

use App\Http\Controllers\Controller;
use App\Services\Inventory\PurchaseService;
use App\Models\Inventory\PurchaseOrder;
use App\Models\Inventory\PurchaseRequisition;
use App\Models\Inventory\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(
        private readonly PurchaseService $purchaseService
    ) {}

    // ===================== VENDORS =====================

    public function getVendors(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'vendor_type', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $vendors = $this->purchaseService->getVendors($perPage, $filters);

        return response()->json([
            'data' => $vendors->items(),
            'meta' => [
                'current_page' => $vendors->currentPage(),
                'last_page' => $vendors->lastPage(),
                'per_page' => $vendors->perPage(),
                'total' => $vendors->total(),
            ],
        ]);
    }

    public function createVendor(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'trade_license' => 'nullable|string|max:100',
            'vendor_type' => 'nullable|in:supplier,contractor,service_provider,manufacturer',
            'payment_terms' => 'nullable|string',
            'credit_limit' => 'nullable|integer|min:0',
            'credit_days' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $vendor = $this->purchaseService->createVendor($validated);
        return response()->json(['data' => $vendor], 201);
    }

    // ===================== PURCHASE REQUISITIONS =====================

    public function getRequisitions(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'priority', 'requester_id']);
        $perPage = (int) $request->get('per_page', 20);

        $requisitions = $this->purchaseService->getRequisitions($perPage, $filters);

        return response()->json([
            'data' => $requisitions->items(),
            'meta' => [
                'current_page' => $requisitions->currentPage(),
                'last_page' => $requisitions->lastPage(),
                'per_page' => $requisitions->perPage(),
                'total' => $requisitions->total(),
            ],
        ]);
    }

    public function createRequisition(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'purpose' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:inventory_items,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.specifications' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.estimated_price' => 'required|numeric|min:0',
        ]);

        $validated['requester_id'] = auth()->id();
        $requisition = $this->purchaseService->createRequisition($validated);

        return response()->json(['data' => $requisition], 201);
    }

    public function submitRequisition(string $uuid): JsonResponse
    {
        $requisition = $this->purchaseService->submitRequisition($uuid);
        return response()->json(['data' => $requisition]);
    }

    public function approveRequisition(string $uuid): JsonResponse
    {
        $requisition = $this->purchaseService->approveRequisition($uuid, auth()->id());
        return response()->json(['data' => $requisition]);
    }

    public function convertToPO(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'order_date' => 'nullable|date',
            'expected_delivery_date' => 'nullable|date',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'shipping_cost' => 'nullable|numeric|min:0',
            'shipping_address' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $order = $this->purchaseService->convertToPurchaseOrder($uuid, $validated);

        return response()->json(['data' => $order], 201);
    }

    // ===================== PURCHASE ORDERS =====================

    public function getPurchaseOrders(Request $request): JsonResponse
    {
        $filters = $request->only(['vendor_id', 'status']);
        $perPage = (int) $request->get('per_page', 20);

        $orders = $this->purchaseService->getPurchaseOrders($perPage, $filters);

        return response()->json([
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function showPurchaseOrder(string $uuid): JsonResponse
    {
        $order = PurchaseOrder::with(['vendor', 'creator', 'items', 'grns'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $order]);
    }

    public function receiveGoods(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'received_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:inventory_items,id',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.accepted_quantity' => 'nullable|numeric|min:0',
            'items.*.rejected_quantity' => 'nullable|numeric|min:0',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        $validated['received_by'] = auth()->id();
        $grn = $this->purchaseService->receiveGoods($uuid, $validated);

        return response()->json(['data' => $grn], 201);
    }

    public function getStats(): JsonResponse
    {
        $stats = $this->purchaseService->getPurchaseStats();
        return response()->json(['data' => $stats]);
    }
}
