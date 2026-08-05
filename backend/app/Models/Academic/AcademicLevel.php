<?php

declare(strict_types=1);

namespace App\Models\Academic;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicLevel extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'academic_levels';

    protected $fillable = [
        'uuid',
        'name',
        'short_name',
        'code',
        'education_type',
        'duration',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'duration' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the programs for this level.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'academic_level_id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
