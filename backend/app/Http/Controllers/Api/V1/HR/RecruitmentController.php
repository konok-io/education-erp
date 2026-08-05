<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\HR;

use App\Http\Controllers\Controller;
use App\Services\HR\RecruitmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecruitmentController extends Controller
{
    public function __construct(
        private readonly RecruitmentService $recruitmentService
    ) {}

    // ===================== JOB CIRCULAR =====================

    public function getJobCirculars(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'department_id', 'search', 'is_active']);
        $perPage = (int) $request->get('per_page', 20);

        $circulars = $this->recruitmentService->getJobCirculars($perPage, $filters);

        return response()->json([
            'data' => $circulars->items(),
            'meta' => [
                'current_page' => $circulars->currentPage(),
                'last_page' => $circulars->lastPage(),
                'per_page' => $circulars->perPage(),
                'total' => $circulars->total(),
            ],
        ]);
    }

    public function createJobCircular(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'job_code' => 'nullable|string|max:50',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'employment_type_id' => 'nullable|exists:employment_types,id',
            'vacancy' => 'nullable|integer|min:1',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0',
            'salary_range' => 'nullable|string',
            'application_deadline' => 'nullable|date',
            'published_date' => 'nullable|date',
            'interview_date' => 'nullable|date',
            'status' => 'nullable|in:draft,published,closed,cancelled',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $circular = $this->recruitmentService->createJobCircular($validated);

        return response()->json(['data' => $circular], 201);
    }

    public function publishJobCircular(string $uuid): JsonResponse
    {
        $circular = $this->recruitmentService->publishJobCircular($uuid);
        return response()->json(['data' => $circular]);
    }

    public function closeJobCircular(string $uuid): JsonResponse
    {
        $circular = $this->recruitmentService->closeJobCircular($uuid);
        return response()->json(['data' => $circular]);
    }

    // ===================== JOB APPLICATIONS =====================

    public function getJobApplications(Request $request): JsonResponse
    {
        $filters = $request->only(['job_circular_id', 'status', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $applications = $this->recruitmentService->getJobApplications($perPage, $filters);

        return response()->json([
            'data' => $applications->items(),
            'meta' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total(),
            ],
        ]);
    }

    public function createJobApplication(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'job_circular_id' => 'required|exists:job_circulars,id',
            'full_name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:20',
            'blood_group' => 'nullable|string|max:10',
            'religion' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:20',
            'nid' => 'nullable|string|max:50',
            'passport' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'alternative_mobile' => 'nullable|string|max:20',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'photo' => 'nullable|string',
            'cv' => 'nullable|string',
            'cover_letter' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $application = $this->recruitmentService->createJobApplication($validated);

        return response()->json(['data' => $application], 201);
    }

    public function updateApplicationStatus(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:applied,under_review,shortlisted,interview_scheduled,interviewed,selected,rejected,waiting_list,withdrawn',
            'reason' => 'nullable|string',
        ]);

        $application = $this->recruitmentService->updateApplicationStatus(
            $uuid,
            $validated['status'],
            $validated['reason'] ?? null
        );

        return response()->json(['data' => $application]);
    }

    // ===================== INTERVIEWS =====================

    public function getInterviews(Request $request): JsonResponse
    {
        $filters = $request->only(['job_circular_id', 'job_application_id', 'decision', 'date_from', 'date_to']);
        $perPage = (int) $request->get('per_page', 20);

        $interviews = $this->recruitmentService->getInterviews($perPage, $filters);

        return response()->json([
            'data' => $interviews->items(),
            'meta' => [
                'current_page' => $interviews->currentPage(),
                'last_page' => $interviews->lastPage(),
                'per_page' => $interviews->perPage(),
                'total' => $interviews->total(),
            ],
        ]);
    }

    public function scheduleInterview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'job_circular_id' => 'required|exists:job_circulars,id',
            'job_application_id' => 'required|exists:job_applications,id',
            'interview_date' => 'required|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'venue' => 'nullable|string|max:255',
            'interview_type' => 'nullable|in:personal,panel,written,practical',
            'panel_members' => 'nullable|array',
            'total_marks' => 'nullable|numeric|min:0',
        ]);

        $interview = $this->recruitmentService->scheduleInterview($validated);

        return response()->json(['data' => $interview], 201);
    }

    public function evaluateCandidate(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'obtained_marks' => 'nullable|numeric|min:0',
            'questions' => 'nullable|string',
            'answers' => 'nullable|string',
            'remarks' => 'nullable|string',
            'feedback' => 'nullable|string',
            'evaluation_scores' => 'nullable|array',
            'decision' => 'nullable|in:pending,selected,rejected,waiting_list,hold',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        $interview = $this->recruitmentService->evaluateCandidate($uuid, $validated);

        return response()->json(['data' => $interview]);
    }

    // ===================== OFFER LETTERS =====================

    public function getOfferLetters(Request $request): JsonResponse
    {
        $filters = $request->only(['status']);
        $perPage = (int) $request->get('per_page', 20);

        $offers = $this->recruitmentService->getOfferLetters($perPage, $filters);

        return response()->json([
            'data' => $offers->items(),
            'meta' => [
                'current_page' => $offers->currentPage(),
                'last_page' => $offers->lastPage(),
                'per_page' => $offers->perPage(),
                'total' => $offers->total(),
            ],
        ]);
    }

    public function createOfferLetter(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'job_circular_id' => 'required|exists:job_circulars,id',
            'job_application_id' => 'required|exists:job_applications,id',
            'interview_id' => 'nullable|exists:interviews,id',
            'candidate_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'designation_id' => 'required|exists:designations,id',
            'department_id' => 'nullable|exists:departments,id',
            'employment_type_id' => 'required|exists:employment_types,id',
            'salary_grade_id' => 'nullable|exists:salary_grades,id',
            'offered_salary' => 'nullable|numeric|min:0',
            'offer_date' => 'required|date',
            'joining_date' => 'required|date',
            'terms_conditions' => 'nullable|string',
            'benefits' => 'nullable|string',
        ]);

        $offer = $this->recruitmentService->createOfferLetter($validated);

        return response()->json(['data' => $offer], 201);
    }

    public function sendOfferLetter(string $uuid): JsonResponse
    {
        $offer = $this->recruitmentService->sendOfferLetter($uuid);
        return response()->json(['data' => $offer]);
    }

    public function acceptOfferLetter(string $uuid): JsonResponse
    {
        $offer = $this->recruitmentService->acceptOfferLetter($uuid);
        return response()->json(['data' => $offer]);
    }

    public function declineOfferLetter(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string',
        ]);

        $offer = $this->recruitmentService->declineOfferLetter($uuid, $validated['reason'] ?? null);
        return response()->json(['data' => $offer]);
    }

    public function markJoined(string $uuid): JsonResponse
    {
        $offer = $this->recruitmentService->markJoined($uuid);
        return response()->json(['data' => $offer]);
    }

    // ===================== STATISTICS =====================

    public function getRecruitmentStats(): JsonResponse
    {
        $stats = $this->recruitmentService->getRecruitmentStats();
        return response()->json(['data' => $stats]);
    }
}
