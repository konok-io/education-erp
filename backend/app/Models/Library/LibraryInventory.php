<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryInventory extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'library_inventory';

    protected $fillable = [
        'uuid', 'inventory_code', 'book_id', 'barcode', 'rfid', 'status',
        'condition', 'current_location', 'holder_id', 'holder_type',
        'purchase_date', 'purchase_price', 'last_check_date', 'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'last_check_date' => 'date',
    ];

    // ===================== STATUS =====================
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_ISSUED = 'issued';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_LOST = 'lost';
    public const STATUS_DAMAGED = 'damaged';
    public const STATUS_ARCHIVED = 'archived';
    public const STATUS_REPAIR = 'repair';

    // ===================== CONDITIONS =====================
    public const CONDITION_NEW = 'new';
    public const CONDITION_GOOD = 'good';
    public const CONDITION_FAIR = 'fair';
    public const CONDITION_POOR = 'poor';

    // ===================== RELATIONSHIPS =====================

    public function book(): BelongsTo
    {
        return $this->belongsTo(LibraryBook::class, 'book_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(LibraryBookIssue::class, 'inventory_id');
    }

    // ===================== METHODS =====================

    public static function generateInventoryCode(): string
    {
        return 'INV-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_ISSUED => 'Issued',
            self::STATUS_RESERVED => 'Reserved',
            self::STATUS_LOST => 'Lost',
            self::STATUS_DAMAGED => 'Damaged',
            self::STATUS_ARCHIVED => 'Archived',
            self::STATUS_REPAIR => 'Under Repair',
        ];
    }

    public static function conditions(): array
    {
        return [
            self::CONDITION_NEW => 'New',
            self::CONDITION_GOOD => 'Good',
            self::CONDITION_FAIR => 'Fair',
            self::CONDITION_POOR => 'Poor',
        ];
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }
}
