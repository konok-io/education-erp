<?php

declare(strict_types=1);

namespace App\Models\Teacher;

use App\Models\Academic\AcademicClass;
use App\Models\Academic\Section;
use App\Models\Academic\AcademicSession;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherClassAssignment extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'teacher_class_assignments';

    protected $fillable = [
        'uuid',
        'teacher_id',
        'class_id',
        'section_id',
        'session_id',
        'is_primary_teacher',
        'weekly_classes',
        'status',
        'assigned_by',
        'assigned_at',
        'remarks',
    ];

    protected $casts = [
        'is_primary_teacher' => 'boolean',
        'weekly_classes' => 'integer',
        'assigned_at' => 'datetime',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
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

    public function scopeByClass($query, string $classId)
    {
        return $query->where('class_id', $classId);
    }
}
