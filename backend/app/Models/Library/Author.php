<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'authors';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'email',
        'phone',
        'country',
        'biography',
        'photo',
        'website',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_authors')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }
}
