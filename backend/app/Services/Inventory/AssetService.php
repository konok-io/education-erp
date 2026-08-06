<?php

declare(strict_types=1);

namespace App\Services\Inventory;

use App\Models\Inventory\Asset;
use App\Models\Inventory\AssetCategory;
use App\Models\Inventory\AssetAssignment;
use App\Models\Inventory\AssetMaintenance;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssetService
{
    public function getAssets(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Asset::with(['category', 'vendor', 'location']);

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_code', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createAsset(array $data): Asset
    {
        return Asset::create([
            'uuid' => (string) Str::uuid(),
            'asset_code' => Asset::generateAssetCode(),
            'name' => $data['name'],
            'name_bn' => $data['name_bn'] ?? null,
            'category_id' => $data['category_id'],
            'description' => $data['description'] ?? null,
            'brand' => $data['brand'] ?? null,
            'model' => $data['model'] ?? null,
            'serial_number' => $data['serial_number'] ?? null,
            'barcode' => $data['barcode'] ?? null,
            'qr_code' => $data['qr_code'] ?? null,
            'condition' => $data['condition'] ?? Asset::CONDITION_NEW,
            'status' => Asset::STATUS_AVAILABLE,
            'purchase_price' => $data['purchase_price'] ?? 0,
            'current_value' => $data['purchase_price'] ?? 0,
            'purchase_date' => $data['purchase_date'] ?? null,
            'warranty_expiry' => $data['warranty_expiry'] ?? null,
            'depreciation_start_date' => $data['depreciation_start_date'] ?? null,
            'salvage_value' => $data['salvage_value'] ?? 0,
            'vendor_id' => $data['vendor_id'] ?? null,
            'warehouse_id' => $data['warehouse_id'] ?? null,
            'location_id' => $data['location_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'specifications' => $data['specifications'] ?? null,
            'images' => $data['images'] ?? null,
            'is_insurable' => $data['is_insurable'] ?? false,
            'insurance_policy_no' => $data['insurance_policy_no'] ?? null,
            'insurance_expiry' => $data['insurance_expiry'] ?? null,
        ]);
    }

    public function assignAsset(string $uuid, array $data): AssetAssignment
    {
        $asset = Asset::where('uuid', $uuid)->firstOrFail();

        return DB::transaction(function () use ($asset, $data) {
            $assignment = AssetAssignment::create([
                'uuid' => (string) Str::uuid(),
                'assignment_no' => AssetAssignment::generateAssignmentNo(),
                'asset_id' => $asset->id,
                'assignee_id' => $data['assignee_id'],
                'assignee_type' => $data['assignee_type'] ?? 'employee',
                'assigned_by' => $data['assigned_by'],
                'assignment_date' => $data['assignment_date'] ?? now(),
                'expected_return_date' => $data['expected_return_date'] ?? null,
                'status' => AssetAssignment::STATUS_ACTIVE,
                'purpose' => $data['purpose'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $asset->update(['status' => Asset::STATUS_ASSIGNED]);

            return $assignment;
        });
    }

    public function returnAsset(string $uuid, array $data): Asset
    {
        $assignment = AssetAssignment::where('uuid', $uuid)->firstOrFail();
        $asset = $assignment->asset;

        $assignment->markAsReturned(
            $data['return_condition'] ?? 'good',
            $data['return_notes'] ?? null
        );

        return $asset->fresh();
    }

    public function scheduleMaintenance(string $uuid, array $data): AssetMaintenance
    {
        $asset = Asset::where('uuid', $uuid)->firstOrFail();

        return AssetMaintenance::create([
            'uuid' => (string) Str::uuid(),
            'maintenance_no' => AssetMaintenance::generateMaintenanceNo(),
            'asset_id' => $asset->id,
            'maintenance_type' => $data['maintenance_type'],
            'status' => AssetMaintenance::STATUS_SCHEDULED,
            'scheduled_date' => $data['scheduled_date'],
            'performed_by' => $data['performed_by'] ?? null,
            'vendor_name' => $data['vendor_name'] ?? null,
            'cost' => $data['cost'] ?? 0,
            'description' => $data['description'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function getAssetStats(): array
    {
        return [
            'total' => Asset::count(),
            'available' => Asset::where('status', Asset::STATUS_AVAILABLE)->count(),
            'assigned' => Asset::where('status', Asset::STATUS_ASSIGNED)->count(),
            'maintenance' => Asset::where('status', Asset::STATUS_MAINTENANCE)->count(),
            'repair' => Asset::where('status', Asset::STATUS_REPAIR)->count(),
            'total_value' => Asset::sum('purchase_price'),
            'current_value' => Asset::sum('current_value'),
            'by_condition' => Asset::selectRaw('`condition`, COUNT(*) as count')
                ->groupBy('condition')
                ->pluck('count', 'condition'),
            'by_category' => Asset::join('asset_categories', 'assets.category_id', '=', 'asset_categories.id')
                ->selectRaw('asset_categories.name, COUNT(*) as count')
                ->groupBy('asset_categories.name')
                ->pluck('count', 'asset_categories.name'),
        ];
    }
}
