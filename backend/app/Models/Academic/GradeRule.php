<?php

declare(strict_types=1);

namespace App\Models\Academic;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeRule extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'grade_rules';

    protected $fillable = [
        'uuid',
        'academic_level_id',
        'grade_name',
        'grade_point',
        'min_percentage',
        'max_percentage',
        'is_active',
    ];

    protected $casts = [
        'grade_point' => 'decimal:2',
        'min_percentage' => 'decimal:2',
        'max_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the academic level.
     */
    public function academicLevel()
    {
        return $this->belongsTo(AcademicLevel::class, 'academic_level_id');
    }

    /**
     * Scope to filter by academic level.
     */
    public function scopeByLevel($query, string $levelId)
    {
        return $query->where('academic_level_id', $levelId);
    }

    /**
     * Scope to get active grades.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
