<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryShelf extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'library_shelves';

    protected $fillable = [
        'uuid',
        'name',
        'code',
        'building',
        'floor',
        'room',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function racks(): HasMany
    {
        return $this->hasMany(LibraryRack::class, 'shelf_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
