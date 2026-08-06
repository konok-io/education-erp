<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemSerialNumber extends Model
{
    use HasFactory;

    protected $table = 'item_serial_numbers';

    const STATUS_AVAILABLE = 'available';
    const STATUS_SOLD = 'sold';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_DAMAGED = 'damaged';
    const STATUS_RETURNED = 'returned';

    protected $fillable = [
        'uuid',
        'item_id',
        'asset_id',
        'serial_number',
        'barcode',
        'status',
        'purchase_date',
        'purchase_price',
        'warranty_expiry',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
