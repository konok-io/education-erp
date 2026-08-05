<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Attendance\AttendanceResource;
use App\Services\Attendance\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttendanceController extends BaseController
{
    public function __construct(
        private readonly AttendanceService $attendanceService
    ) {}

    // ===================== LIST =====================

    public function index(Request $request): AnonymousResourceCollection
    {
        $attendances = $this->attendanceService->getAll(
            perPage: $request->input('per_page', 20),
            filters: $request->only([
                'type', 'date', 'session_id', 'class_id', 'section_id', 
                'subject_id', 'status', 'is_approved', 'entry_method'
            ])
        );

        return AttendanceResource::collection($attendances);
    }

    // ===================== STUDENT ATTENDANCE =====================

    public function markStudentAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late,leave,half_day',
            'attendance.*.late_minutes' => 'nullable|integer',
            'attendance.*.remarks' => 'nullable|string',
        ]);

        $result = $this->attendanceService->markStudentAttendance(
            $request->input('session_id'),
            $request->input('class_id'),
            $request->input('section_id'),
            $request->input('subject_id'),
            $request->input('date'),
            $request->input('attendance'),
            $request->input('entry_method', 'manual')
        );

        return $this->success($result, 'Student attendance marked successfully');
    }

    public function getStudentAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'session_id' => 'nullable|exists:academic_sessions,id',
            'class_id' => 'nullable|exists:classes,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $attendances = $this->attendanceService->getStudentAttendance(
            $request->input('student_id'),
            $request->input('start_date'),
            $request->input('end_date'),
            $request->only(['session_id', 'class_id'])
        );

        return $this->success($attendances);
    }

    // ===================== TEACHER ATTENDANCE =====================

    public function markTeacherAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.teacher_id' => 'required|exists:teachers,id',
            'attendance.*.status' => 'required|in:present,absent,late,leave,half_day',
            'attendance.*.late_minutes' => 'nullable|integer',
            'attendance.*.remarks' => 'nullable|string',
        ]);

        $result = $this->attendanceService->markTeacherAttendance(
            $request->input('date'),
            $request->input('attendance'),
            $request->input('entry_method', 'manual')
        );

        return $this->success($result, 'Teacher attendance marked successfully');
    }

    public function getTeacherAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $attendances = $this->attendanceService->getTeacherAttendance(
            $request->input('teacher_id'),
            $request->input('start_date'),
            $request->input('end_date')
        );

        return $this->success($attendances);
    }

    // ===================== EMPLOYEE ATTENDANCE =====================

    public function markEmployeeAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.employee_id' => 'required|exists:employees,id',
            'attendance.*.status' => 'required|in:present,absent,late,leave,half_day',
            'attendance.*.late_minutes' => 'nullable|integer',
            'attendance.*.remarks' => 'nullable|string',
        ]);

        $result = $this->attendanceService->markEmployeeAttendance(
            $request->input('date'),
            $request->input('attendance'),
            $request->input('entry_method', 'manual')
        );

        return $this->success($result, 'Employee attendance marked successfully');
    }

    public function getEmployeeAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $attendances = $this->attendanceService->getEmployeeAttendance(
            $request->input('employee_id'),
            $request->input('start_date'),
            $request->input('end_date')
        );

        return $this->success($attendances);
    }

    // ===================== QR ATTENDANCE =====================

    public function verifyQRCode(Request $request): JsonResponse
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        $result = $this->attendanceService->verifyQRCode($request->input('qr_data'));

        if (!$result) {
            return $this->error('Invalid QR code');
        }

        return $this->success($result);
    }

    public function markByQR(Request $request): JsonResponse
    {
        $request->validate([
            'qr_data' => 'required|string',
            'status' => 'required|in:present,absent,late',
            'date' => 'required|date',
        ]);

        $result = $this->attendanceService->markByQR(
            $request->input('qr_data'),
            $request->input('status'),
            $request->input('date')
        );

        if (!$result) {
            return $this->error('Invalid QR code or user');
        }

        return $this->success($result, 'Attendance marked successfully');
    }

    // ===================== APPROVAL =====================

    public function approve(string $uuid): JsonResponse
    {
        $result = $this->attendanceService->approve($uuid, auth()->id());

        if (!$result) {
            return $this->error('Attendance not found');
        }

        return $this->success(null, 'Attendance approved successfully');
    }

    public function bulkApprove(Request $request): JsonResponse
    {
        $request->validate([
            'attendance_ids' => 'required|array',
            'attendance_ids.*' => 'required|exists:attendances,id',
        ]);

        $result = $this->attendanceService->bulkApprove(
            $request->input('attendance_ids'),
            auth()->id()
        );

        return $this->success($result, 'Attendances approved successfully');
    }

    // ===================== CORRECTION =====================

    public function requestCorrection(Request $request): JsonResponse
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'new_status' => 'required|in:present,absent,late,leave,half_day',
            'reason' => 'required|string',
        ]);

        $result = $this->attendanceService->requestCorrection(
            $request->input('attendance_id'),
            $request->input('new_status'),
            $request->input('reason'),
            auth()->id()
        );

        return $this->success($result, 'Correction requested successfully');
    }

    public function reviewCorrection(Request $request, string $uuid): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'review_notes' => 'nullable|string',
        ]);

        $result = $this->attendanceService->reviewCorrection(
            $uuid,
            $request->input('status'),
            $request->input('review_notes'),
            auth()->id()
        );

        if (!$result) {
            return $this->error('Correction request not found');
        }

        return $this->success(null, 'Correction reviewed successfully');
    }

    public function getCorrectionRequests(Request $request): AnonymousResourceCollection
    {
        $corrections = $this->attendanceService->getCorrectionRequests(
            $request->input('per_page', 20),
            $request->input('status')
        );

        return AttendanceResource::collection($corrections);
    }

    // ===================== REPORTS =====================

    public function getReport(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:student,teacher,employee',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'session_id' => 'nullable|exists:academic_sessions,id',
            'class_id' => 'nullable|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $report = $this->attendanceService->getReport(
            $request->input('type'),
            $request->input('start_date'),
            $request->input('end_date'),
            $request->only(['session_id', 'class_id', 'section_id'])
        );

        return $this->success($report);
    }

    public function getClassAttendanceSummary(Request $request): JsonResponse
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
        ]);

        $summary = $this->attendanceService->getClassAttendanceSummary(
            $request->input('class_id'),
            $request->input('date')
        );

        return $this->success($summary);
    }

    // ===================== ANALYTICS =====================

    public function getAnalytics(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:student,teacher,employee',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'group_by' => 'nullable|in:day,week,month,class,section',
        ]);

        $analytics = $this->attendanceService->getAnalytics(
            $request->input('type'),
            $request->input('start_date'),
            $request->input('end_date'),
            $request->input('group_by', 'day')
        );

        return $this->success($analytics);
    }

    public function getDashboardStats(Request $request): JsonResponse
    {
        $stats = $this->attendanceService->getDashboardStats($request->input('date'));

        return $this->success($stats);
    }

    // ===================== IMPORT/EXPORT =====================

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
            'type' => 'required|in:student,teacher,employee',
        ]);

        $result = $this->attendanceService->import(
            $request->file('file'),
            $request->input('type')
        );

        return $this->success($result, 'Import completed');
    }

    public function export(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:excel,csv,pdf',
            'type' => 'required|in:student,teacher,employee',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $url = $this->attendanceService->export(
            $request->input('format'),
            $request->input('type'),
            $request->input('start_date'),
            $request->input('end_date'),
            $request->only(['session_id', 'class_id', 'section_id'])
        );

        return $this->success(['url' => $url], 'Export ready');
    }
}
