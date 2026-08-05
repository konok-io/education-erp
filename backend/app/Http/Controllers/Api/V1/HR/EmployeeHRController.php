<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\HR;

use App\Http\Controllers\Controller;
use App\Services\HR\OnboardingService;
use App\Services\HR\TransferService;
use App\Services\HR\ConfirmationService;
use App\Services\HR\ServiceBookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeHRController extends Controller
{
    public function __construct(
        private readonly OnboardingService $onboardingService,
        private readonly TransferService $transferService,
        private readonly ConfirmationService $confirmationService,
        private readonly ServiceBookService $serviceBookService
    ) {}

    // ===================== ONBOARDING =====================

    public function getChecklists(Request $request): JsonResponse
    {
        $category = $request->get('category');
        $checklists = $this->onboardingService->getChecklists($category);
        return response()->json(['data' => $checklists]);
    }

    public function createChecklist(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'checklist_name' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'order' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_required' => 'nullable|boolean',
        ]);

        $checklist = $this->onboardingService->createChecklist($validated);
        return response()->json(['data' => $checklist], 201);
    }

    public function getOnboardings(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'employee_id']);
        $perPage = (int) $request->get('per_page', 20);

        $onboardings = $this->onboardingService->getOnboardings($perPage, $filters);

        return response()->json([
            'data' => $onboardings->items(),
            'meta' => [
                'current_page' => $onboardings->currentPage(),
                'last_page' => $onboardings->lastPage(),
                'per_page' => $onboardings->perPage(),
                'total' => $onboardings->total(),
            ],
        ]);
    }

    public function startOnboarding(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'offer_letter_id' => 'nullable|exists:offer_letters,id',
            'start_date' => 'required|date',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $onboarding = $this->onboardingService->startOnboarding($validated);
        return response()->json(['data' => $onboarding], 201);
    }

    public function completeChecklist(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'checklist_id' => 'required|exists:onboarding_checklists,id',
            'remarks' => 'nullable|string',
        ]);

        $userId = auth()->id();
        $completion = $this->onboardingService->completeChecklist(
            $uuid,
            $validated['checklist_id'],
            $userId,
            $validated['remarks'] ?? null
        );

        return response()->json(['data' => $completion]);
    }

    public function getOnboardingProgress(string $uuid): JsonResponse
    {
        $progress = $this->onboardingService->getOnboardingProgress($uuid);
        return response()->json(['data' => $progress]);
    }

    public function getOnboardingStats(): JsonResponse
    {
        $stats = $this->onboardingService->getOnboardingStats();
        return response()->json(['data' => $stats]);
    }

    // ===================== TRANSFERS =====================

    public function getTransfers(Request $request): JsonResponse
    {
        $filters = $request->only([
            'status', 'employee_id', 'from_department_id',
            'to_department_id', 'date_from', 'date_to'
        ]);
        $perPage = (int) $request->get('per_page', 20);

        $transfers = $this->transferService->getTransfers($perPage, $filters);

        return response()->json([
            'data' => $transfers->items(),
            'meta' => [
                'current_page' => $transfers->currentPage(),
                'last_page' => $transfers->lastPage(),
                'per_page' => $transfers->perPage(),
                'total' => $transfers->total(),
            ],
        ]);
    }

    public function createTransfer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'to_department_id' => 'nullable|exists:departments,id',
            'to_designation_id' => 'nullable|exists:designations,id',
            'to_campus_id' => 'nullable|exists:campuses,id',
            'to_shift_id' => 'nullable|exists:employee_shifts,id',
            'reporting_manager_id' => 'nullable|exists:employees,id',
            'transfer_date' => 'required|date',
            'effective_date' => 'required|date',
            'transfer_type' => 'nullable|in:department,campus,designation,shift,reporting_manager,combined',
            'reason' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        // Get current employee details
        $employee = \App\Models\Employee\Employee::findOrFail($validated['employee_id']);
        $validated['from_department_id'] = $employee->department_id;
        $validated['from_designation_id'] = $employee->designation_id;
        $validated['from_campus_id'] = $employee->campus_id;
        $validated['from_shift_id'] = $employee->shift_id;

        $transfer = $this->transferService->createTransfer($validated);
        return response()->json(['data' => $transfer], 201);
    }

    public function recommendTransfer(string $uuid): JsonResponse
    {
        $userId = auth()->id();
        $transfer = $this->transferService->recommendTransfer($uuid, $userId);
        return response()->json(['data' => $transfer]);
    }

    public function approveTransfer(string $uuid): JsonResponse
    {
        $userId = auth()->id();
        $transfer = $this->transferService->approveTransfer($uuid, $userId);
        return response()->json(['data' => $transfer]);
    }

    public function cancelTransfer(string $uuid): JsonResponse
    {
        $transfer = $this->transferService->cancelTransfer($uuid);
        return response()->json(['data' => $transfer]);
    }

    public function getTransferStats(): JsonResponse
    {
        $stats = $this->transferService->getTransferStats();
        return response()->json(['data' => $stats]);
    }

    // ===================== CONFIRMATION =====================

    public function getConfirmations(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'employee_id', 'pending']);
        $perPage = (int) $request->get('per_page', 20);

        $confirmations = $this->confirmationService->getConfirmations($perPage, $filters);

        return response()->json([
            'data' => $confirmations->items(),
            'meta' => [
                'current_page' => $confirmations->currentPage(),
                'last_page' => $confirmations->lastPage(),
                'per_page' => $confirmations->perPage(),
                'total' => $confirmations->total(),
            ],
        ]);
    }

    public function createConfirmation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'probation_start_date' => 'required|date',
            'probation_end_date' => 'required|date|after:probation_start_date',
            'performance_summary' => 'nullable|string',
            'recommendation' => 'nullable|in:confirm,extend_probation,terminate',
            'recommendation_remarks' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $confirmation = $this->confirmationService->createConfirmation($validated);
        return response()->json(['data' => $confirmation], 201);
    }

    public function recommendConfirmation(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'recommendation' => 'required|in:confirm,extend_probation,terminate',
            'remarks' => 'nullable|string',
        ]);

        $userId = auth()->id();
        $confirmation = $this->confirmationService->recommendConfirmation($uuid, $validated, $userId);
        return response()->json(['data' => $confirmation]);
    }

    public function approveConfirmation(string $uuid): JsonResponse
    {
        $userId = auth()->id();
        $confirmation = $this->confirmationService->approveConfirmation($uuid, $userId);
        return response()->json(['data' => $confirmation]);
    }

    public function getConfirmationStats(): JsonResponse
    {
        $stats = $this->confirmationService->getConfirmationStats();
        return response()->json(['data' => $stats]);
    }

    // ===================== SERVICE BOOK =====================

    public function getServiceBooks(Request $request): JsonResponse
    {
        $filters = $request->only(['employee_id', 'event_type', 'date_from', 'date_to']);
        $perPage = (int) $request->get('per_page', 20);

        $entries = $this->serviceBookService->getServiceBooks($perPage, $filters);

        return response()->json([
            'data' => $entries->items(),
            'meta' => [
                'current_page' => $entries->currentPage(),
                'last_page' => $entries->lastPage(),
                'per_page' => $entries->perPage(),
                'total' => $entries->total(),
            ],
        ]);
    }

    public function getEmployeeServiceBook(int $employeeId): JsonResponse
    {
        $entries = $this->serviceBookService->getEmployeeServiceBook($employeeId);
        return response()->json(['data' => $entries]);
    }

    public function getServiceBookTimeline(int $employeeId): JsonResponse
    {
        $timeline = $this->serviceBookService->getServiceBookTimeline($employeeId);
        return response()->json(['data' => $timeline]);
    }

    public function getEmployeeTenure(int $employeeId): JsonResponse
    {
        $tenure = $this->serviceBookService->getEmployeeTenure($employeeId);
        return response()->json(['data' => $tenure]);
    }

    public function createServiceBookEntry(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'entry_date' => 'required|date',
            'event_type' => 'required|string',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'metadata' => 'nullable|array',
            'remarks' => 'nullable|string',
        ]);

        $entry = $this->serviceBookService->createServiceBookEntry($validated);
        return response()->json(['data' => $entry], 201);
    }
}
