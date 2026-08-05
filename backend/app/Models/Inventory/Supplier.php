<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'suppliers';

    protected $fillable = [
        'uuid',
        'code',
        'company_name',
        'contact_person',
        'phone',
        'mobile',
        'email',
        'address',
        'city',
        'country',
        'trade_license',
        'tin',
        'bin',
        'vat_number',
        'website',
        'bank_name',
        'bank_account',
        'credit_limit',
        'payment_days',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'payment_days' => 'integer',
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_id');
    }

    public function goodsReceivedNotes(): HasMany
    {
        return $this->hasMany(GoodsReceivedNote::class, 'supplier_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('company_name', 'like', "%{$term}%")
              ->orWhere('code', 'like', "%{$term}%")
              ->orWhere('contact_person', 'like', "%{$term}%");
        });
    }
}
