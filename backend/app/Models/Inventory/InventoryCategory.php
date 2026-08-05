<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryCategory extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'inventory_categories';

    protected $fillable = [
        'uuid', 'category_code', 'category_name', 'description', 'icon',
        'color', 'parent_id', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function parent(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(InventoryCategory::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(InventoryProduct::class, 'category_id');
    }

    // ===================== METHODS =====================

    public static function generateCategoryCode(): string
    {
        return 'IC-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }
}
