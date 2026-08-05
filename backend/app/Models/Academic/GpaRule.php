<?php

declare(strict_types=1);

namespace App\Models\Academic;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GpaRule extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'gpa_rules';

    protected $fillable = [
        'uuid',
        'academic_level_id',
        'name',
        'type',
        'min_gpa',
        'max_gpa',
        'fail_gpa',
        'is_current',
        'status',
    ];

    protected $casts = [
        'min_gpa' => 'decimal:2',
        'max_gpa' => 'decimal:2',
        'fail_gpa' => 'decimal:2',
        'is_current' => 'boolean',
    ];

    /**
     * Get the academic level.
     */
    public function academicLevel()
    {
        return $this->belongsTo(AcademicLevel::class, 'academic_level_id');
    }

    /**
     * Scope to get current rules.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope to filter by academic level.
     */
    public function scopeByLevel($query, string $levelId)
    {
        return $query->where('academic_level_id', $levelId);
    }

    /**
     * Scope to filter by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
