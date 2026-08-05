<?php

declare(strict_types=1);

namespace App\Models\Academic;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Semester extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'semesters';

    protected $fillable = [
        'uuid',
        'program_id',
        'title',
        'code',
        'order_no',
        'duration_months',
        'status',
    ];

    protected $casts = [
        'order_no' => 'integer',
        'duration_months' => 'integer',
    ];

    /**
     * Get the program that owns the semester.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    /**
     * Get the classes for this semester.
     */
    public function classes(): HasMany
    {
        return $this->hasMany(AcademicClass::class, 'semester_id');
    }

    /**
     * Scope to filter by program.
     */
    public function scopeByProgram($query, string $programId)
    {
        return $query->where('program_id', $programId);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
