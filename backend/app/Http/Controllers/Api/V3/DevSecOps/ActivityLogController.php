<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\DevSecOps;

use App\Services\DevSecOps\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends BaseController
{
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'action', 'status', 'resource_type', 'resource_id', 'actor_id', 'days']);
        $perPage = (int) $request->get('per_page', 15);
        
        $logs = $this->activityLogService->getAll($filters, $perPage);
        
        return $this->paginated($logs, 'Activity logs retrieved successfully');
    }

    public function byResource(Request $request, string $resourceType, string $resourceId): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 15);
        $logs = $this->activityLogService->getByResource($resourceType, $resourceId, $perPage);
        
        return $this->paginated($logs, 'Resource activity logs retrieved successfully');
    }

    public function recent(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 7);
        $perPage = (int) $request->get('per_page', 15);
        
        $logs = $this->activityLogService->getRecent($days, $perPage);
        
        return $this->paginated($logs, 'Recent activity logs retrieved successfully');
    }

    public function stats(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 30);
        $stats = $this->activityLogService->getStats($days);
        
        return $this->success($stats, 'Activity stats retrieved successfully');
    }

    public function pipelineStats(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 30);
        $stats = $this->activityLogService->getPipelineStats($days);
        
        return $this->success($stats, 'Pipeline stats retrieved successfully');
    }

    public function deploymentStats(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 30);
        $stats = $this->activityLogService->getDeploymentStats($days);
        
        return $this->success($stats, 'Deployment stats retrieved successfully');
    }

    public function securityStats(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 30);
        $stats = $this->activityLogService->getSecurityStats($days);
        
        return $this->success($stats, 'Security stats retrieved successfully');
    }
}
