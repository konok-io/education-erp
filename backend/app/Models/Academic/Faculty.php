<?php

declare(strict_types=1);

namespace App\Models\Academic;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faculty extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'faculties';

    protected $fillable = [
        'uuid',
        'name',
        'code',
        'description',
        'status',
    ];

    /**
     * Get the departments for this faculty.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'faculty_id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
