<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Teacher\TeacherResource;
use App\Services\Teacher\TeacherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeacherController extends BaseController
{
    public function __construct(
        private readonly TeacherService $teacherService
    ) {}

    // ===================== CRUD =====================

    public function index(Request $request): AnonymousResourceCollection
    {
        $teachers = $this->teacherService->getAll(
            perPage: $request->input('per_page', 20),
            filters: $request->only([
                'search', 'department_id', 'status', 'employment_type', 'designation_id'
            ])
        );

        return TeacherResource::collection($teachers);
    }

    public function store(Request $request): JsonResponse
    {
        $teacher = $this->teacherService->create($request->all());

        return $this->created(
            new TeacherResource($teacher->load($this->teacherService->getRelations())),
            'Teacher registered successfully'
        );
    }

    public function show(string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        return $this->success(
            new TeacherResource($teacher->load($this->teacherService->getRelations()))
        );
    }

    public function update(Request $request, string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $updatedTeacher = $this->teacherService->update($teacher, $request->all());

        return $this->updated(
            new TeacherResource($updatedTeacher->load($this->teacherService->getRelations())),
            'Teacher updated successfully'
        );
    }

    public function destroy(string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $this->teacherService->delete($teacher);

        return $this->deleted('Teacher deleted successfully');
    }

    // ===================== SEARCH =====================

    public function search(Request $request): AnonymousResourceCollection
    {
        $teachers = $this->teacherService->search(
            query: $request->input('q'),
            perPage: $request->input('per_page', 20)
        );

        return TeacherResource::collection($teachers);
    }

    public function findByTeacherNo(string $teacherNo): JsonResponse
    {
        $teacher = $this->teacherService->findByTeacherNo($teacherNo);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        return $this->success(
            new TeacherResource($teacher->load($this->teacherService->getRelations()))
        );
    }

    // ===================== PROFILE =====================

    public function updateProfile(Request $request, string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $this->teacherService->updateProfile($teacher, $request->all());

        return $this->success(null, 'Profile updated successfully');
    }

    public function updatePhoto(Request $request, string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $photoUrl = $this->teacherService->updatePhoto($teacher, $request->file('photo'));

        return $this->success(['photo_url' => $photoUrl], 'Photo updated successfully');
    }

    // ===================== QUALIFICATIONS =====================

    public function addQualification(Request $request, string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $qualification = $this->teacherService->addQualification($teacher, $request->all());

        return $this->success($qualification, 'Qualification added successfully');
    }

    public function updateQualification(Request $request, string $uuid, string $qualUuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $qualification = $this->teacherService->updateQualification($teacher, $qualUuid, $request->all());

        return $this->success($qualification, 'Qualification updated successfully');
    }

    public function deleteQualification(string $uuid, string $qualUuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $this->teacherService->deleteQualification($teacher, $qualUuid);

        return $this->deleted('Qualification deleted successfully');
    }

    // ===================== EXPERIENCES =====================

    public function addExperience(Request $request, string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $experience = $this->teacherService->addExperience($teacher, $request->all());

        return $this->success($experience, 'Experience added successfully');
    }

    public function updateExperience(Request $request, string $uuid, string $expUuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $experience = $this->teacherService->updateExperience($teacher, $expUuid, $request->all());

        return $this->success($experience, 'Experience updated successfully');
    }

    public function deleteExperience(string $uuid, string $expUuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $this->teacherService->deleteExperience($teacher, $expUuid);

        return $this->deleted('Experience deleted successfully');
    }

    // ===================== SUBJECT ASSIGNMENT =====================

    public function assignSubjects(Request $request, string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $this->teacherService->assignSubjects($teacher, $request->all());

        return $this->success(null, 'Subjects assigned successfully');
    }

    public function removeSubject(string $uuid, string $assignmentUuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $this->teacherService->removeSubject($teacher, $assignmentUuid);

        return $this->success(null, 'Subject removed successfully');
    }

    public function getAssignedSubjects(string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        return $this->success($teacher->subjectAssignments()->with(['subject', 'program', 'session'])->get());
    }

    // ===================== CLASS ASSIGNMENT =====================

    public function assignClasses(Request $request, string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $this->teacherService->assignClasses($teacher, $request->all());

        return $this->success(null, 'Classes assigned successfully');
    }

    public function removeClass(string $uuid, string $assignmentUuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $this->teacherService->removeClass($teacher, $assignmentUuid);

        return $this->success(null, 'Class removed successfully');
    }

    public function getAssignedClasses(string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        return $this->success($teacher->classAssignments()->with(['class', 'section', 'session'])->get());
    }

    // ===================== SALARY =====================

    public function updateSalary(Request $request, string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $salary = $this->teacherService->updateSalary($teacher, $request->all());

        return $this->success($salary, 'Salary updated successfully');
    }

    public function getSalary(string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        return $this->success($teacher->salary);
    }

    // ===================== LEAVE =====================

    public function applyLeave(Request $request, string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $leave = $this->teacherService->applyLeave($teacher, $request->all());

        return $this->success($leave, 'Leave applied successfully');
    }

    public function getLeaveHistory(string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        return $this->success($teacher->leaves);
    }

    // ===================== STATUS =====================

    public function updateStatus(Request $request, string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $request->validate([
            'status' => ['required', 'string', 'in:pending,active,inactive,on_leave,suspended,retired,resigned,terminated'],
            'remarks' => ['nullable', 'string'],
        ]);

        $this->teacherService->updateStatus($teacher, $request->status, $request->remarks);

        return $this->success(null, 'Status updated successfully');
    }

    // ===================== QR CODE =====================

    public function generateQRCode(string $uuid): JsonResponse
    {
        $teacher = $this->teacherService->findByUuid($uuid);

        if (!$teacher) {
            return $this->notFound('Teacher not found');
        }

        $qrCode = $this->teacherService->generateQRCode($teacher);

        return $this->success(['qr_code' => $qrCode]);
    }

    // ===================== IMPORT/EXPORT =====================

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,xlsx,xls', 'max:10240'],
        ]);

        $result = $this->teacherService->import($request->file('file'));

        return $this->success($result, 'Import completed');
    }

    public function export(Request $request): JsonResponse
    {
        $url = $this->teacherService->export(
            $request->input('format', 'excel'),
            $request->only(['department_id', 'status'])
        );

        return $this->success(['url' => $url], 'Export ready');
    }

    // ===================== STATISTICS =====================

    public function statistics(): JsonResponse
    {
        $stats = $this->teacherService->getStatistics();

        return $this->success($stats);
    }

    public function activeCount(): JsonResponse
    {
        $count = $this->teacherService->getActiveCount();

        return $this->success(['count' => $count]);
    }
}
