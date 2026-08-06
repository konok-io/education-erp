<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequisitionItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_requisition_items';

    protected $fillable = [
        'uuid',
        'requisition_id',
        'item_id',
        'item_name',
        'specifications',
        'quantity',
        'unit',
        'estimated_price',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'estimated_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class, 'requisition_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function calculateTotal(): void
    {
        $this->update(['total' => $this->quantity * $this->estimated_price]);
    }
}
