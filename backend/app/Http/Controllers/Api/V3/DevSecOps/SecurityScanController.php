<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\DevSecOps;

use App\DTO\DevSecOps\SecurityScanDTO;
use App\Http\Requests\DevSecOps\SecurityScanRequest;
use App\Services\DevSecOps\SecurityScanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SecurityScanController extends BaseController
{
    public function __construct(
        protected SecurityScanService $securityScanService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'tool', 'status', 'severity', 'pipeline_run_id', 'artifact_id']);
        $perPage = (int) $request->get('per_page', 15);
        
        $scans = $this->securityScanService->getAll($filters, $perPage);
        
        return $this->paginated($scans, 'Security scans retrieved successfully');
    }

    public function show(string $id): JsonResponse
    {
        $scan = $this->securityScanService->getById($id);
        
        if (!$scan) {
            return $this->error('Security scan not found', 404);
        }
        
        return $this->success($scan, 'Security scan retrieved successfully');
    }

    public function store(SecurityScanRequest $request): JsonResponse
    {
        $dto = SecurityScanDTO::fromArray($request->validated());
        $scan = $this->securityScanService->create($dto);
        
        return $this->created($scan, 'Security scan created successfully');
    }

    public function start(string $id): JsonResponse
    {
        $scan = $this->securityScanService->startScan($id);
        
        if (!$scan) {
            return $this->error('Security scan not found', 404);
        }
        
        return $this->success($scan, 'Security scan started successfully');
    }

    public function updateResults(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'results' => 'required|array',
            'vulnerabilities' => 'nullable|array',
            'secrets_found' => 'nullable|array',
        ]);

        $scan = $this->securityScanService->updateResults(
            $id,
            $validated['results'],
            $validated['vulnerabilities'] ?? [],
            $validated['secrets_found'] ?? []
        );
        
        if (!$scan) {
            return $this->error('Security scan not found', 404);
        }
        
        return $this->success($scan, 'Scan results updated successfully');
    }

    public function fail(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'error_message' => 'required|string',
        ]);

        $scan = $this->securityScanService->failScan($id, $validated['error_message']);
        
        if (!$scan) {
            return $this->error('Security scan not found', 404);
        }
        
        return $this->success($scan, 'Security scan marked as failed');
    }

    public function byPipelineRun(string $pipelineRunId): JsonResponse
    {
        $scans = $this->securityScanService->getByPipelineRun($pipelineRunId);
        
        return $this->success($scans, 'Pipeline run security scans retrieved successfully');
    }

    public function byArtifact(string $artifactId): JsonResponse
    {
        $scans = $this->securityScanService->getByArtifact($artifactId);
        
        return $this->success($scans, 'Artifact security scans retrieved successfully');
    }

    public function recentVulnerabilities(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 7);
        $limit = (int) $request->get('limit', 20);
        
        $scans = $this->securityScanService->getRecentWithVulnerabilities($days, $limit);
        
        return $this->success($scans, 'Recent vulnerabilities retrieved successfully');
    }

    public function stats(): JsonResponse
    {
        $stats = $this->securityScanService->getStats();
        
        return $this->success($stats, 'Security scan stats retrieved successfully');
    }
}
