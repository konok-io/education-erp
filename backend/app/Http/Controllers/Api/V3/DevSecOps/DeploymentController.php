<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\DevSecOps;

use App\DTO\DevSecOps\DeploymentDTO;
use App\Http\Requests\DevSecOps\DeploymentRequest;
use App\Services\DevSecOps\DeploymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeploymentController extends BaseController
{
    public function __construct(
        protected DeploymentService $deploymentService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['environment_id', 'status', 'strategy', 'version']);
        $perPage = (int) $request->get('per_page', 15);
        
        $deployments = $this->deploymentService->getAll($filters, $perPage);
        
        return $this->paginated($deployments, 'Deployments retrieved successfully');
    }

    public function listActive(): JsonResponse
    {
        $deployments = $this->deploymentService->getActive();
        
        return $this->success($deployments, 'Active deployments retrieved successfully');
    }

    public function show(string $id): JsonResponse
    {
        $deployment = $this->deploymentService->getById($id);
        
        if (!$deployment) {
            return $this->error('Deployment not found', 404);
        }
        
        return $this->success($deployment, 'Deployment retrieved successfully');
    }

    public function store(DeploymentRequest $request): JsonResponse
    {
        $dto = DeploymentDTO::fromArray($request->validated());
        $deployment = $this->deploymentService->create($dto);
        
        return $this->created($deployment, 'Deployment created successfully');
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,deploying,deployed,failed,rolled_back,scaling,monitoring',
            'error_message' => 'nullable|string',
        ]);

        $deployment = $this->deploymentService->updateStatus($id, $validated['status'], $validated['error_message'] ?? null);
        
        if (!$deployment) {
            return $this->error('Deployment not found', 404);
        }
        
        return $this->success($deployment, 'Deployment status updated successfully');
    }

    public function rollback(string $id): JsonResponse
    {
        $deployment = $this->deploymentService->rollback($id);
        
        if (!$deployment) {
            return $this->error('Failed to rollback deployment', 400);
        }
        
        return $this->success($deployment, 'Deployment rolled back successfully');
    }

    public function byEnvironment(string $environmentId): JsonResponse
    {
        $deployments = $this->deploymentService->getByEnvironment($environmentId);
        
        return $this->success($deployments, 'Environment deployments retrieved successfully');
    }

    public function latestByEnvironment(string $environmentId): JsonResponse
    {
        $deployment = $this->deploymentService->getLatestByEnvironment($environmentId);
        
        if (!$deployment) {
            return $this->error('No deployment found for environment', 404);
        }
        
        return $this->success($deployment, 'Latest deployment retrieved successfully');
    }

    public function history(Request $request, string $environmentId): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 15);
        $deployments = $this->deploymentService->getHistory($environmentId, $perPage);
        
        return $this->paginated($deployments, 'Deployment history retrieved successfully');
    }
}
