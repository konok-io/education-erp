<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequestItem extends Model
{
    protected $table = 'purchase_request_items';

    protected $fillable = [
        'purchase_request_id',
        'product_id',
        'product_name',
        'specifications',
        'quantity',
        'unit_id',
        'estimated_rate',
        'estimated_amount',
        'remarks',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'estimated_rate' => 'decimal:2',
        'estimated_amount' => 'decimal:2',
    ];

    // ===================== RELATIONSHIPS =====================

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }
}
