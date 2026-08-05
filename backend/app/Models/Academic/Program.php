<?php

declare(strict_types=1);

namespace App\Models\Academic;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'programs';

    protected $fillable = [
        'uuid',
        'department_id',
        'academic_level_id',
        'name',
        'code',
        'duration',
        'credit',
        'description',
        'status',
    ];

    protected $casts = [
        'duration' => 'integer',
        'credit' => 'decimal:2',
    ];

    /**
     * Get the department that owns the program.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the academic level that owns the program.
     */
    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class, 'academic_level_id');
    }

    /**
     * Get the semesters for this program.
     */
    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class, 'program_id');
    }

    /**
     * Get the classes for this program.
     */
    public function classes(): HasMany
    {
        return $this->hasMany(AcademicClass::class, 'program_id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by academic level.
     */
    public function scopeByLevel($query, string $levelId)
    {
        return $query->where('academic_level_id', $levelId);
    }

    /**
     * Scope to filter by department.
     */
    public function scopeByDepartment($query, string $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}
