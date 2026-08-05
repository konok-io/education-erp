<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Http\Requests\Student\PromoteStudentRequest;
use App\Http\Requests\Student\TransferStudentRequest;
use App\Http\Resources\Student\StudentResource;
use App\Services\Student\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StudentController extends BaseController
{
    public function __construct(
        private readonly StudentService $studentService
    ) {}

    // ===================== STUDENT CRUD =====================

    /**
     * List students.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $students = $this->studentService->getAll(
            perPage: $request->input('per_page', 20),
            filters: $request->only([
                'search', 'session_id', 'academic_level_id', 'department_id',
                'program_id', 'class_id', 'section_id', 'group_id', 'status', 'gender'
            ])
        );

        return StudentResource::collection($students);
    }

    /**
     * Store new student.
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        $student = $this->studentService->create($request->validated());

        return $this->created(
            new StudentResource($student->load($this->studentService->getRelations())),
            'Student registered successfully'
        );
    }

    /**
     * Show student.
     */
    public function show(string $uuid): JsonResponse
    {
        $student = $this->studentService->findByUuid($uuid);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        return $this->success(
            new StudentResource($student->load($this->studentService->getRelations()))
        );
    }

    /**
     * Update student.
     */
    public function update(UpdateStudentRequest $request, string $uuid): JsonResponse
    {
        $student = $this->studentService->findByUuid($uuid);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        $updatedStudent = $this->studentService->update($student, $request->validated());

        return $this->updated(
            new StudentResource($updatedStudent->load($this->studentService->getRelations())),
            'Student updated successfully'
        );
    }

    /**
     * Delete student.
     */
    public function destroy(string $uuid): JsonResponse
    {
        $student = $this->studentService->findByUuid($uuid);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        $this->studentService->delete($student);

        return $this->deleted('Student deleted successfully');
    }

    // ===================== SEARCH & LOOKUP =====================

    /**
     * Search students.
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $students = $this->studentService->search(
            query: $request->input('q'),
            perPage: $request->input('per_page', 20)
        );

        return StudentResource::collection($students);
    }

    /**
     * Get student by student number.
     */
    public function findByStudentNo(string $studentNo): JsonResponse
    {
        $student = $this->studentService->findByStudentNo($studentNo);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        return $this->success(
            new StudentResource($student->load($this->studentService->getRelations()))
        );
    }

    // ===================== PROFILE =====================

    /**
     * Update profile.
     */
    public function updateProfile(Request $request, string $uuid): JsonResponse
    {
        $student = $this->studentService->findByUuid($uuid);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        $this->studentService->updateProfile($student, $request->all());

        return $this->success(null, 'Profile updated successfully');
    }

    /**
     * Update photo.
     */
    public function updatePhoto(Request $request, string $uuid): JsonResponse
    {
        $student = $this->studentService->findByUuid($uuid);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $photoUrl = $this->studentService->updatePhoto($student, $request->file('photo'));

        return $this->success(['photo_url' => $photoUrl], 'Photo updated successfully');
    }

    // ===================== GUARDIAN =====================

    /**
     * Update guardian.
     */
    public function updateGuardian(Request $request, string $uuid): JsonResponse
    {
        $student = $this->studentService->findByUuid($uuid);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        $this->studentService->updateGuardian($student, $request->all());

        return $this->success(null, 'Guardian information updated successfully');
    }

    // ===================== MEDICAL =====================

    /**
     * Update medical info.
     */
    public function updateMedical(Request $request, string $uuid): JsonResponse
    {
        $student = $this->studentService->findByUuid($uuid);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        $this->studentService->updateMedical($student, $request->all());

        return $this->success(null, 'Medical information updated successfully');
    }

    // ===================== DOCUMENTS =====================

    /**
     * Upload document.
     */
    public function uploadDocument(Request $request, string $uuid): JsonResponse
    {
        $student = $this->studentService->findByUuid($uuid);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        $request->validate([
            'document_type' => ['required', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'issue_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date', 'after:issue_date'],
        ]);

        $document = $this->studentService->uploadDocument(
            $student,
            $request->file('file'),
            $request->only(['document_type', 'title', 'issue_date', 'expiry_date'])
        );

        return $this->success($document, 'Document uploaded successfully');
    }

    /**
     * Delete document.
     */
    public function deleteDocument(string $uuid, string $documentUuid): JsonResponse
    {
        $student = $this->studentService->findByUuid($uuid);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        $this->studentService->deleteDocument($student, $documentUuid);

        return $this->deleted('Document deleted successfully');
    }

    /**
     * Get all documents.
     */
    public function getDocuments(string $uuid): JsonResponse
    {
        $student = $this->studentService->findByUuid($uuid);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        return $this->success($student->documents);
    }

    // ===================== STATUS =====================

    /**
     * Update status.
     */
    public function updateStatus(Request $request, string $uuid): JsonResponse
    {
        $student = $this->studentService->findByUuid($uuid);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        $request->validate([
            'status' => ['required', 'string', 'in:pending,active,inactive,transferred,graduated,suspended,expelled,dropped,alumni'],
            'remarks' => ['nullable', 'string'],
        ]);

        $this->studentService->updateStatus($student, $request->status, $request->remarks);

        return $this->success(null, 'Status updated successfully');
    }

    // ===================== PROMOTION =====================

    /**
     * Promote student.
     */
    public function promote(PromoteStudentRequest $request, string $uuid): JsonResponse
    {
        $student = $this->studentService->findByUuid($uuid);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        $promotion = $this->studentService->promote($student, $request->validated());

        return $this->success($promotion, 'Student promoted successfully');
    }

    /**
     * Get promotion history.
     */
    public function promotionHistory(string $uuid): JsonResponse
    {
        $student = $this->studentService->findByUuid($uuid);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        return $this->success($student->promotions()->with(['toSession', 'toClass'])->get());
    }

    // ===================== TRANSFER =====================

    /**
     * Transfer student.
     */
    public function transfer(TransferStudentRequest $request, string $uuid): JsonResponse
    {
        $student = $this->studentService->findByUuid($uuid);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        $transfer = $this->studentService->transfer($student, $request->validated());

        return $this->success($transfer, 'Student transferred successfully');
    }

    /**
     * Get transfer history.
     */
    public function transferHistory(string $uuid): JsonResponse
    {
        $student = $this->studentService->findByUuid($uuid);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        return $this->success($student->transfers);
    }

    // ===================== QR & BARCODE =====================

    /**
     * Generate QR code.
     */
    public function generateQRCode(string $uuid): JsonResponse
    {
        $student = $this->studentService->findByUuid($uuid);

        if (!$student) {
            return $this->notFound('Student not found');
        }

        $qrCode = $this->studentService->generateQRCode($student);

        return $this->success(['qr_code' => $qrCode]);
    }

    // ===================== IMPORT/EXPORT =====================

    /**
     * Import students.
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,xlsx,xls', 'max:10240'],
            'session_id' => ['required', 'exists:academic_sessions,id'],
        ]);

        $result = $this->studentService->import($request->file('file'), $request->session_id);

        return $this->success($result, 'Import completed');
    }

    /**
     * Export students.
     */
    public function export(Request $request): JsonResponse
    {
        $url = $this->studentService->export(
            $request->input('format', 'excel'),
            $request->only(['session_id', 'program_id', 'class_id', 'status'])
        );

        return $this->success(['url' => $url], 'Export ready');
    }

    // ===================== STATISTICS =====================

    /**
     * Get student statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $stats = $this->studentService->getStatistics($request->only(['session_id', 'program_id', 'class_id']));

        return $this->success($stats);
    }

    /**
     * Get active students count.
     */
    public function activeCount(Request $request): JsonResponse
    {
        $count = $this->studentService->getActiveCount($request->input('session_id'));

        return $this->success(['count' => $count]);
    }
}
