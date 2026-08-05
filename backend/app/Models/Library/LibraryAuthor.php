<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryAuthor extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'library_authors';

    protected $fillable = [
        'uuid', 'author_code', 'author_name', 'biography', 'email',
        'website', 'country', 'photo', 'specialization', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function books(): HasMany
    {
        return $this->hasMany(LibraryBook::class, 'author_id');
    }

    // ===================== METHODS =====================

    public static function generateAuthorCode(): string
    {
        return 'AUT-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }
}
