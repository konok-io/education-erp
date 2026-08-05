<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'assets';

    protected $fillable = [
        'uuid',
        'asset_code',
        'product_id',
        'asset_name',
        'serial_number',
        'barcode',
        'qr_code',
        'category',
        'warehouse_id',
        'assigned_to_type',
        'assigned_to_id',
        'assigned_to_name',
        'purchase_date',
        'purchase_cost',
        'warranty_expiry',
        'supplier',
        'location',
        'condition',
        'status',
        'description',
        'notes',
        'depreciation_rate',
        'current_value',
        'disposal_date',
        'disposal_value',
        'is_active',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'warranty_expiry' => 'date',
        'depreciation_rate' => 'decimal:2',
        'current_value' => 'decimal:2',
        'disposal_date' => 'date',
        'disposal_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_ALLOCATED = 'allocated';
    public const STATUS_REPAIR = 'repair';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_LOST = 'lost';
    public const STATUS_DISPOSED = 'disposed';

    // ===================== CONDITIONS =====================
    public const CONDITION_NEW = 'new';
    public const CONDITION_GOOD = 'good';
    public const CONDITION_FAIR = 'fair';
    public const CONDITION_POOR = 'poor';

    // ===================== RELATIONSHIPS =====================

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(AssetTransfer::class, 'asset_id');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(AssetMaintenance::class, 'asset_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNotIn('status', [self::STATUS_DISPOSED]);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeAllocated($query)
    {
        return $query->where('status', self::STATUS_ALLOCATED);
    }

    // ===================== METHODS =====================

    public static function generateAssetCode(): string
    {
        $prefix = 'AST';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%04d', $prefix, $year, $count);
    }

    public static function generateBarcode(): string
    {
        return 'AST' . \Illuminate\Support\Str::random(10);
    }

    public static function generateQRCode(): string
    {
        return \Illuminate\Support\Str::uuid()->toString();
    }

    public static function categories(): array
    {
        return [
            'computer' => 'Computer/Laptop',
            'printer' => 'Printer/Scanner',
            'projector' => 'Projector',
            'furniture' => 'Furniture',
            'vehicle' => 'Vehicle',
            'generator' => 'Generator/UPS',
            'ac' => 'Air Conditioner',
            'lab' => 'Lab Equipment',
            'electrical' => 'Electrical Equipment',
            'other' => 'Other',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_ALLOCATED => 'Allocated',
            self::STATUS_REPAIR => 'Under Repair',
            self::STATUS_MAINTENANCE => 'Under Maintenance',
            self::STATUS_LOST => 'Lost',
            self::STATUS_DISPOSED => 'Disposed',
        ];
    }

    public function calculateCurrentValue(): float
    {
        if (!$this->purchase_cost || !$this->depreciation_rate) {
            return (float) $this->purchase_cost;
        }

        $years = $this->purchase_date->diffInYears(now());
        $depreciation = $this->purchase_cost * ($this->depreciation_rate / 100) * $years;
        $currentValue = $this->purchase_cost - $depreciation;

        return max(0, (float) $currentValue);
    }

    public function allocate(string $holderType, int $holderId, string $holderName): void
    {
        $this->update([
            'status' => self::STATUS_ALLOCATED,
            'assigned_to_type' => $holderType,
            'assigned_to_id' => $holderId,
            'assigned_to_name' => $holderName,
        ]);
    }

    public function markAsAvailable(): void
    {
        $this->update([
            'status' => self::STATUS_AVAILABLE,
            'assigned_to_type' => null,
            'assigned_to_id' => null,
            'assigned_to_name' => null,
        ]);
    }

    public function isUnderWarranty(): bool
    {
        return $this->warranty_expiry && $this->warranty_expiry > now();
    }
}
