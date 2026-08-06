<?php

declare(strict_types=1);

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryBookAuthor extends Model
{
    use HasFactory;

    protected $table = 'library_book_authors';

    protected $fillable = [
        'book_id',
        'author_id',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(LibraryBook::class, 'book_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(LibraryAuthor::class, 'author_id');
    }
}
