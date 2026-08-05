<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'product_categories';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'code',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrderBySort($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
