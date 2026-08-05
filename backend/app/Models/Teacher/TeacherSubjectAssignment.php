<?php

declare(strict_types=1);

namespace App\Models\Teacher;

use App\Models\Academic\Subject;
use App\Models\Academic\Program;
use App\Models\Academic\Semester;
use App\Models\Academic\AcademicSession;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherSubjectAssignment extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'teacher_subject_assignments';

    protected $fillable = [
        'uuid',
        'teacher_id',
        'subject_id',
        'program_id',
        'semester_id',
        'session_id',
        'is_class_teacher',
        'status',
        'assigned_by',
        'assigned_at',
        'remarks',
    ];

    protected $casts = [
        'is_class_teacher' => 'boolean',
        'assigned_at' => 'datetime',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBySession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeBySubject($query, string $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }
}
