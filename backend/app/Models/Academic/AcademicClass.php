<?php

declare(strict_types=1);

namespace App\Models\Academic;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicClass extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'uuid',
        'program_id',
        'session_id',
        'semester_id',
        'name',
        'code',
        'capacity',
        'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    /**
     * Get the program that owns the class.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    /**
     * Get the session that owns the class.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    /**
     * Get the semester that owns the class.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Get the sections for this class.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    /**
     * Get the groups for this class.
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'class_id');
    }

    /**
     * Scope to filter by session.
     */
    public function scopeBySession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
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
