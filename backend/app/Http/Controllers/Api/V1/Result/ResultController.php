<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Result;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Result\ResultResource;
use App\Services\Result\ResultService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ResultController extends BaseController
{
    public function __construct(
        private readonly ResultService $resultService
    ) {}

    // ===================== EXAM CRUD =====================

    public function getExams(Request $request): AnonymousResourceCollection
    {
        $exams = $this->resultService->getExams(
            perPage: $request->input('per_page', 20),
            filters: $request->only(['session_id', 'status', 'exam_type'])
        );

        return ResultResource::collection($exams);
    }

    public function createExam(Request $request): JsonResponse
    {
        $exam = $this->resultService->createExam($request->all());

        return $this->created($exam, 'Exam created successfully');
    }

    public function updateExam(Request $request, string $uuid): JsonResponse
    {
        $exam = $this->resultService->updateExam($uuid, $request->all());

        return $this->updated($exam, 'Exam updated successfully');
    }

    public function deleteExam(string $uuid): JsonResponse
    {
        $this->resultService->deleteExam($uuid);

        return $this->deleted('Exam deleted successfully');
    }

    // ===================== MARK ENTRY =====================

    public function entryMarks(Request $request): JsonResponse
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.theory' => 'nullable|numeric|min:0',
            'marks.*.practical' => 'nullable|numeric|min:0',
            'marks.*.viva' => 'nullable|numeric|min:0',
            'marks.*.attendance' => 'nullable|numeric|min:0',
            'marks.*.assignment' => 'nullable|numeric|min:0',
            'marks.*.internal' => 'nullable|numeric|min:0',
        ]);

        $result = $this->resultService->entryMarks(
            $request->input('exam_id'),
            $request->input('subject_id'),
            $request->input('marks'),
            auth()->id()
        );

        return $this->success($result, 'Marks entered successfully');
    }

    public function updateMarks(Request $request, string $uuid): JsonResponse
    {
        $request->validate([
            'theory' => 'nullable|numeric|min:0',
            'practical' => 'nullable|numeric|min:0',
            'viva' => 'nullable|numeric|min:0',
            'attendance' => 'nullable|numeric|min:0',
            'assignment' => 'nullable|numeric|min:0',
            'internal' => 'nullable|numeric|min:0',
        ]);

        $result = $this->resultService->updateMarks($uuid, $request->all());

        return $this->updated($result, 'Marks updated successfully');
    }

    public function getStudentResults(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'session_id' => 'nullable|exists:academic_sessions,id',
            'semester_id' => 'nullable|exists:semesters,id',
        ]);

        $results = $this->resultService->getStudentResults(
            $request->input('student_id'),
            $request->only(['session_id', 'semester_id'])
        );

        return $this->success($results);
    }

    // ===================== RESULT PROCESSING =====================

    public function processResults(Request $request): JsonResponse
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        $result = $this->resultService->processResults($request->input('exam_id'));

        return $this->success($result, 'Results processed successfully');
    }

    public function getClassResults(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'nullable|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $results = $this->resultService->getClassResults(
            $request->input('exam_id'),
            $request->only(['class_id', 'section_id'])
        );

        return ResultResource::collection($results);
    }

    // ===================== GPA/CGPA =====================

    public function calculateGPA(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'semester_id' => 'nullable|exists:semesters,id',
        ]);

        $gpa = $this->resultService->calculateGPA(
            $request->input('student_id'),
            $request->input('semester_id')
        );

        return $this->success($gpa);
    }

    public function calculateCGPA(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $cgpa = $this->resultService->calculateCGPA($request->input('student_id'));

        return $this->success($cgpa);
    }

    // ===================== PUBLISH/APPROVE =====================

    public function verifyResult(string $uuid): JsonResponse
    {
        $this->resultService->verifyResult($uuid, auth()->id());

        return $this->success(null, 'Result verified successfully');
    }

    public function approveResult(string $uuid): JsonResponse
    {
        $this->resultService->approveResult($uuid, auth()->id());

        return $this->success(null, 'Result approved successfully');
    }

    public function publishResult(Request $request): JsonResponse
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        $this->resultService->publishResults($request->input('exam_id'), auth()->id());

        return $this->success(null, 'Results published successfully');
    }

    public function lockResult(string $uuid): JsonResponse
    {
        $this->resultService->lockResult($uuid);

        return $this->success(null, 'Result locked successfully');
    }

    // ===================== TRANSCRIPT/MARKSHEET =====================

    public function getTranscript(string $studentId): JsonResponse
    {
        $transcript = $this->resultService->generateTranscript($studentId);

        return $this->success($transcript);
    }

    public function getMarksheet(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'exam_id' => 'required|exists:exams,id',
        ]);

        $marksheet = $this->resultService->generateMarksheet(
            $request->input('student_id'),
            $request->input('exam_id')
        );

        return $this->success($marksheet);
    }

    // ===================== MERIT LIST =====================

    public function getMeritList(Request $request): JsonResponse
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'nullable|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $meritList = $this->resultService->generateMeritList(
            $request->input('exam_id'),
            $request->input('class_id'),
            $request->input('section_id'),
            $request->input('limit', 100)
        );

        return $this->success($meritList);
    }

    public function getFailList(Request $request): JsonResponse
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $failList = $this->resultService->getFailList(
            $request->input('exam_id'),
            $request->input('class_id')
        );

        return $this->success($failList);
    }

    // ===================== ANALYTICS =====================

    public function getAnalytics(Request $request): JsonResponse
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        $analytics = $this->resultService->getAnalytics($request->input('exam_id'));

        return $this->success($analytics);
    }

    public function getSubjectAnalysis(Request $request): JsonResponse
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        $analysis = $this->resultService->getSubjectAnalysis(
            $request->input('exam_id'),
            $request->input('subject_id')
        );

        return $this->success($analysis);
    }

    // ===================== RE-SCRUTINY =====================

    public function applyReScrutiny(Request $request): JsonResponse
    {
        $request->validate([
            'result_detail_id' => 'required|exists:result_details,id',
            'reason' => 'required|string',
            'fee_amount' => 'nullable|numeric',
        ]);

        $scrutiny = $this->resultService->applyReScrutiny(
            $request->input('result_detail_id'),
            $request->input('reason'),
            $request->input('fee_amount', 0)
        );

        return $this->success($scrutiny, 'Re-scrutiny application submitted');
    }

    public function reviewReScrutiny(Request $request, string $uuid): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:completed,rejected',
            'new_marks' => 'nullable|numeric',
            'change_reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $result = $this->resultService->reviewReScrutiny(
            $uuid,
            $request->input('status'),
            $request->input('new_marks'),
            $request->input('change_reason'),
            $request->input('notes'),
            auth()->id()
        );

        return $this->success($result, 'Re-scrutiny reviewed successfully');
    }

    // ===================== GRADE RULES =====================

    public function getGradeRules(): JsonResponse
    {
        $rules = $this->resultService->getGradeRules();

        return $this->success($rules);
    }

    public function createGradeRule(Request $request): JsonResponse
    {
        $rule = $this->resultService->createGradeRule($request->all());

        return $this->created($rule, 'Grade rule created successfully');
    }

    // ===================== EXPORT =====================

    public function exportResults(Request $request): JsonResponse
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'format' => 'required|in:excel,csv,pdf',
        ]);

        $url = $this->resultService->exportResults(
            $request->input('exam_id'),
            $request->input('format'),
            $request->only(['class_id', 'section_id'])
        );

        return $this->success(['url' => $url], 'Export ready');
    }
}
