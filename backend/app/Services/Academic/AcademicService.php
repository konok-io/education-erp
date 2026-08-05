<?php

declare(strict_types=1);

namespace App\Services\Academic;

use App\Models\Academic\AcademicClass;
use App\Models\Academic\AcademicLevel;
use App\Models\Academic\AcademicSession;
use App\Models\Academic\Department;
use App\Models\Academic\Faculty;
use App\Models\Academic\GpaRule;
use App\Models\Academic\Program;
use App\Models\Academic\ProgramSubject;
use App\Models\Academic\Semester;
use App\Models\Academic\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class AcademicService
{
    /**
     * Get all records with filters and pagination.
     */
    public function getAll(Model $model, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $model->newQuery();

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Create a new record.
     */
    public function create(Model $model, array $data): Model
    {
        // Generate UUID if not exists
        if (!isset($data['uuid']) && in_array('uuid', $model->getFillable())) {
            $data['uuid'] = (string) Str::uuid();
        }

        // Set default status
        if (!isset($data['status']) && in_array('status', $model->getFillable())) {
            $data['status'] = 'active';
        }

        return $model->create($data);
    }

    /**
     * Find by UUID.
     */
    public function findByUuid(Model $model, string $uuid): ?Model
    {
        return $model->where('uuid', $uuid)->first();
    }

    /**
     * Update a record.
     */
    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        return $model->fresh();
    }

    /**
     * Delete a record.
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    /**
     * Set current academic session.
     */
    public function setCurrentSession(string $uuid): void
    {
        AcademicSession::setCurrent($uuid);
    }

    /**
     * Get complete academic hierarchy.
     */
    public function getAcademicHierarchy(): array
    {
        return [
            'academic_levels' => AcademicLevel::active()->orderBy('sort_order')->get(),
            'faculties' => Faculty::active()->get(),
            'departments' => Department::active()->with('faculty')->get(),
            'programs' => Program::active()->with(['department', 'academicLevel'])->get(),
            'sessions' => AcademicSession::active()->orderBy('start_date', 'desc')->get(),
            'semesters' => Semester::active()->with('program')->orderBy('order_no')->get(),
        ];
    }

    /**
     * Get subjects by program.
     */
    public function getSubjectsByProgram(string $programId): array
    {
        $program = Program::where('uuid', $programId)->first();
        
        if (!$program) {
            return [];
        }

        return ProgramSubject::where('program_id', $program->id)
            ->with(['subject', 'semester'])
            ->get()
            ->groupBy('semester_id')
            ->toArray();
    }

    /**
     * Get classes by session.
     */
    public function getClassesBySession(string $sessionId): array
    {
        $session = AcademicSession::where('uuid', $sessionId)->first();
        
        if (!$session) {
            return [];
        }

        return AcademicClass::where('session_id', $session->id)
            ->with(['program', 'semester', 'sections', 'groups'])
            ->get()
            ->toArray();
    }

    /**
     * Get active grades by academic level.
     */
    public function getGradesByLevel(string $levelId): array
    {
        $level = AcademicLevel::where('uuid', $levelId)->first();
        
        if (!$level) {
            return [];
        }

        return \App\Models\Academic\GradeRule::where('academic_level_id', $level->id)
            ->active()
            ->orderBy('min_percentage', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get current GPA rules by academic level.
     */
    public function getCurrentGpaRules(string $levelId): array
    {
        $level = AcademicLevel::where('uuid', $levelId)->first();
        
        if (!$level) {
            return [];
        }

        return GpaRule::where('academic_level_id', $level->id)
            ->current()
            ->get()
            ->toArray();
    }

    /**
     * Assign subject to program.
     */
    public function assignSubjectToProgram(
        string $programUuid,
        string $semesterUuid,
        string $subjectUuid,
        bool $isCompulsory = true
    ): ProgramSubject {
        $program = Program::where('uuid', $programUuid)->firstOrFail();
        $semester = Semester::where('uuid', $semesterUuid)->firstOrFail();
        $subject = Subject::where('uuid', $subjectUuid)->firstOrFail();

        return ProgramSubject::updateOrCreate(
            [
                'program_id' => $program->id,
                'semester_id' => $semester->id,
                'subject_id' => $subject->id,
            ],
            [
                'is_compulsory' => $isCompulsory,
                'status' => 'active',
            ]
        );
    }

    /**
     * Remove subject from program.
     */
    public function removeSubjectFromProgram(string $programUuid, string $subjectUuid): bool
    {
        $program = Program::where('uuid', $programUuid)->first();
        $subject = Subject::where('uuid', $subjectUuid)->first();

        if (!$program || !$subject) {
            return false;
        }

        return ProgramSubject::where('program_id', $program->id)
            ->where('subject_id', $subject->id)
            ->delete() > 0;
    }

    /**
     * Calculate GPA from grades.
     */
    public function calculateGpa(array $subjectGrades): float
    {
        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($subjectGrades as $grade) {
            $totalPoints += $grade['grade_point'] * $grade['credit'];
            $totalCredits += $grade['credit'];
        }

        if ($totalCredits === 0) {
            return 0.00;
        }

        return round($totalPoints / $totalCredits, 2);
    }
}
