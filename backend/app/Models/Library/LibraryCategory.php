<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryCategory extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'library_categories';

    protected $fillable = [
        'uuid', 'category_code', 'category_name', 'description', 'icon',
        'color', 'parent_id', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function parent(): BelongsTo
    {
        return $this->belongsTo(LibraryCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(LibraryCategory::class, 'parent_id');
    }

    public function books(): HasMany
    {
        return $this->hasMany(LibraryBook::class, 'category_id');
    }

    // ===================== METHODS =====================

    public static function generateCategoryCode(): string
    {
        return 'CAT-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }
}
