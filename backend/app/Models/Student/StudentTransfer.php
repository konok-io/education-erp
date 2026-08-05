<?php

declare(strict_types=1);

namespace App\Models\Student;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentTransfer extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'student_transfers';

    protected $fillable = [
        'uuid',
        'student_id',
        'transfer_type',
        'from_campus_id',
        'to_campus_id',
        'from_department_id',
        'to_department_id',
        'from_program_id',
        'to_program_id',
        'from_class_id',
        'to_class_id',
        'from_section_id',
        'to_section_id',
        'from_group_id',
        'to_group_id',
        'reason',
        'transfer_date',
        'approved_by',
        'status',
        'remarks',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    /**
     * Get the student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Transfer type constants.
     */
    public const TYPE_CAMPUS = 'campus';
    public const TYPE_DEPARTMENT = 'department';
    public const TYPE_PROGRAM = 'program';
    public const TYPE_CLASS = 'class';
    public const TYPE_SECTION = 'section';
    public const TYPE_GROUP = 'group';

    /**
     * Status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    /**
     * Scope to filter by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('transfer_type', $type);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
