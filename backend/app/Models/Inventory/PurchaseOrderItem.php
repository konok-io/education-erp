<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrderItem extends Model
{
    protected $table = 'purchase_order_items';

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'product_name',
        'specifications',
        'ordered_quantity',
        'received_quantity',
        'rejected_quantity',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'vat_percent',
        'vat_amount',
        'total',
        'remarks',
    ];

    protected $casts = [
        'ordered_quantity' => 'decimal:2',
        'received_quantity' => 'decimal:2',
        'rejected_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'vat_percent' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // ===================== RELATIONSHIPS =====================

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function goodsReceivedNoteItems(): HasMany
    {
        return $this->hasMany(GoodsReceivedNoteItem::class, 'purchase_order_item_id');
    }

    // ===================== METHODS =====================

    public function calculateTotal(): void
    {
        $subtotal = $this->ordered_quantity * $this->unit_price;
        $this->discount_amount = $subtotal * ($this->discount_percent / 100);
        $afterDiscount = $subtotal - $this->discount_amount;
        $this->vat_amount = $afterDiscount * ($this->vat_percent / 100);
        $this->total = $afterDiscount + $this->vat_amount;
        $this->save();
    }

    public function remainingQuantity(): float
    {
        return (float) ($this->ordered_quantity - $this->received_quantity);
    }
}
