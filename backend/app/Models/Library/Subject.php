<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'subjects';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'code',
        'category_id',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'subject_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
