<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryBranch extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'library_branches';

    protected $fillable = [
        'uuid', 'branch_code', 'branch_name', 'description', 'location',
        'building', 'floor', 'phone', 'email', 'capacity', 'is_digital',
        'is_active', 'manager_id',
    ];

    protected $casts = [
        'is_digital' => 'boolean',
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    // ===================== RELATIONSHIPS =====================

    public function manager(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'manager_id');
    }

    public function books(): HasMany
    {
        return $this->hasMany(LibraryBook::class, 'branch_id');
    }

    public function readingRooms(): HasMany
    {
        return $this->hasMany(LibraryReadingRoom::class, 'branch_id');
    }

    // ===================== METHODS =====================

    public static function generateBranchCode(): string
    {
        return 'LB-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }
}
