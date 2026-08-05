<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\HR;

use App\Http\Controllers\Controller;
use App\Services\HR\TrainingService;
use App\Services\HR\AwardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingAwardController extends Controller
{
    public function __construct(
        private readonly TrainingService $trainingService,
        private readonly AwardService $awardService
    ) {}

    // ===================== TRAINING TYPES =====================

    public function getTrainingTypes(): JsonResponse
    {
        $types = $this->trainingService->getTrainingTypes();
        return response()->json(['data' => $types]);
    }

    public function createTrainingType(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'code' => 'required|string|max:20|unique:training_types,code',
            'description' => 'nullable|string',
        ]);

        $type = $this->trainingService->createTrainingType($validated);
        return response()->json(['data' => $type], 201);
    }

    // ===================== TRAINING RECORDS =====================

    public function getTrainingRecords(Request $request): JsonResponse
    {
        $filters = $request->only([
            'employee_id', 'training_type_id', 'result',
            'date_from', 'date_to'
        ]);
        $perPage = (int) $request->get('per_page', 20);

        $records = $this->trainingService->getTrainingRecords($perPage, $filters);

        return response()->json([
            'data' => $records->items(),
            'meta' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => $records->perPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function createTrainingRecord(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'training_type_id' => 'required|exists:training_types,id',
            'training_name' => 'required|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'venue' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration_days' => 'nullable|integer|min:1',
            'duration_hours' => 'nullable|integer|min:0',
            'certificate_number' => 'nullable|string|max:100',
            'certificate_date' => 'nullable|date',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $record = $this->trainingService->createTrainingRecord($validated);
        return response()->json(['data' => $record], 201);
    }

    public function updateTrainingResult(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'result' => 'required|in:pending,passed,failed,incomplete,excellent,very_good,good',
            'feedback' => 'nullable|string',
            'score' => 'nullable|integer|min:0|max:100',
            'certificate_number' => 'nullable|string|max:100',
            'certificate_date' => 'nullable|date',
            'certificate_file' => 'nullable|string',
        ]);

        $record = $this->trainingService->updateTrainingResult($uuid, $validated);
        return response()->json(['data' => $record]);
    }

    public function getEmployeeTrainingHistory(int $employeeId): JsonResponse
    {
        $history = $this->trainingService->getEmployeeTrainingHistory($employeeId);
        return response()->json(['data' => $history]);
    }

    public function getTrainingStats(): JsonResponse
    {
        $stats = $this->trainingService->getTrainingStats();
        return response()->json(['data' => $stats]);
    }

    // ===================== AWARD TYPES =====================

    public function getAwardTypes(): JsonResponse
    {
        $types = $this->awardService->getAwardTypes();
        return response()->json(['data' => $types]);
    }

    public function createAwardType(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'code' => 'required|string|max:20|unique:award_types,code',
            'description' => 'nullable|string',
            'default_reward' => 'nullable|numeric|min:0',
            'is_monetary' => 'nullable|boolean',
        ]);

        $type = $this->awardService->createAwardType($validated);
        return response()->json(['data' => $type], 201);
    }

    // ===================== AWARDS =====================

    public function getAwards(Request $request): JsonResponse
    {
        $filters = $request->only(['employee_id', 'award_type_id', 'date_from', 'date_to']);
        $perPage = (int) $request->get('per_page', 20);

        $awards = $this->awardService->getAwards($perPage, $filters);

        return response()->json([
            'data' => $awards->items(),
            'meta' => [
                'current_page' => $awards->currentPage(),
                'last_page' => $awards->lastPage(),
                'per_page' => $awards->perPage(),
                'total' => $awards->total(),
            ],
        ]);
    }

    public function createAward(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'award_type_id' => 'required|exists:award_types,id',
            'title' => 'nullable|string|max:255',
            'award_date' => 'required|date',
            'reason' => 'nullable|string',
            'reward_amount' => 'nullable|numeric|min:0',
            'reward_type' => 'nullable|in:cash,certificate,trophy,plaque,gift',
            'certificate_number' => 'nullable|string|max:100',
            'certificate_date' => 'nullable|date',
            'certificate_file' => 'nullable|string',
            'presented_by' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $award = $this->awardService->createAward($validated);
        return response()->json(['data' => $award], 201);
    }

    public function getEmployeeAwards(int $employeeId): JsonResponse
    {
        $awards = $this->awardService->getEmployeeAwards($employeeId);
        return response()->json(['data' => $awards]);
    }

    public function getAwardStats(): JsonResponse
    {
        $stats = $this->awardService->getAwardStats();
        return response()->json(['data' => $stats]);
    }
}
