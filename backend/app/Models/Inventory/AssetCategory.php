<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asset_categories';

    const DEPRECIATION_STRAIGHT_LINE = 'straight_line';
    const DEPRECIATION_DECLINING_BALANCE = 'declining_balance';
    const DEPRECIATION_SUM_OF_YEARS = 'sum_of_years';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'code',
        'description',
        'parent_id',
        'is_active',
        'depreciation_rate',
        'depreciation_method',
        'useful_life_years',
    ];

    protected $casts = [
        'depreciation_rate' => 'decimal:2',
        'useful_life_years' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(AssetCategory::class, 'parent_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'category_id');
    }

    public static function depreciationMethods(): array
    {
        return [
            self::DEPRECIATION_STRAIGHT_LINE => 'Straight Line',
            self::DEPRECIATION_DECLINING_BALANCE => 'Declining Balance',
            self::DEPRECIATION_SUM_OF_YEARS => 'Sum of Years Digits',
        ];
    }
}
