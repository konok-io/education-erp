<?php

declare(strict_types=1);

namespace App\Models\Academic;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'departments';

    protected $fillable = [
        'uuid',
        'faculty_id',
        'name',
        'code',
        'description',
        'status',
    ];

    /**
     * Get the faculty that owns the department.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    /**
     * Get the programs for this department.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'department_id');
    }

    /**
     * Get the subjects for this department.
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'department_id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by faculty.
     */
    public function scopeByFaculty($query, string $facultyId)
    {
        return $query->where('faculty_id', $facultyId);
    }
}
