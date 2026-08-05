<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryBrand extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'inventory_brands';

    protected $fillable = [
        'uuid', 'brand_code', 'brand_name', 'description', 'logo',
        'website', 'country', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function products(): HasMany
    {
        return $this->hasMany(InventoryProduct::class, 'brand_id');
    }

    // ===================== METHODS =====================

    public static function generateBrandCode(): string
    {
        return 'IB-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }
}
