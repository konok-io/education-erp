<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\Observability;

use App\DTO\Observability\HealthCheckDTO;
use App\Http\Controllers\Controller;
use App\Services\Observability\HealthCheckService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HealthCheckController extends Controller
{
    public function __construct(
        protected HealthCheckService $healthCheckService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'service_id', 'type', 'status', 'environment', 'is_active'
        ]);
        $perPage = (int) $request->input('per_page', 15);

        $healthChecks = $this->healthCheckService->index($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => HealthCheckDTO::fromCollection($healthChecks->items()),
            'meta' => [
                'current_page' => $healthChecks->currentPage(),
                'last_page' => $healthChecks->lastPage(),
                'per_page' => $healthChecks->perPage(),
                'total' => $healthChecks->total(),
            ],
        ]);
    }

    public function active(): JsonResponse
    {
        $healthChecks = $this->healthCheckService->getActiveChecks();

        return response()->json([
            'success' => true,
            'data' => HealthCheckDTO::fromCollection($healthChecks->toArray()),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $healthCheck = $this->healthCheckService->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => HealthCheckDTO::fromModel($healthCheck),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'service_id' => 'nullable|uuid',
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'endpoint' => 'nullable|url',
            'method' => 'nullable|string',
            'status' => 'nullable|string',
            'check_interval_seconds' => 'nullable|integer',
            'timeout_seconds' => 'nullable|integer',
            'retry_count' => 'nullable|integer',
            'headers' => 'nullable|array',
            'expected_response' => 'nullable|array',
            'is_active' => 'nullable|boolean',
            'environment' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $healthCheck = $this->healthCheckService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Health check created successfully',
            'data' => HealthCheckDTO::fromModel($healthCheck),
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|string',
            'endpoint' => 'nullable|url',
            'method' => 'nullable|string',
            'check_interval_seconds' => 'nullable|integer',
            'timeout_seconds' => 'nullable|integer',
            'retry_count' => 'nullable|integer',
            'headers' => 'nullable|array',
            'expected_response' => 'nullable|array',
            'is_active' => 'nullable|boolean',
            'environment' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $healthCheck = $this->healthCheckService->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Health check updated successfully',
            'data' => HealthCheckDTO::fromModel($healthCheck),
        ]);
    }

    public function toggle(string $id): JsonResponse
    {
        $healthCheck = $this->healthCheckService->toggleActive($id);

        return response()->json([
            'success' => true,
            'message' => 'Health check toggled successfully',
            'data' => HealthCheckDTO::fromModel($healthCheck),
        ]);
    }

    public function execute(string $id): JsonResponse
    {
        $result = $this->healthCheckService->execute($id);

        return response()->json([
            'success' => true,
            'message' => 'Health check executed successfully',
            'data' => [
                'result_id' => $result->id,
                'status' => $result->status,
                'response_time_ms' => (float) $result->response_time_ms,
                'http_status_code' => $result->http_status_code,
                'error_message' => $result->error_message,
                'checked_at' => $result->checked_at->toIso8601String(),
            ],
        ]);
    }

    public function executeAll(): JsonResponse
    {
        $results = $this->healthCheckService->executeAll();

        return response()->json([
            'success' => true,
            'message' => 'All health checks executed successfully',
            'data' => $results,
        ]);
    }

    public function results(string $id): JsonResponse
    {
        $results = $this->healthCheckService->getResults($id);

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->healthCheckService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Health check deleted successfully',
        ]);
    }

    public function summary(): JsonResponse
    {
        $summary = $this->healthCheckService->getSummary();

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }
}
