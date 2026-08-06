<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Inventory;

use App\Http\Controllers\Controller;
use App\Services\Inventory\InventoryService;
use App\Models\Inventory\InventoryItem;
use App\Models\Inventory\InventoryCategory;
use App\Models\Inventory\InventoryLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventoryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['category_id', 'status', 'is_serialized', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $items = $this->inventoryService->getItems($perPage, $filters);

        return response()->json([
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:inventory_categories,id',
            'unit' => 'nullable|string|max:50',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'min_stock_level' => 'nullable|numeric|min:0',
            'max_stock_level' => 'nullable|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
            'opening_stock' => 'nullable|numeric|min:0',
            'is_serialized' => 'nullable|boolean',
            'is_taxable' => 'nullable|boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $item = $this->inventoryService->createItem($validated);
        return response()->json(['data' => $item], 201);
    }

    public function show(string $uuid): JsonResponse
    {
        $item = InventoryItem::with(['category', 'stockMovements'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $item]);
    }

    public function adjustStock(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric',
            'type' => 'required|in:in,out',
            'location_id' => 'nullable|exists:inventory_locations,id',
            'notes' => 'nullable|string',
        ]);

        $item = $this->inventoryService->adjustStock(
            $uuid,
            (float) $validated['quantity'],
            $validated['type'],
            $validated['location_id'] ?? null,
            auth()->id(),
            $validated['notes'] ?? null
        );

        return response()->json(['data' => $item]);
    }

    public function transferStock(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_location_id' => 'required|exists:inventory_locations,id',
            'to_location_id' => 'required|exists:inventory_locations,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:inventory_items,id',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['requested_by'] = auth()->id();
        $transfer = $this->inventoryService->transferStock($validated);

        return response()->json(['data' => $transfer], 201);
    }

    public function getStats(): JsonResponse
    {
        $stats = $this->inventoryService->getInventoryStats();
        return response()->json(['data' => $stats]);
    }

    public function getCategories(): JsonResponse
    {
        $categories = InventoryCategory::where('is_active', true)
            ->with('children')
            ->whereNull('parent_id')
            ->get();

        return response()->json(['data' => $categories]);
    }

    public function getLocations(): JsonResponse
    {
        $locations = InventoryLocation::where('is_active', true)
            ->with('children')
            ->whereNull('parent_id')
            ->get();

        return response()->json(['data' => $locations]);
    }
}
