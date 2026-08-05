<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryBook extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'library_books';

    protected $fillable = [
        'uuid', 'book_code', 'isbn', 'title', 'subtitle', 'edition',
        'language', 'book_type', 'category_id', 'author_id', 'publisher_id',
        'branch_id', 'publication_year', 'pages', 'shelf', 'rack', 'price',
        'currency', 'total_copies', 'available_copies', 'cover_image',
        'description', 'keywords', 'subjects', 'edition_year', 'volume',
        'rating', 'view_count', 'download_count', 'is_digital', 'digital_file',
        'digital_format', 'file_size', 'is_featured', 'is_reference_only',
        'is_active', 'created_by',
    ];

    protected $casts = [
        'keywords' => 'array',
        'subjects' => 'array',
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'file_size' => 'integer',
        'publication_year' => 'integer',
        'is_digital' => 'boolean',
        'is_featured' => 'boolean',
        'is_reference_only' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ===================== BOOK TYPES =====================
    public const TYPE_PHYSICAL = 'physical';
    public const TYPE_DIGITAL = 'digital';
    public const TYPE_REFERENCE = 'reference';
    public const TYPE_JOURNAL = 'journal';
    public const TYPE_MAGAZINE = 'magazine';
    public const TYPE_NEWSPAPER = 'newspaper';
    public const TYPE_AUDIO = 'audio_book';
    public const TYPE_VIDEO = 'video_course';

    // ===================== RELATIONSHIPS =====================

    public function category(): BelongsTo
    {
        return $this->belongsTo(LibraryCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(LibraryAuthor::class, 'author_id');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(LibraryPublisher::class, 'publisher_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(LibraryBranch::class, 'branch_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(LibraryInventory::class, 'book_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(LibraryBookIssue::class, 'book_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(LibraryReservation::class, 'book_id');
    }

    // ===================== SCOPES =====================

    public function scopeAvailable($query)
    {
        return $query->where('available_copies', '>', 0);
    }

    public function scopeDigital($query)
    {
        return $query->where('is_digital', true);
    }

    // ===================== METHODS =====================

    public static function generateBookCode(): string
    {
        $prefix = 'LBK';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public static function bookTypes(): array
    {
        return [
            self::TYPE_PHYSICAL => 'Physical Book',
            self::TYPE_DIGITAL => 'Digital Book',
            self::TYPE_REFERENCE => 'Reference Book',
            self::TYPE_JOURNAL => 'Journal',
            self::TYPE_MAGAZINE => 'Magazine',
            self::TYPE_NEWSPAPER => 'Newspaper',
            self::TYPE_AUDIO => 'Audio Book',
            self::TYPE_VIDEO => 'Video Course',
        ];
    }

    public function decrementAvailable(): void
    {
        $this->decrement('available_copies');
    }

    public function incrementAvailable(): void
    {
        $this->increment('available_copies');
    }

    public function canIssue(): bool
    {
        return $this->available_copies > 0 && $this->is_active;
    }
}
