<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Inventory;

use App\Http\Controllers\Controller;
use App\Services\Inventory\AssetService;
use App\Models\Inventory\Asset;
use App\Models\Inventory\AssetCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function __construct(
        private readonly AssetService $assetService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['category_id', 'status', 'condition', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $assets = $this->assetService->getAssets($perPage, $filters);

        return response()->json([
            'data' => $assets->items(),
            'meta' => [
                'current_page' => $assets->currentPage(),
                'last_page' => $assets->lastPage(),
                'per_page' => $assets->perPage(),
                'total' => $assets->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'category_id' => 'required|exists:asset_categories,id',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'condition' => 'nullable|in:new,good,fair,poor',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'vendor_id' => 'nullable|exists:vendors,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'location_id' => 'nullable|exists:inventory_locations,id',
            'notes' => 'nullable|string',
        ]);

        $asset = $this->assetService->createAsset($validated);
        return response()->json(['data' => $asset], 201);
    }

    public function show(string $uuid): JsonResponse
    {
        $asset = Asset::with(['category', 'vendor', 'location', 'assignments', 'maintenanceRecords'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $asset]);
    }

    public function assign(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'assignee_id' => 'required|exists:users,id',
            'assignee_type' => 'nullable|string',
            'assignment_date' => 'nullable|date',
            'expected_return_date' => 'nullable|date',
            'purpose' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['assigned_by'] = auth()->id();
        $assignment = $this->assetService->assignAsset($uuid, $validated);

        return response()->json(['data' => $assignment], 201);
    }

    public function returnAsset(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'return_condition' => 'nullable|in:excellent,good,fair,poor,damaged',
            'return_notes' => 'nullable|string',
        ]);

        $asset = $this->assetService->returnAsset($uuid, $validated);
        return response()->json(['data' => $asset]);
    }

    public function scheduleMaintenance(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'maintenance_type' => 'required|in:preventive,corrective,inspection,calibration,upgrade',
            'scheduled_date' => 'required|date',
            'performed_by' => 'nullable|exists:users,id',
            'vendor_name' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $maintenance = $this->assetService->scheduleMaintenance($uuid, $validated);
        return response()->json(['data' => $maintenance], 201);
    }

    public function getStats(): JsonResponse
    {
        $stats = $this->assetService->getAssetStats();
        return response()->json(['data' => $stats]);
    }

    public function getCategories(): JsonResponse
    {
        $categories = AssetCategory::where('is_active', true)
            ->with('children')
            ->whereNull('parent_id')
            ->get();

        return response()->json(['data' => $categories]);
    }
}
