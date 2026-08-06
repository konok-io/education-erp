<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\DevSecOps;

use App\Services\DevSecOps\ActivityLogService;
use App\Services\DevSecOps\ArtifactService;
use App\Services\DevSecOps\DeploymentService;
use App\Services\DevSecOps\EnvironmentService;
use App\Services\DevSecOps\PipelineService;
use App\Services\DevSecOps\ReleaseService;
use App\Services\DevSecOps\SecurityScanService;
use Illuminate\Http\JsonResponse;

class DevSecOpsController extends BaseController
{
    public function __construct(
        protected EnvironmentService $environmentService,
        protected PipelineService $pipelineService,
        protected DeploymentService $deploymentService,
        protected ArtifactService $artifactService,
        protected ReleaseService $releaseService,
        protected SecurityScanService $securityScanService,
        protected ActivityLogService $activityLogService
    ) {}

    public function dashboard(): JsonResponse
    {
        $stats = [
            'environments' => [
                'total' => $this->environmentService->getAll([], 1)->total(),
                'active' => $this->environmentService->getActive()->count(),
            ],
            'pipelines' => [
                'total' => $this->pipelineService->getAll([], 1)->total(),
                'active' => $this->pipelineService->getActive()->count(),
            ],
            'deployments' => [
                'active' => $this->deploymentService->getActive()->count(),
                'stats' => $this->activityLogService->getDeploymentStats(30),
            ],
            'artifacts' => [
                'total' => $this->artifactService->getAll([], 1)->total(),
                'vulnerable' => $this->artifactService->getVulnerable()->count(),
            ],
            'releases' => [
                'total' => $this->releaseService->getAll([], 1)->total(),
                'published' => $this->releaseService->getPublished(1)->total(),
                'lts' => $this->releaseService->getLts()->count(),
            ],
            'security' => $this->securityScanService->getStats(),
            'activity' => $this->activityLogService->getStats(7),
        ];

        return $this->success($stats, 'DevSecOps dashboard data retrieved successfully');
    }

    public function summary(): JsonResponse
    {
        $summary = [
            'recent_deployments' => $this->deploymentService->getAll([], 5)->items(),
            'recent_releases' => $this->releaseService->getPublished(5)->items(),
            'recent_security_scans' => $this->securityScanService->getRecentWithVulnerabilities(7, 10),
            'pipeline_stats' => $this->activityLogService->getPipelineStats(7),
        ];

        return $this->success($summary, 'DevSecOps summary retrieved successfully');
    }

    public function health(): JsonResponse
    {
        $health = [
            'status' => 'healthy',
            'services' => [
                'environments' => $this->environmentService->getActive()->count() > 0 ? 'operational' : 'no_data',
                'pipelines' => $this->pipelineService->getActive()->count() > 0 ? 'operational' : 'no_data',
                'deployments' => 'operational',
                'security_scans' => 'operational',
            ],
            'timestamp' => now()->toIso8601String(),
        ];

        return $this->success($health, 'Health check successful');
    }
}
