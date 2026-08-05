<?php

declare(strict_types=1);

namespace App\Services\Attendance;

use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceCorrection;
use App\Models\Student\Student;
use App\Models\Teacher\Teacher;
use App\Models\Employee\Employee;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttendanceService
{
    public function getAll(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Attendance::query()->with(['student.profile', 'teacher.profile', 'employee.profile']);

        if (!empty($filters['type'])) {
            $query->where('attendance_type', $filters['type']);
        }

        if (!empty($filters['date'])) {
            $query->byDate($filters['date']);
        }

        if (!empty($filters['session_id'])) {
            $query->bySession($filters['session_id']);
        }

        if (!empty($filters['class_id'])) {
            $query->byClass($filters['class_id']);
        }

        if (!empty($filters['section_id'])) {
            $query->bySection($filters['section_id']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['is_approved'])) {
            if ($filters['is_approved']) {
                $query->approved();
            } else {
                $query->pending();
            }
        }

        if (!empty($filters['entry_method'])) {
            $query->where('entry_method', $filters['entry_method']);
        }

        return $query->orderBy('attendance_date', 'desc')->paginate($perPage);
    }

    // ===================== STUDENT ATTENDANCE =====================

    public function markStudentAttendance(
        int $sessionId,
        int $classId,
        ?int $sectionId,
        ?int $subjectId,
        string $date,
        array $attendanceData,
        string $entryMethod = 'manual'
    ): array {
        $results = ['total' => 0, 'success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($attendanceData as $item) {
            $results['total']++;

            try {
                $student = Student::where('uuid', $item['student_id'])->first();
                
                if (!$student) {
                    $results['failed']++;
                    $results['errors'][] = "Student not found: {$item['student_id']}";
                    continue;
                }

                $exists = Attendance::where('student_id', $student->id)
                    ->where('attendance_date', $date)
                    ->where('class_id', $classId)
                    ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                    ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
                    ->exists();

                if ($exists) {
                    $results['failed']++;
                    $results['errors'][] = "Attendance already exists for student: {$student->student_no}";
                    continue;
                }

                Attendance::create([
                    'uuid' => (string) Str::uuid(),
                    'attendance_no' => Attendance::generateAttendanceNo(),
                    'attendance_type' => Attendance::TYPE_STUDENT,
                    'attendance_date' => $date,
                    'attendance_time' => now(),
                    'session_id' => $sessionId,
                    'class_id' => $classId,
                    'section_id' => $sectionId,
                    'subject_id' => $subjectId,
                    'student_id' => $student->id,
                    'status' => $item['status'],
                    'late_minutes' => $item['late_minutes'] ?? 0,
                    'remarks' => $item['remarks'] ?? null,
                    'entry_method' => $entryMethod,
                    'entry_by' => auth()->id(),
                    'is_approved' => false,
                ]);

                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = $e->getMessage();
            }
        }

        return $results;
    }

    public function getStudentAttendance(
        string $studentId,
        string $startDate,
        string $endDate,
        array $filters = []
    ): array {
        $student = Student::where('uuid', $studentId)->first();

        if (!$student) {
            return [];
        }

        $query = Attendance::studentAttendance()
            ->where('student_id', $student->id)
            ->byDate($startDate)
            ->where('attendance_date', '<=', $endDate);

        if (!empty($filters['session_id'])) {
            $session = \App\Models\Academic\AcademicSession::where('uuid', $filters['session_id'])->first();
            if ($session) {
                $query->bySession($session->id);
            }
        }

        return $query->orderBy('attendance_date')->get()->toArray();
    }

    // ===================== TEACHER ATTENDANCE =====================

    public function markTeacherAttendance(
        string $date,
        array $attendanceData,
        string $entryMethod = 'manual'
    ): array {
        $results = ['total' => 0, 'success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($attendanceData as $item) {
            $results['total']++;

            try {
                $teacher = Teacher::where('uuid', $item['teacher_id'])->first();
                
                if (!$teacher) {
                    $results['failed']++;
                    $results['errors'][] = "Teacher not found: {$item['teacher_id']}";
                    continue;
                }

                $exists = Attendance::where('teacher_id', $teacher->id)
                    ->where('attendance_date', $date)
                    ->exists();

                if ($exists) {
                    $results['failed']++;
                    $results['errors'][] = "Attendance already exists for teacher: {$teacher->teacher_no}";
                    continue;
                }

                Attendance::create([
                    'uuid' => (string) Str::uuid(),
                    'attendance_no' => Attendance::generateAttendanceNo(),
                    'attendance_type' => Attendance::TYPE_TEACHER,
                    'attendance_date' => $date,
                    'attendance_time' => now(),
                    'campus_id' => $teacher->campus_id,
                    'teacher_id' => $teacher->id,
                    'status' => $item['status'],
                    'late_minutes' => $item['late_minutes'] ?? 0,
                    'remarks' => $item['remarks'] ?? null,
                    'entry_method' => $entryMethod,
                    'entry_by' => auth()->id(),
                    'is_approved' => false,
                ]);

                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = $e->getMessage();
            }
        }

        return $results;
    }

    public function getTeacherAttendance(
        string $teacherId,
        string $startDate,
        string $endDate
    ): array {
        $teacher = Teacher::where('uuid', $teacherId)->first();

        if (!$teacher) {
            return [];
        }

        return Attendance::teacherAttendance()
            ->where('teacher_id', $teacher->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->orderBy('attendance_date')
            ->get()
            ->toArray();
    }

    // ===================== EMPLOYEE ATTENDANCE =====================

    public function markEmployeeAttendance(
        string $date,
        array $attendanceData,
        string $entryMethod = 'manual'
    ): array {
        $results = ['total' => 0, 'success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($attendanceData as $item) {
            $results['total']++;

            try {
                $employee = Employee::where('uuid', $item['employee_id'])->first();
                
                if (!$employee) {
                    $results['failed']++;
                    $results['errors'][] = "Employee not found: {$item['employee_id']}";
                    continue;
                }

                $exists = Attendance::where('employee_id', $employee->id)
                    ->where('attendance_date', $date)
                    ->exists();

                if ($exists) {
                    $results['failed']++;
                    $results['errors'][] = "Attendance already exists for employee: {$employee->employee_no}";
                    continue;
                }

                Attendance::create([
                    'uuid' => (string) Str::uuid(),
                    'attendance_no' => Attendance::generateAttendanceNo(),
                    'attendance_type' => Attendance::TYPE_EMPLOYEE,
                    'attendance_date' => $date,
                    'attendance_time' => now(),
                    'campus_id' => $employee->campus_id,
                    'employee_id' => $employee->id,
                    'status' => $item['status'],
                    'late_minutes' => $item['late_minutes'] ?? 0,
                    'remarks' => $item['remarks'] ?? null,
                    'entry_method' => $entryMethod,
                    'entry_by' => auth()->id(),
                    'is_approved' => false,
                ]);

                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = $e->getMessage();
            }
        }

        return $results;
    }

    public function getEmployeeAttendance(
        string $employeeId,
        string $startDate,
        string $endDate
    ): array {
        $employee = Employee::where('uuid', $employeeId)->first();

        if (!$employee) {
            return [];
        }

        return Attendance::employeeAttendance()
            ->where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->orderBy('attendance_date')
            ->get()
            ->toArray();
    }

    // ===================== QR CODE =====================

    public function verifyQRCode(string $qrData): ?array
    {
        $data = json_decode($qrData, true);

        if (!$data || !isset($data['uuid'])) {
            return null;
        }

        return [
            'uuid' => $data['uuid'],
            'id_number' => $data['id_number'] ?? null,
            'name' => $data['name'] ?? null,
            'type' => $data['type'] ?? null,
        ];
    }

    public function markByQR(string $qrData, string $status, string $date): ?array
    {
        $data = json_decode($qrData, true);

        if (!$data || !isset($data['uuid'])) {
            return null;
        }

        $attendanceData = [];

        switch ($data['type'] ?? '') {
            case 'student':
                $student = Student::where('uuid', $data['uuid'])->first();
                if ($student) {
                    $attendanceData['student_id'] = $student->id;
                }
                break;
            case 'teacher':
                $teacher = Teacher::where('uuid', $data['uuid'])->first();
                if ($teacher) {
                    $attendanceData['teacher_id'] = $teacher->id;
                }
                break;
            case 'employee':
                $employee = Employee::where('uuid', $data['uuid'])->first();
                if ($employee) {
                    $attendanceData['employee_id'] = $employee->id;
                }
                break;
        }

        if (empty($attendanceData)) {
            return null;
        }

        $attendance = Attendance::create([
            'uuid' => (string) Str::uuid(),
            'attendance_no' => Attendance::generateAttendanceNo(),
            'attendance_type' => $data['type'],
            'attendance_date' => $date,
            'attendance_time' => now(),
            'status' => $status,
            'entry_method' => Attendance::METHOD_QR,
            'entry_by' => auth()->id(),
            'qr_data' => $data,
            'is_approved' => false,
        ]);

        return $attendance->toArray();
    }

    // ===================== APPROVAL =====================

    public function approve(string $uuid, int $userId): bool
    {
        $attendance = Attendance::where('uuid', $uuid)->first();

        if (!$attendance) {
            return false;
        }

        $attendance->approve($userId);
        return true;
    }

    public function bulkApprove(array $ids, int $userId): array
    {
        $count = 0;

        foreach ($ids as $id) {
            $attendance = Attendance::where('uuid', $id)->first();
            if ($attendance) {
                $attendance->approve($userId);
                $count++;
            }
        }

        return ['approved' => $count];
    }

    // ===================== CORRECTION =====================

    public function requestCorrection(
        string $attendanceId,
        string $newStatus,
        string $reason,
        int $userId
    ): AttendanceCorrection {
        $attendance = Attendance::where('uuid', $attendanceId)->firstOrFail();

        return AttendanceCorrection::create([
            'uuid' => (string) Str::uuid(),
            'attendance_id' => $attendance->id,
            'requested_by' => $userId,
            'old_status' => $attendance->status,
            'new_status' => $newStatus,
            'reason' => $reason,
            'status' => AttendanceCorrection::STATUS_PENDING,
        ]);
    }

    public function reviewCorrection(
        string $uuid,
        string $status,
        ?string $notes,
        int $userId
    ): bool {
        $correction = AttendanceCorrection::where('uuid', $uuid)->first();

        if (!$correction) {
            return false;
        }

        if ($status === 'approved') {
            $correction->approve($userId, $notes);
        } else {
            $correction->reject($userId, $notes);
        }

        return true;
    }

    public function getCorrectionRequests(int $perPage = 20, ?string $status = null): LengthAwarePaginator
    {
        $query = AttendanceCorrection::with(['attendance', 'requester']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    // ===================== REPORTS =====================

    public function getReport(
        string $type,
        string $startDate,
        string $endDate,
        array $filters = []
    ): array {
        $query = Attendance::query()
            ->whereBetween('attendance_date', [$startDate, $endDate]);

        switch ($type) {
            case 'student':
                $query->studentAttendance()
                    ->with(['student.profile', 'class', 'section']);
                if (!empty($filters['session_id'])) {
                    $session = \App\Models\Academic\AcademicSession::where('uuid', $filters['session_id'])->first();
                    if ($session) {
                        $query->where('session_id', $session->id);
                    }
                }
                if (!empty($filters['class_id'])) {
                    $class = \App\Models\Academic\AcademicClass::where('uuid', $filters['class_id'])->first();
                    if ($class) {
                        $query->where('class_id', $class->id);
                    }
                }
                break;

            case 'teacher':
                $query->teacherAttendance()
                    ->with(['teacher.profile']);
                break;

            case 'employee':
                $query->employeeAttendance()
                    ->with(['employee.profile']);
                break;
        }

        $records = $query->get();

        return [
            'total' => $records->count(),
            'present' => $records->where('status', Attendance::STATUS_PRESENT)->count(),
            'absent' => $records->where('status', Attendance::STATUS_ABSENT)->count(),
            'late' => $records->where('status', Attendance::STATUS_LATE)->count(),
            'leave' => $records->where('status', Attendance::STATUS_LEAVE)->count(),
            'half_day' => $records->where('status', Attendance::STATUS_HALF_DAY)->count(),
            'present_percentage' => $records->count() > 0 
                ? round(($records->whereIn('status', [Attendance::STATUS_PRESENT, Attendance::STATUS_LATE])->count() / $records->count()) * 100, 2) 
                : 0,
            'records' => $records,
        ];
    }

    public function getClassAttendanceSummary(int $classId, string $date): array
    {
        $totalStudents = Student::where('class_id', $classId)->where('status', 'active')->count();

        $attendances = Attendance::where('class_id', $classId)
            ->byDate($date)
            ->studentAttendance()
            ->get();

        return [
            'date' => $date,
            'total_students' => $totalStudents,
            'marked' => $attendances->count(),
            'unmarked' => $totalStudents - $attendances->count(),
            'present' => $attendances->where('status', Attendance::STATUS_PRESENT)->count(),
            'absent' => $attendances->where('status', Attendance::STATUS_ABSENT)->count(),
            'late' => $attendances->where('status', Attendance::STATUS_LATE)->count(),
            'leave' => $attendances->where('status', Attendance::STATUS_LEAVE)->count(),
            'half_day' => $attendances->where('status', Attendance::STATUS_HALF_DAY)->count(),
            'percentage' => $totalStudents > 0 
                ? round(($attendances->whereIn('status', [Attendance::STATUS_PRESENT, Attendance::STATUS_LATE])->count() / $totalStudents) * 100, 2) 
                : 0,
        ];
    }

    // ===================== ANALYTICS =====================

    public function getAnalytics(
        string $type,
        string $startDate,
        string $endDate,
        string $groupBy = 'day'
    ): array {
        $query = Attendance::whereBetween('attendance_date', [$startDate, $endDate]);

        switch ($type) {
            case 'student':
                $query->studentAttendance();
                break;
            case 'teacher':
                $query->teacherAttendance();
                break;
            case 'employee':
                $query->employeeAttendance();
                break;
        }

        $records = $query->get();

        return [
            'total' => $records->count(),
            'present' => $records->whereIn('status', [Attendance::STATUS_PRESENT, Attendance::STATUS_LATE])->count(),
            'absent' => $records->where('status', Attendance::STATUS_ABSENT)->count(),
            'late' => $records->where('status', Attendance::STATUS_LATE)->count(),
            'leave' => $records->where('status', Attendance::STATUS_LEAVE)->count(),
            'percentage' => $records->count() > 0 
                ? round(($records->whereIn('status', [Attendance::STATUS_PRESENT, Attendance::STATUS_LATE])->count() / $records->count()) * 100, 2) 
                : 0,
            'by_status' => $records->groupBy('status')->map->count(),
            'by_date' => $records->groupBy(fn($a) => $a->attendance_date->format('Y-m-d'))->map->count(),
        ];
    }

    public function getDashboardStats(?string $date = null): array
    {
        $date = $date ?? now()->toDateString();

        return [
            'student' => [
                'total' => Attendance::studentAttendance()->byDate($date)->count(),
                'present' => Attendance::studentAttendance()->byDate($date)->byStatus(Attendance::STATUS_PRESENT)->count(),
                'absent' => Attendance::studentAttendance()->byDate($date)->byStatus(Attendance::STATUS_ABSENT)->count(),
                'late' => Attendance::studentAttendance()->byDate($date)->byStatus(Attendance::STATUS_LATE)->count(),
            ],
            'teacher' => [
                'total' => Attendance::teacherAttendance()->byDate($date)->count(),
                'present' => Attendance::teacherAttendance()->byDate($date)->byStatus(Attendance::STATUS_PRESENT)->count(),
                'absent' => Attendance::teacherAttendance()->byDate($date)->byStatus(Attendance::STATUS_ABSENT)->count(),
                'late' => Attendance::teacherAttendance()->byDate($date)->byStatus(Attendance::STATUS_LATE)->count(),
            ],
            'employee' => [
                'total' => Attendance::employeeAttendance()->byDate($date)->count(),
                'present' => Attendance::employeeAttendance()->byDate($date)->byStatus(Attendance::STATUS_PRESENT)->count(),
                'absent' => Attendance::employeeAttendance()->byDate($date)->byStatus(Attendance::STATUS_ABSENT)->count(),
                'late' => Attendance::employeeAttendance()->byDate($date)->byStatus(Attendance::STATUS_LATE)->count(),
            ],
            'pending_approvals' => Attendance::pending()->count(),
            'pending_corrections' => AttendanceCorrection::where('status', AttendanceCorrection::STATUS_PENDING)->count(),
        ];
    }

    // ===================== IMPORT/EXPORT =====================

    public function import(UploadedFile $file, string $type): array
    {
        // Import logic would go here
        return ['total' => 0, 'success' => 0, 'failed' => 0, 'errors' => []];
    }

    public function export(string $format, string $type, string $startDate, string $endDate, array $filters = []): string
    {
        $filename = "{$type}_attendance_{$startDate}_{$endDate}";
        return url("storage/exports/{$filename}.{$format}");
    }
}
