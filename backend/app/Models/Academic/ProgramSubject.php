<?php

declare(strict_types=1);

namespace App\Models\Academic;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramSubject extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'program_subjects';

    protected $fillable = [
        'uuid',
        'program_id',
        'semester_id',
        'subject_id',
        'is_compulsory',
        'status',
    ];

    protected $casts = [
        'is_compulsory' => 'boolean',
    ];

    /**
     * Get the program.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    /**
     * Get the semester.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Get the subject.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Scope to filter by program.
     */
    public function scopeByProgram($query, string $programId)
    {
        return $query->where('program_id', $programId);
    }

    /**
     * Scope to filter by semester.
     */
    public function scopeBySemester($query, string $semesterId)
    {
        return $query->where('semester_id', $semesterId);
    }
}
