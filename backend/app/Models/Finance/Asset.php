<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'assets';

    protected $fillable = [
        'uuid',
        'asset_code',
        'name',
        'name_bn',
        'account_id',
        'asset_type',
        'purchase_date',
        'purchase_cost',
        'current_value',
        'salvage_value',
        'useful_life',
        'depreciation_method',
        'depreciation_rate',
        'accumulated_depreciation',
        'supplier',
        'warranty_expiry',
        'location',
        'status',
        'description',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'current_value' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'useful_life' => 'integer',
        'depreciation_rate' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'warranty_expiry' => 'date',
    ];

    // ===================== TYPES =====================
    public const TYPE_LAND = 'land';
    public const TYPE_BUILDING = 'building';
    public const TYPE_FURNITURE = 'furniture';
    public const TYPE_COMPUTER = 'computer';
    public const TYPE_VEHICLE = 'vehicle';
    public const TYPE_EQUIPMENT = 'equipment';
    public const TYPE_LIBRARY = 'library';
    public const TYPE_OTHER = 'other';

    // ===================== DEPRECIATION METHODS =====================
    public const DEPRECIATION_STRAIGHT_LINE = 'straight_line';
    public const DEPRECIATION_WDV = 'wdv';

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_DISPOSED = 'disposed';
    public const STATUS_SOLD = 'sold';

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public static function assetTypes(): array
    {
        return [
            self::TYPE_LAND => 'Land',
            self::TYPE_BUILDING => 'Building',
            self::TYPE_FURNITURE => 'Furniture',
            self::TYPE_COMPUTER => 'Computer',
            self::TYPE_VEHICLE => 'Vehicle',
            self::TYPE_EQUIPMENT => 'Equipment',
            self::TYPE_LIBRARY => 'Library Books',
            self::TYPE_OTHER => 'Other',
        ];
    }

    public static function depreciationMethods(): array
    {
        return [
            self::DEPRECIATION_STRAIGHT_LINE => 'Straight Line',
            self::DEPRECIATION_WDV => 'Written Down Value',
        ];
    }

    public function calculateAnnualDepreciation(): float
    {
        if ($this->depreciation_method === self::DEPRECIATION_STRAIGHT_LINE) {
            return ($this->purchase_cost - $this->salvage_value) / $this->useful_life;
        }

        // WDV method
        $rate = $this->depreciation_rate / 100;
        return $this->current_value * $rate;
    }

    public function updateDepreciation(): void
    {
        $annual = $this->calculateAnnualDepreciation();
        $this->accumulated_depreciation += $annual;
        $this->current_value = $this->purchase_cost - $this->accumulated_depreciation;
        $this->save();
    }
}
