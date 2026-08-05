<?php

declare(strict_types=1);

namespace App\Services\Routine;

use App\Models\Routine\Routine;
use App\Models\Routine\TimeSlot;
use App\Models\Routine\Period;
use App\Models\Routine\Room;
use App\Models\Routine\AcademicCalendar;
use App\Models\Routine\Holiday;
use App\Models\Academic\AcademicClass;
use App\Models\Academic\Section;
use App\Models\Academic\Subject;
use App\Models\Teacher\Teacher;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoutineService
{
    // ===================== ROUTINE CRUD =====================

    public function getAll(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = Routine::with(['class', 'section', 'subject', 'teacher', 'room', 'timeSlot']);

        if (!empty($filters['session_id'])) {
            $session = \App\Models\Academic\AcademicSession::where('uuid', $filters['session_id'])->first();
            if ($session) {
                $query->bySession($session->id);
            }
        }

        if (!empty($filters['class_id'])) {
            $class = AcademicClass::where('uuid', $filters['class_id'])->first();
            if ($class) {
                $query->byClass($class->id);
            }
        }

        if (!empty($filters['section_id'])) {
            $section = Section::where('uuid', $filters['section_id'])->first();
            if ($section) {
                $query->where('section_id', $section->id);
            }
        }

        if (!empty($filters['teacher_id'])) {
            $teacher = Teacher::where('uuid', $filters['teacher_id'])->first();
            if ($teacher) {
                $query->byTeacher($teacher->id);
            }
        }

        if (isset($filters['day_of_week'])) {
            $query->byDay((int) $filters['day_of_week']);
        }

        if (!empty($filters['routine_type'])) {
            $query->where('routine_type', $filters['routine_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_published'])) {
            $query->where('is_published', $filters['is_published']);
        }

        return $query->orderBy('day_of_week')
            ->orderBy('time_slot_id')
            ->paginate($perPage);
    }

    public function create(array $data): ?Routine
    {
        $teacherId = $this->getModelId(Teacher::class, $data['teacher_id']);
        $roomId = $this->getModelId(Room::class, $data['room_id']);
        $timeSlotId = $this->getModelId(TimeSlot::class, $data['time_slot_id']);

        // Check for conflicts
        if ($this->hasConflict($teacherId, $roomId, $data['day_of_week'], $timeSlotId)) {
            return null;
        }

        return Routine::create([
            'uuid' => (string) Str::uuid(),
            'routine_code' => Routine::generateRoutineCode(),
            'session_id' => $this->getModelId(\App\Models\Academic\AcademicSession::class, $data['session_id']),
            'class_id' => $this->getModelId(AcademicClass::class, $data['class_id']),
            'section_id' => $this->getModelId(Section::class, $data['section_id'] ?? null),
            'subject_id' => $this->getModelId(Subject::class, $data['subject_id']),
            'teacher_id' => $teacherId,
            'room_id' => $roomId,
            'time_slot_id' => $timeSlotId,
            'period_id' => $this->getModelId(Period::class, $data['period_id'] ?? null),
            'day_of_week' => $data['day_of_week'],
            'routine_type' => $data['routine_type'],
            'status' => Routine::STATUS_DRAFT,
            'created_by' => auth()->id(),
            'remarks' => $data['remarks'] ?? null,
        ]);
    }

    public function findByUuid(string $uuid): ?Routine
    {
        return Routine::where('uuid', $uuid)->first();
    }

    public function update(Routine $routine, array $data): Routine
    {
        $routine->update(array_intersect_key($data, array_flip([
            'room_id', 'teacher_id', 'time_slot_id', 'day_of_week', 'remarks'
        ])));

        return $routine->fresh(['class', 'section', 'subject', 'teacher', 'room', 'timeSlot']);
    }

    public function delete(Routine $routine): bool
    {
        return $routine->delete();
    }

    // ===================== BULK OPERATIONS =====================

    public function bulkCreate(array $routines): array
    {
        $results = ['total' => 0, 'success' => 0, 'failed' => 0, 'conflicts' => []];

        foreach ($routines as $routineData) {
            $results['total']++;

            $routine = $this->create($routineData);

            if ($routine) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['conflicts'][] = [
                    'day' => $routineData['day_of_week'],
                    'time_slot' => $routineData['time_slot_id'],
                ];
            }
        }

        return $results;
    }

    // ===================== PUBLISH =====================

    public function publishRoutines(array $ids, int $userId): void
    {
        foreach ($ids as $id) {
            $routine = Routine::where('uuid', $id)->first();
            if ($routine) {
                $routine->publish($userId);
            }
        }
    }

    // ===================== CONFLICT DETECTION =====================

    private function hasConflict(int $teacherId, int $roomId, int $dayOfWeek, int $timeSlotId): bool
    {
        // Check teacher conflict
        $teacherConflict = Routine::where('teacher_id', $teacherId)
            ->where('day_of_week', $dayOfWeek)
            ->where('time_slot_id', $timeSlotId)
            ->exists();

        if ($teacherConflict) {
            return true;
        }

        // Check room conflict
        $roomConflict = Routine::where('room_id', $roomId)
            ->where('day_of_week', $dayOfWeek)
            ->where('time_slot_id', $timeSlotId)
            ->exists();

        return $roomConflict;
    }

    public function checkConflicts(int $teacherId, int $dayOfWeek, int $timeSlotId): array
    {
        $conflicts = [];

        $teacherConflicts = Routine::where('teacher_id', $teacherId)
            ->where('day_of_week', $dayOfWeek)
            ->where('time_slot_id', $timeSlotId)
            ->with(['class', 'subject'])
            ->get();

        if ($teacherConflicts->isNotEmpty()) {
            $conflicts['teacher'] = $teacherConflicts->map(fn($r) => [
                'class' => $r->class?->name,
                'subject' => $r->subject?->subject_name,
                'day' => Routine::days()[$dayOfWeek],
            ])->toArray();
        }

        return $conflicts;
    }

    // ===================== AUTO GENERATOR =====================

    public function autoGenerate(int $sessionId, int $classId, ?int $sectionId): array
    {
        // Implementation would involve:
        // 1. Get available subjects for the class
        // 2. Get available teachers
        // 3. Get available rooms
        // 4. Get available time slots
        // 5. Generate routine respecting constraints

        return [
            'generated' => 0,
            'conflicts' => [],
            'message' => 'Auto-generation requires detailed scheduling algorithm',
        ];
    }

    // ===================== TEACHER/STUDENT ROUTINE =====================

    public function getTeacherRoutine(string $teacherUuid): array
    {
        $teacher = Teacher::where('uuid', $teacherUuid)->firstOrFail();

        $routines = Routine::where('teacher_id', $teacher->id)
            ->published()
            ->with(['class', 'section', 'subject', 'room', 'timeSlot'])
            ->orderBy('day_of_week')
            ->orderBy('time_slot_id')
            ->get();

        return $this->formatRoutineByDay($routines);
    }

    public function getStudentRoutine(string $studentUuid): array
    {
        $student = \App\Models\Student\Student::where('uuid', $studentUuid)->firstOrFail();

        $routines = Routine::where('class_id', $student->class_id)
            ->published()
            ->with(['class', 'section', 'subject', 'teacher', 'room', 'timeSlot'])
            ->orderBy('day_of_week')
            ->orderBy('time_slot_id')
            ->get();

        return $this->formatRoutineByDay($routines);
    }

    public function getClassRoutine(int $classId, ?int $sectionId): array
    {
        $query = Routine::where('class_id', $classId)->published();

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $routines = $query->with(['class', 'section', 'subject', 'teacher', 'room', 'timeSlot'])
            ->orderBy('day_of_week')
            ->orderBy('time_slot_id')
            ->get();

        return $this->formatRoutineByDay($routines);
    }

    private function formatRoutineByDay($routines): array
    {
        $formatted = [];

        foreach (Routine::days() as $day => $dayName) {
            $formatted[$day] = [
                'day' => $dayName,
                'classes' => $routines->where('day_of_week', $day)->map(fn($r) => [
                    'id' => $r->uuid,
                    'time' => $r->timeSlot ? $r->timeSlot->start_time->format('H:i') . ' - ' . $r->timeSlot->end_time->format('H:i') : null,
                    'subject' => $r->subject?->subject_name,
                    'teacher' => $r->teacher?->profile?->full_name,
                    'room' => $r->room?->room_number,
                    'type' => $r->routine_type,
                ])->values()->toArray(),
            ];
        }

        return $formatted;
    }

    // ===================== TIME SLOTS =====================

    public function getTimeSlots(): \Illuminate\Database\Eloquent\Collection
    {
        return TimeSlot::active()->ordered()->get();
    }

    public function createTimeSlot(array $data): TimeSlot
    {
        return TimeSlot::create([
            'uuid' => (string) Str::uuid(),
            'slot_name' => $data['slot_name'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'duration_minutes' => $data['duration_minutes'] ?? 50,
            'break_before' => $data['break_before'] ?? 0,
            'break_after' => $data['break_after'] ?? 0,
            'slot_order' => $data['slot_order'] ?? 1,
            'status' => 'active',
        ]);
    }

    // ===================== ROOMS =====================

    public function getRooms(?string $roomType = null, ?string $building = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = Room::active();

        if ($roomType) {
            $query->byType($roomType);
        }

        if ($building) {
            $query->where('building', $building);
        }

        return $query->orderBy('room_number')->get();
    }

    public function createRoom(array $data): Room
    {
        return Room::create([
            'uuid' => (string) Str::uuid(),
            'room_number' => $data['room_number'],
            'room_name' => $data['room_name'],
            'building' => $data['building'] ?? null,
            'floor' => $data['floor'] ?? null,
            'capacity' => $data['capacity'] ?? 40,
            'room_type' => $data['room_type'] ?? Room::TYPE_CLASSROOM,
            'has_projector' => $data['has_projector'] ?? false,
            'has_ac' => $data['has_ac'] ?? false,
            'has_computer' => $data['has_computer'] ?? false,
            'description' => $data['description'] ?? null,
            'status' => 'active',
        ]);
    }

    // ===================== CALENDAR =====================

    public function getCalendar(?int $sessionId, string $startDate, string $endDate): array
    {
        $query = AcademicCalendar::active();

        if ($sessionId) {
            $query->where('session_id', $sessionId);
        }

        $events = $query->whereBetween('start_date', [$startDate, $endDate])->get();

        return $events->map(fn($e) => [
            'id' => $e->uuid,
            'title' => $e->title,
            'start' => $e->start_date->toDateString(),
            'end' => $e->end_date?->toDateString(),
            'type' => $e->event_type,
            'color' => $e->color,
        ])->toArray();
    }

    public function createCalendarEvent(array $data): AcademicCalendar
    {
        return AcademicCalendar::create([
            'uuid' => (string) Str::uuid(),
            'session_id' => $this->getModelId(\App\Models\Academic\AcademicSession::class, $data['session_id'] ?? null),
            'title' => $data['title'],
            'title_bn' => $data['title_bn'] ?? null,
            'description' => $data['description'] ?? null,
            'event_type' => $data['event_type'] ?? 'other',
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? $data['start_date'],
            'is_all_day' => $data['is_all_day'] ?? true,
            'color' => $data['color'] ?? '#3b82f6',
            'status' => 'active',
        ]);
    }

    // ===================== HOLIDAYS =====================

    public function getHolidays(?int $year = null): \Illuminate\Database\Eloquent\Collection
    {
        $year = $year ?? now()->year;

        return Holiday::active()
            ->where(function ($query) use ($year) {
                $query->whereYear('date', $year)
                    ->orWhere('is_recurring', true);
            })
            ->orderBy('date')
            ->get();
    }

    public function createHoliday(array $data): Holiday
    {
        return Holiday::create([
            'uuid' => (string) Str::uuid(),
            'title' => $data['title'],
            'title_bn' => $data['title_bn'] ?? null,
            'description' => $data['description'] ?? null,
            'holiday_type' => $data['holiday_type'] ?? 'special',
            'date' => $data['date'],
            'end_date' => $data['end_date'] ?? $data['date'],
            'is_recurring' => $data['is_recurring'] ?? false,
            'recurring_year' => $data['recurring_year'] ?? null,
            'color' => $data['color'] ?? '#ef4444',
            'is_published' => true,
            'status' => 'active',
        ]);
    }

    public function deleteHoliday(string $uuid): bool
    {
        $holiday = Holiday::where('uuid', $uuid)->first();
        return $holiday ? $holiday->delete() : false;
    }

    // ===================== EXPORT =====================

    public function export(int $classId, string $format): string
    {
        $filename = "routine_class_{$classId}_" . now()->format('Ymd_His');
        return url("storage/exports/{$filename}.{$format}");
    }

    // ===================== HELPERS =====================

    private function getModelId(string $model, ?string $uuid): ?int
    {
        if (!$uuid) {
            return null;
        }

        $record = $model::where('uuid', $uuid)->first();
        return $record?->id;
    }
}
