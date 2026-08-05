<?php

declare(strict_types=1);

namespace App\Models\Student;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentPromotion extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'student_promotions';

    protected $fillable = [
        'uuid',
        'student_id',
        'from_session_id',
        'to_session_id',
        'from_semester_id',
        'to_semester_id',
        'from_class_id',
        'to_class_id',
        'from_section_id',
        'to_section_id',
        'from_group_id',
        'to_group_id',
        'result',
        'status',
        'promoted_by',
        'promotion_date',
        'remarks',
    ];

    protected $casts = [
        'promotion_date' => 'date',
        'result' => 'array',
    ];

    /**
     * Get the student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get from session.
     */
    public function fromSession(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\AcademicSession::class, 'from_session_id');
    }

    /**
     * Get to session.
     */
    public function toSession(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\AcademicSession::class, 'to_session_id');
    }

    /**
     * Get from semester.
     */
    public function fromSemester(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Semester::class, 'from_semester_id');
    }

    /**
     * Get to semester.
     */
    public function toSemester(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Semester::class, 'to_semester_id');
    }

    /**
     * Get from class.
     */
    public function fromClass(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\AcademicClass::class, 'from_class_id');
    }

    /**
     * Get to class.
     */
    public function toClass(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\AcademicClass::class, 'to_class_id');
    }

    /**
     * Promotion status constants.
     */
    public const STATUS_PROMOTED = 'promoted';
    public const STATUS_RETAINED = 'retained';
    public const STATUS_CONDITIONAL = 'conditional';
}
