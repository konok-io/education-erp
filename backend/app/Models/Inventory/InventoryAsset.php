<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryAsset extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'inventory_assets';

    protected $fillable = [
        'uuid', 'asset_code', 'asset_tag', 'asset_name', 'category_id',
        'supplier_id', 'serial_number', 'model', 'brand', 'color', 'size',
        'description', 'purchase_date', 'purchase_price', 'current_value',
        'salvage_value', 'useful_life_years', 'depreciation_rate',
        'depreciation_method', 'accumulated_depreciation', 'warehouse_id',
        'location', 'room', 'assigned_to', 'assigned_user_id', 'assigned_type',
        'assigned_date', 'warranty_expiry', 'insurance_policy', 'insurance_expiry',
        'condition', 'status', 'barcode', 'qr_code', 'image', 'specifications',
        'notes', 'is_active', 'created_by',
    ];

    protected $casts = [
        'specifications' => 'array',
        'purchase_date' => 'date',
        'assigned_date' => 'date',
        'warranty_expiry' => 'date',
        'insurance_expiry' => 'date',
        'purchase_price' => 'decimal:2',
        'current_value' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ===================== STATUSES =====================
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_LOST = 'lost';
    public const STATUS_DISPOSED = 'disposed';

    // ===================== DEPRECIATION METHODS =====================
    public const DEPRECIATION_STRAIGHT_LINE = 'straight_line';
    public const DEPRECIATION_REDUCING_BALANCE = 'reducing_balance';

    // ===================== RELATIONSHIPS =====================

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(InventorySupplier::class, 'supplier_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(AssetMaintenance::class, 'asset_id');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(AssetTransfer::class, 'asset_id');
    }

    // ===================== SCOPES =====================

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', self::STATUS_ASSIGNED);
    }

    // ===================== METHODS =====================

    public static function generateAssetCode(): string
    {
        $prefix = 'AST';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public static function generateAssetTag(): string
    {
        return 'TAG-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_MAINTENANCE => 'Under Maintenance',
            self::STATUS_LOST => 'Lost',
            self::STATUS_DISPOSED => 'Disposed',
        ];
    }

    public static function depreciationMethods(): array
    {
        return [
            self::DEPRECIATION_STRAIGHT_LINE => 'Straight Line',
            self::DEPRECIATION_REDUCING_BALANCE => 'Reducing Balance',
        ];
    }

    public function calculateDepreciation(): float
    {
        if (!$this->purchase_price || !$this->useful_life_years) {
            return 0;
        }

        if ($this->depreciation_method === self::DEPRECIATION_STRAIGHT_LINE) {
            return ($this->purchase_price - $this->salvage_value) / $this->useful_life_years;
        }

        // Reducing balance method
        $rate = $this->depreciation_rate ?? (2 / $this->useful_life_years);
        return $this->current_value * $rate;
    }

    public function isWarrantyExpired(): bool
    {
        return $this->warranty_expiry && $this->warranty_expiry < now();
    }

    public function isInsuranceExpired(): bool
    {
        return $this->insurance_expiry && $this->insurance_expiry < now();
    }

    public function isAssigned(): bool
    {
        return $this->status === self::STATUS_ASSIGNED;
    }
}
