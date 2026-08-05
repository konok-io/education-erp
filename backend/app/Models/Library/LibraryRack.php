<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryRack extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'library_racks';

    protected $fillable = [
        'uuid',
        'shelf_id',
        'name',
        'code',
        'row',
        'column',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function shelf(): BelongsTo
    {
        return $this->belongsTo(LibraryShelf::class, 'shelf_id');
    }

    public function bookCopies(): HasMany
    {
        return $this->hasMany(BookCopy::class, 'rack_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
