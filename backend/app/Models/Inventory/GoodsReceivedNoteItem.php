<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceivedNoteItem extends Model
{
    protected $table = 'goods_received_note_items';

    protected $fillable = [
        'goods_received_note_id',
        'product_id',
        'purchase_order_item_id',
        'product_name',
        'ordered_quantity',
        'received_quantity',
        'accepted_quantity',
        'rejected_quantity',
        'unit_price',
        'total',
        'condition',
        'remarks',
    ];

    protected $casts = [
        'ordered_quantity' => 'decimal:2',
        'received_quantity' => 'decimal:2',
        'accepted_quantity' => 'decimal:2',
        'rejected_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // ===================== CONDITIONS =====================
    public const CONDITION_GOOD = 'good';
    public const CONDITION_DAMAGED = 'damaged';
    public const CONDITION_DEFECTIVE = 'defective';

    // ===================== RELATIONSHIPS =====================

    public function goodsReceivedNote(): BelongsTo
    {
        return $this->belongsTo(GoodsReceivedNote::class, 'goods_received_note_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id');
    }
}
