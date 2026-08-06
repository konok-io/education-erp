<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\Backup;

use App\Enums\Backup\FailoverType;
use App\Http\Controllers\Controller;
use App\Services\Backup\FailoverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FailoverController extends Controller
{
    public function __construct(
        protected FailoverService $failoverService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'status', 'type', 'source_site', 'destination_site'
        ]);
        $perPage = (int) $request->input('per_page', 15);

        $failovers = $this->failoverService->index($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $failovers->items(),
            'meta' => [
                'current_page' => $failovers->currentPage(),
                'last_page' => $failovers->lastPage(),
                'per_page' => $failovers->perPage(),
                'total' => $failovers->total(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $failover = $this->failoverService->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $failover,
        ]);
    }

    public function initiate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:automatic,manual,planned,emergency',
            'source_site' => 'required|string',
            'destination_site' => 'required|string',
            'trigger_reason' => 'nullable|string',
            'trigger_details' => 'nullable|string',
            'initiated_by' => 'required|uuid',
            'approved_by' => 'nullable|uuid',
        ]);

        $failover = $this->failoverService->initiate(
            $data['name'],
            FailoverType::from($data['type']),
            $data['source_site'],
            $data['destination_site'],
            $data['initiated_by'],
            $data['trigger_reason'] ?? null,
            $data['trigger_details'] ?? null,
            $data['approved_by'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Failover initiated successfully',
            'data' => $failover,
        ], 201);
    }

    public function start(string $id): JsonResponse
    {
        $failover = $this->failoverService->start($id);

        return response()->json([
            'success' => true,
            'message' => 'Failover started successfully',
            'data' => $failover,
        ]);
    }

    public function complete(string $id): JsonResponse
    {
        $failover = $this->failoverService->complete($id);

        return response()->json([
            'success' => true,
            'message' => 'Failover completed successfully',
            'data' => $failover,
        ]);
    }

    public function fail(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'error_message' => 'required|string',
        ]);

        $failover = $this->failoverService->fail($id, $data['error_message']);

        return response()->json([
            'success' => true,
            'message' => 'Failover marked as failed',
            'data' => $failover,
        ]);
    }

    public function rollback(string $id): JsonResponse
    {
        $failover = $this->failoverService->rollback($id);

        return response()->json([
            'success' => true,
            'message' => 'Failover rolled back successfully',
            'data' => $failover,
        ]);
    }

    public function cancel(string $id): JsonResponse
    {
        $failover = $this->failoverService->cancel($id);

        return response()->json([
            'success' => true,
            'message' => 'Failover cancelled successfully',
            'data' => $failover,
        ]);
    }

    public function updateAffected(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'affected_users' => 'required|integer',
            'downtime_seconds' => 'required|integer',
        ]);

        $failover = $this->failoverService->updateAffectedCount(
            $id,
            $data['affected_users'],
            $data['downtime_seconds']
        );

        return response()->json([
            'success' => true,
            'message' => 'Failover affected count updated',
            'data' => $failover,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->failoverService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Failover event deleted successfully',
        ]);
    }

    public function summary(): JsonResponse
    {
        $summary = $this->failoverService->getSummary();

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    // DR Sites
    public function drSites(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'type', 'region']);

        $sites = $this->failoverService->getDRSites($filters);

        return response()->json([
            'success' => true,
            'data' => $sites,
        ]);
    }

    public function drSiteShow(string $id): JsonResponse
    {
        $site = $this->failoverService->findDRSiteOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $site,
        ]);
    }

    public function createDRSite(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'type' => 'required|string|in:primary,secondary,dr,hot,warm,cold',
            'status' => 'nullable|string',
            'region' => 'required|string',
            'zone' => 'nullable|string',
            'infrastructure_config' => 'nullable|array',
            'endpoint' => 'nullable|url',
            'connection_config' => 'nullable|array',
            'role' => 'nullable|string',
            'is_primary' => 'nullable|boolean',
            'auto_failover_enabled' => 'nullable|boolean',
            'health_check_endpoint' => 'nullable|url',
            'health_check_interval_seconds' => 'nullable|integer',
            'recovery_time_target_seconds' => 'nullable|integer',
            'recovery_point_target_seconds' => 'nullable|integer',
            'metadata' => 'nullable|array',
        ]);

        $site = $this->failoverService->createDRSite($data);

        return response()->json([
            'success' => true,
            'message' => 'DR site created successfully',
            'data' => $site,
        ], 201);
    }

    public function updateDRSite(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|string',
            'status' => 'nullable|string',
            'region' => 'nullable|string',
            'zone' => 'nullable|string',
            'infrastructure_config' => 'nullable|array',
            'endpoint' => 'nullable|url',
            'connection_config' => 'nullable|array',
            'role' => 'nullable|string',
            'is_primary' => 'nullable|boolean',
            'auto_failover_enabled' => 'nullable|boolean',
            'health_check_endpoint' => 'nullable|url',
            'health_check_interval_seconds' => 'nullable|integer',
            'recovery_time_target_seconds' => 'nullable|integer',
            'recovery_point_target_seconds' => 'nullable|integer',
            'metadata' => 'nullable|array',
        ]);

        $site = $this->failoverService->updateDRSite($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'DR site updated successfully',
            'data' => $site,
        ]);
    }

    public function updateDRSiteHealth(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|string|in:healthy,unhealthy,unknown',
        ]);

        $site = $this->failoverService->updateDRSiteHealth($id, $data['status']);

        return response()->json([
            'success' => true,
            'message' => 'DR site health status updated',
            'data' => $site,
        ]);
    }

    public function deleteDRSite(string $id): JsonResponse
    {
        $this->failoverService->deleteDRSite($id);

        return response()->json([
            'success' => true,
            'message' => 'DR site deleted successfully',
        ]);
    }
}
