<?php

declare(strict_types=1);

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryBookCopy extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'library_book_copies';

    const CONDITION_NEW = 'new';
    const CONDITION_GOOD = 'good';
    const CONDITION_FAIR = 'fair';
    const CONDITION_POOR = 'poor';
    const CONDITION_DAMAGED = 'damaged';

    const STATUS_AVAILABLE = 'available';
    const STATUS_ISSUED = 'issued';
    const STATUS_RESERVED = 'reserved';
    const STATUS_LOST = 'lost';
    const STATUS_DAMAGED = 'damaged';
    const STATUS_REPAIR = 'repair';
    const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'uuid',
        'accession_no',
        'book_id',
        'copy_number',
        'barcode',
        'qr_code',
        'shelf_id',
        'rack_id',
        'condition',
        'status',
        'purchase_date',
        'last_issue_date',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'last_issue_date' => 'date',
    ];

    public static function generateAccessionNo(): string
    {
        $prefix = 'ACC';
        $year = date('Y');
        $month = date('m');
        $lastCopy = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastCopy ? ((int) substr($lastCopy->accession_no, -5)) + 1 : 1;
        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(LibraryBook::class, 'book_id');
    }

    public function shelf(): BelongsTo
    {
        return $this->belongsTo(LibraryShelf::class, 'shelf_id');
    }

    public function rack(): BelongsTo
    {
        return $this->belongsTo(LibraryRack::class, 'rack_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(LibraryIssue::class, 'book_copy_id');
    }

    public function currentIssue(): HasMany
    {
        return $this->hasMany(LibraryIssue::class, 'book_copy_id')->where('status', 'issued');
    }
}
