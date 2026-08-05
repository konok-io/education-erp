<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publisher extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'publishers';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'code',
        'address',
        'city',
        'country',
        'phone',
        'email',
        'website',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_publishers')
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
