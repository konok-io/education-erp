<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductUnit extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'product_units';

    protected $fillable = [
        'uuid',
        'name',
        'code',
        'short_code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'unit_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
