<?php

declare(strict_types=1);

namespace App\Models\Academic;

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
        'subject_code',
        'subject_name',
        'subject_name_bn',
        'department_id',
        'subject_category_id',
        'credit',
        'full_marks',
        'pass_marks',
        'theory_marks',
        'practical_marks',
        'is_optional',
        'status',
    ];

    protected $casts = [
        'credit' => 'decimal:2',
        'full_marks' => 'integer',
        'pass_marks' => 'integer',
        'theory_marks' => 'integer',
        'practical_marks' => 'integer',
        'is_optional' => 'boolean',
    ];

    /**
     * Get the department that owns the subject.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the subject category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SubjectCategory::class, 'subject_category_id');
    }

    /**
     * Get the program subjects.
     */
    public function programSubjects(): HasMany
    {
        return $this->hasMany(ProgramSubject::class, 'subject_id');
    }

    /**
     * Scope to filter by department.
     */
    public function scopeByDepartment($query, string $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, string $categoryId)
    {
        return $query->where('subject_category_id', $categoryId);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get optional subjects.
     */
    public function scopeOptional($query)
    {
        return $query->where('is_optional', true);
    }

    /**
     * Scope to get compulsory subjects.
     */
    public function scopeCompulsory($query)
    {
        return $query->where('is_optional', false);
    }
}
