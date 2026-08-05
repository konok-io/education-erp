<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'books';

    protected $fillable = [
        'uuid',
        'isbn',
        'title',
        'title_bn',
        'subtitle',
        'edition',
        'language',
        'category_id',
        'subject_id',
        'description',
        'publication_year',
        'pages',
        'price',
        'currency',
        'keywords',
        'cover_image',
        'pdf_file',
        'is_digital',
        'is_reference_only',
        'total_copies',
        'available_copies',
        'is_active',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'pages' => 'integer',
        'price' => 'decimal:2',
        'total_copies' => 'integer',
        'available_copies' => 'integer',
        'is_digital' => 'boolean',
        'is_reference_only' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_ISSUED = 'issued';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_LOST = 'lost';
    public const STATUS_DAMAGED = 'damaged';

    // ===================== RELATIONSHIPS =====================

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'book_authors')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function publishers(): BelongsToMany
    {
        return $this->belongsToMany(Publisher::class, 'book_publishers')
            ->withTimestamps();
    }

    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class, 'book_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(BookReservation::class, 'book_id');
    }

    public function digitalBook(): HasOne
    {
        return $this->hasOne(DigitalBook::class, 'book_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('available_copies', '>', 0);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('isbn', 'like', "%{$term}%")
              ->orWhere('keywords', 'like', "%{$term}%");
        });
    }

    // ===================== METHODS =====================

    public function getAuthorNames(): string
    {
        return $this->authors->pluck('name')->implode(', ');
    }

    public function decrementAvailableCopies(): void
    {
        $this->decrement('available_copies');
    }

    public function incrementAvailableCopies(): void
    {
        $this->increment('available_copies');
    }
}

use Illuminate\Database\Eloquent\Relations\HasOne;
