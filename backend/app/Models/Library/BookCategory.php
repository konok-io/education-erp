<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookCategory extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'book_categories';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'code',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'category_id');
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'category_id');
    }

    public function digitalBooks(): HasMany
    {
        return $this->hasMany(DigitalBook::class, 'category_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrderBySort($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
