<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BookCopy extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'book_copies';

    protected $fillable = [
        'uuid',
        'book_id',
        'rack_id',
        'accession_number',
        'barcode',
        'qr_code',
        'condition',
        'status',
        'acquisition_date',
        'purchase_price',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'purchase_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_ISSUED = 'issued';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_LOST = 'lost';
    public const STATUS_DAMAGED = 'damaged';
    public const STATUS_ARCHIVED = 'archived';

    // ===================== CONDITIONS =====================
    public const CONDITION_NEW = 'new';
    public const CONDITION_GOOD = 'good';
    public const CONDITION_FAIR = 'fair';
    public const CONDITION_POOR = 'poor';

    // ===================== RELATIONSHIPS =====================

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function rack(): BelongsTo
    {
        return $this->belongsTo(LibraryRack::class, 'rack_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'book_copy_id');
    }

    // ===================== SCOPES =====================

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ===================== METHODS =====================

    public static function generateAccessionNumber(): string
    {
        $prefix = 'ACC';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%05d', $prefix, $year, $count);
    }

    public static function generateBarcode(): string
    {
        return 'LIB' . Str::random(12);
    }

    public static function generateQRCode(): string
    {
        return Str::uuid()->toString();
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function isIssued(): bool
    {
        return $this->status === self::STATUS_ISSUED;
    }

    public function markAsIssued(): void
    {
        $this->update(['status' => self::STATUS_ISSUED]);
    }

    public function markAsAvailable(): void
    {
        $this->update(['status' => self::STATUS_AVAILABLE]);
    }

    public function markAsLost(): void
    {
        $this->update(['status' => self::STATUS_LOST]);
    }

    public function markAsDamaged(): void
    {
        $this->update(['status' => self::STATUS_DAMAGED]);
    }
}
