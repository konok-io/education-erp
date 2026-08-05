<?php

declare(strict_types=1);

namespace App\Models\Student;

use App\Models\User;
use App\Models\Academic\AcademicClass;
use App\Models\Academic\AcademicSession;
use App\Models\Academic\Program;
use App\Models\Academic\Section;
use App\Models\Academic\Group;
use App\Models\Academic\AcademicLevel;
use App\Models\Academic\Faculty;
use App\Models\Academic\Department;
use App\Models\Academic\Semester;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'students';

    protected $fillable = [
        'uuid',
        'student_no',
        'user_id',
        'campus_id',
        'session_id',
        'academic_level_id',
        'faculty_id',
        'department_id',
        'program_id',
        'semester_id',
        'class_id',
        'section_id',
        'group_id',
        'status',
        'admission_date',
        'remarks',
    ];

    protected $casts = [
        'admission_date' => 'date',
    ];

    // ===================== RELATIONSHIPS =====================

    /**
     * Get the user account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the profile.
     */
    public function profile(): MorphOne
    {
        return $this->morphOne(StudentProfile::class, 'studentable');
    }

    /**
     * Get the guardian.
     */
    public function guardian(): MorphOne
    {
        return $this->morphOne(Guardian::class, 'studentable');
    }

    /**
     * Get the medical info.
     */
    public function medical(): MorphOne
    {
        return $this->morphOne(StudentMedical::class, 'studentable');
    }

    /**
     * Get the documents.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(StudentDocument::class, 'studentable');
    }

    /**
     * Get the promotions.
     */
    public function promotions(): HasMany
    {
        return $this->hasMany(StudentPromotion::class, 'student_id');
    }

    /**
     * Get the transfers.
     */
    public function transfers(): HasMany
    {
        return $this->hasMany(StudentTransfer::class, 'student_id');
    }

    /**
     * Get the academic session.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    /**
     * Get the academic level.
     */
    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class, 'academic_level_id');
    }

    /**
     * Get the faculty.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    /**
     * Get the department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

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
     * Get the class.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    /**
     * Get the section.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    /**
     * Get the group.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * Get the campus.
     */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Campus::class, 'campus_id');
    }

    // ===================== SCOPES =====================

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
     * Scope to filter by class.
     */
    public function scopeByClass($query, string $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope to filter by section.
     */
    public function scopeBySection($query, string $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get active students.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to search by various fields.
     */
    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('student_no', 'like', "%{$search}%")
                ->orWhereHas('profile', function ($pq) use ($search) {
                    $pq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                });
        });
    }

    // ===================== METHODS =====================

    /**
     * Generate student number.
     */
    public static function generateStudentNo(string $sessionCode): string
    {
        $prefix = 'ST';
        $year = substr($sessionCode, 0, 4);
        $count = self::whereYear('created_at', $year)->count() + 1;

        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    /**
     * Get full name from profile.
     */
    public function getFullNameAttribute(): ?string
    {
        return $this->profile?->full_name;
    }

    /**
     * Get photo URL.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        $photo = $this->profile?->photo;
        return $photo ? asset('storage/' . $photo) : null;
    }

    /**
     * Check if student can attend classes.
     */
    public function canAttendClasses(): bool
    {
        return $this->status === 'active';
    }
}
