<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventorySupplier extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'inventory_suppliers';

    protected $fillable = [
        'uuid', 'supplier_code', 'company_name', 'contact_person', 'phone',
        'mobile', 'email', 'address', 'city', 'country', 'tin_vat', 'trade_license',
        'bank_name', 'bank_account', 'payment_terms', 'rating', 'total_orders',
        'total_amount', 'is_active', 'notes',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'total_orders' => 'integer',
        'total_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(InventoryAsset::class, 'supplier_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ===================== METHODS =====================

    public static function generateSupplierCode(): string
    {
        return 'SUP-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }

    public function updateRating(): void
    {
        $avgRating = $this->purchaseOrders()
            ->whereNotNull('rating')
            ->avg('rating');
        $this->update(['rating' => $avgRating]);
    }
}
