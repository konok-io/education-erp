<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\Observability;

use App\DTO\Observability\AlertDTO;
use App\Http\Controllers\Controller;
use App\Services\Observability\AlertService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function __construct(
        protected AlertService $alertService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'service_id', 'severity', 'status', 'environment',
            'is_active', 'start_date', 'end_date'
        ]);
        $perPage = (int) $request->input('per_page', 15);

        $alerts = $this->alertService->index($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => AlertDTO::fromCollection($alerts->items()),
            'meta' => [
                'current_page' => $alerts->currentPage(),
                'last_page' => $alerts->lastPage(),
                'per_page' => $alerts->perPage(),
                'total' => $alerts->total(),
            ],
        ]);
    }

    public function active(Request $request): JsonResponse
    {
        $environment = $request->input('environment');

        $filters = [];
        if ($environment) {
            $filters['environment'] = $environment;
        }

        $alerts = $this->alertService->getActiveAlerts();

        return response()->json([
            'success' => true,
            'data' => AlertDTO::fromCollection($alerts->toArray()),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $alert = $this->alertService->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => AlertDTO::fromModel($alert),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'service_id' => 'nullable|uuid',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'severity' => 'required|string',
            'metric_name' => 'nullable|string',
            'condition' => 'nullable|string',
            'threshold' => 'nullable|numeric',
            'environment' => 'nullable|string',
            'metadata' => 'nullable|array',
            'labels' => 'nullable|array',
        ]);

        $alert = $this->alertService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Alert created successfully',
            'data' => AlertDTO::fromModel($alert),
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'severity' => 'nullable|string',
            'metric_name' => 'nullable|string',
            'condition' => 'nullable|string',
            'threshold' => 'nullable|numeric',
            'environment' => 'nullable|string',
            'metadata' => 'nullable|array',
            'labels' => 'nullable|array',
        ]);

        $alert = $this->alertService->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Alert updated successfully',
            'data' => AlertDTO::fromModel($alert),
        ]);
    }

    public function trigger(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'current_value' => 'required|numeric',
            'user_id' => 'nullable|uuid',
        ]);

        $alert = $this->alertService->trigger(
            $id,
            $data['current_value'],
            $data['user_id'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Alert triggered successfully',
            'data' => AlertDTO::fromModel($alert),
        ]);
    }

    public function acknowledge(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'user_id' => 'required|uuid',
        ]);

        $alert = $this->alertService->acknowledge($id, $data['user_id']);

        return response()->json([
            'success' => true,
            'message' => 'Alert acknowledged successfully',
            'data' => AlertDTO::fromModel($alert),
        ]);
    }

    public function resolve(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'user_id' => 'nullable|uuid',
        ]);

        $alert = $this->alertService->resolve($id, $data['user_id'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Alert resolved successfully',
            'data' => AlertDTO::fromModel($alert),
        ]);
    }

    public function silence(string $id): JsonResponse
    {
        $alert = $this->alertService->silence($id);

        return response()->json([
            'success' => true,
            'message' => 'Alert silenced successfully',
            'data' => AlertDTO::fromModel($alert),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->alertService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Alert deleted successfully',
        ]);
    }

    public function summary(): JsonResponse
    {
        $summary = $this->alertService->getSummary();

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }
}
