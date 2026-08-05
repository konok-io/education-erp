<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admission;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Admission\ApplicationResource;
use App\Services\Admission\AdmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdmissionController extends BaseController
{
    public function __construct(
        private readonly AdmissionService $admissionService
    ) {}

    // ===================== CAMPAIGNS =====================

    public function getCampaigns(Request $request): AnonymousResourceCollection
    {
        $campaigns = $this->admissionService->getCampaigns(
            $request->input('per_page', 20),
            $request->only(['status', 'academic_level_id'])
        );

        return ApplicationResource::collection($campaigns);
    }

    public function createCampaign(Request $request): JsonResponse
    {
        $campaign = $this->admissionService->createCampaign($request->all());

        return $this->created($campaign, 'Campaign created successfully');
    }

    public function updateCampaign(Request $request, string $uuid): JsonResponse
    {
        $campaign = $this->admissionService->updateCampaign($uuid, $request->all());

        return $this->updated($campaign, 'Campaign updated successfully');
    }

    public function toggleCampaign(string $uuid): JsonResponse
    {
        $campaign = $this->admissionService->toggleCampaign($uuid);

        return $this->success($campaign, $campaign->is_active ? 'Campaign activated' : 'Campaign deactivated');
    }

    // ===================== APPLICATIONS =====================

    public function getApplications(Request $request): AnonymousResourceCollection
    {
        $applications = $this->admissionService->getApplications(
            $request->input('per_page', 50),
            $request->only([
                'campaign_id', 'status', 'quota', 'payment_status',
                'search', 'date_from', 'date_to'
            ])
        );

        return ApplicationResource::collection($applications);
    }

    public function getApplication(string $uuid): JsonResponse
    {
        $application = $this->admissionService->getApplication($uuid);

        if (!$application) {
            return $this->notFound('Application not found');
        }

        return $this->success(new ApplicationResource($application));
    }

    public function createApplication(Request $request): JsonResponse
    {
        $request->validate([
            'campaign_id' => 'required|exists:admission_campaigns,id',
            'applicant_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'email' => 'required|email',
            'mobile' => 'required|string|max:20',
        ]);

        $application = $this->admissionService->createApplication($request->all());

        return $this->created(new ApplicationResource($application), 'Application created successfully');
    }

    public function updateApplication(Request $request, string $uuid): JsonResponse
    {
        $application = $this->admissionService->updateApplication($uuid, $request->all());

        return $this->updated(new ApplicationResource($application), 'Application updated successfully');
    }

    public function submitApplication(string $uuid): JsonResponse
    {
        $this->admissionService->submitApplication($uuid);

        return $this->success(null, 'Application submitted successfully');
    }

    // ===================== DOCUMENTS =====================

    public function uploadDocument(Request $request): JsonResponse
    {
        $request->validate([
            'application_id' => 'required|exists:admission_applications,id',
            'document_type' => 'required|string',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
        ]);

        $document = $this->admissionService->uploadDocument(
            $request->input('application_id'),
            $request->input('document_type'),
            $request->file('file')
        );

        return $this->created($document, 'Document uploaded successfully');
    }

    public function verifyDocument(Request $request, string $uuid): JsonResponse
    {
        $request->validate([
            'is_verified' => 'required|boolean',
            'rejection_reason' => 'nullable|string',
        ]);

        $this->admissionService->verifyDocument(
            $uuid,
            $request->input('is_verified'),
            $request->input('rejection_reason'),
            auth()->id()
        );

        return $this->success(null, 'Document verified');
    }

    // ===================== PAYMENTS =====================

    public function initiatePayment(Request $request): JsonResponse
    {
        $request->validate([
            'application_id' => 'required|exists:admission_applications,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
        ]);

        $payment = $this->admissionService->initiatePayment(
            $request->input('application_id'),
            $request->input('amount'),
            $request->input('payment_method'),
            $request->input('transaction_id')
        );

        return $this->success($payment, 'Payment initiated');
    }

    public function verifyPayment(Request $request, string $uuid): JsonResponse
    {
        $this->admissionService->verifyPayment($uuid, auth()->id());

        return $this->success(null, 'Payment verified');
    }

    // ===================== MERIT & APPROVAL =====================

    public function generateMeritList(Request $request): JsonResponse
    {
        $request->validate([
            'campaign_id' => 'required|exists:admission_campaigns,id',
        ]);

        $result = $this->admissionService->generateMeritList($request->input('campaign_id'));

        return $this->success($result, 'Merit list generated');
    }

    public function updateMeritPosition(Request $request, string $uuid): JsonResponse
    {
        $request->validate([
            'merit_position' => 'required|integer|min:1',
        ]);

        $application = $this->admissionService->updateMeritPosition($uuid, $request->input('merit_position'));

        return $this->updated($application, 'Merit position updated');
    }

    public function approveApplication(string $uuid): JsonResponse
    {
        $result = $this->admissionService->approveApplication($uuid, auth()->id());

        return $this->success($result, 'Application approved and student created');
    }

    public function rejectApplication(Request $request, string $uuid): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        $this->admissionService->rejectApplication($uuid, $request->input('reason'));

        return $this->success(null, 'Application rejected');
    }

    // ===================== INTERVIEW =====================

    public function scheduleInterview(Request $request, string $uuid): JsonResponse
    {
        $request->validate([
            'interview_date' => 'required|date',
            'interview_time' => 'required',
            'interview_venue' => 'required|string',
        ]);

        $application = $this->admissionService->scheduleInterview(
            $uuid,
            $request->input('interview_date'),
            $request->input('interview_time'),
            $request->input('interview_venue')
        );

        return $this->success($application, 'Interview scheduled');
    }

    // ===================== DASHBOARD =====================

    public function getDashboard(Request $request): JsonResponse
    {
        $request->validate([
            'campaign_id' => 'nullable|exists:admission_campaigns,id',
        ]);

        $dashboard = $this->admissionService->getDashboard($request->input('campaign_id'));

        return $this->success($dashboard);
    }

    public function getApplicantDashboard(string $applicationNo): JsonResponse
    {
        $dashboard = $this->admissionService->getApplicantDashboard($applicationNo);

        return $this->success($dashboard);
    }

    // ===================== REPORTS =====================

    public function getReport(Request $request): JsonResponse
    {
        $request->validate([
            'campaign_id' => 'required|exists:admission_campaigns,id',
        ]);

        $report = $this->admissionService->getReport(
            $request->input('campaign_id'),
            $request->only(['status', 'quota'])
        );

        return $this->success($report);
    }

    // ===================== EXPORT =====================

    public function exportApplications(Request $request): JsonResponse
    {
        $request->validate([
            'campaign_id' => 'required|exists:admission_campaigns,id',
            'format' => 'required|in:excel,csv,pdf',
        ]);

        $url = $this->admissionService->exportApplications(
            $request->input('campaign_id'),
            $request->input('format'),
            $request->only(['status', 'quota'])
        );

        return $this->success(['url' => $url], 'Export ready');
    }
}
