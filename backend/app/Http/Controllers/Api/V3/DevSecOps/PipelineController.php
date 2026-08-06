<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\DevSecOps;

use App\DTO\DevSecOps\PipelineDTO;
use App\Http\Requests\DevSecOps\PipelineRequest;
use App\Services\DevSecOps\PipelineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PipelineController extends BaseController
{
    public function __construct(
        protected PipelineService $pipelineService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'provider', 'status', 'is_active']);
        $perPage = (int) $request->get('per_page', 15);
        
        $pipelines = $this->pipelineService->getAll($filters, $perPage);
        
        return $this->paginated($pipelines, 'Pipelines retrieved successfully');
    }

    public function listActive(): JsonResponse
    {
        $pipelines = $this->pipelineService->getActive();
        
        return $this->success($pipelines, 'Active pipelines retrieved successfully');
    }

    public function show(string $id): JsonResponse
    {
        $pipeline = $this->pipelineService->getById($id);
        
        if (!$pipeline) {
            return $this->error('Pipeline not found', 404);
        }
        
        return $this->success($pipeline, 'Pipeline retrieved successfully');
    }

    public function store(PipelineRequest $request): JsonResponse
    {
        $dto = PipelineDTO::fromArray($request->validated());
        $pipeline = $this->pipelineService->create($dto);
        
        return $this->created($pipeline, 'Pipeline created successfully');
    }

    public function update(PipelineRequest $request, string $id): JsonResponse
    {
        $dto = PipelineDTO::fromArray($request->validated());
        $pipeline = $this->pipelineService->update($id, $dto);
        
        if (!$pipeline) {
            return $this->error('Pipeline not found', 404);
        }
        
        return $this->success($pipeline, 'Pipeline updated successfully');
    }

    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->pipelineService->delete($id);
        
        if (!$deleted) {
            return $this->error('Pipeline not found', 404);
        }
        
        return $this->success(null, 'Pipeline deleted successfully');
    }

    public function toggleActive(string $id): JsonResponse
    {
        $pipeline = $this->pipelineService->toggleActive($id);
        
        if (!$pipeline) {
            return $this->error('Pipeline not found', 404);
        }
        
        return $this->success($pipeline, 'Pipeline status updated successfully');
    }

    public function trigger(Request $request, string $id): JsonResponse
    {
        $params = $request->only(['trigger', 'branch', 'commit_sha', 'commit_message', 'author']);
        $run = $this->pipelineService->trigger($id, $params);
        
        if (!$run) {
            return $this->error('Failed to trigger pipeline', 400);
        }
        
        return $this->success($run, 'Pipeline triggered successfully', 201);
    }

    public function runs(Request $request, string $id): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 15);
        $runs = $this->pipelineService->getRuns($id, $perPage);
        
        return $this->paginated($runs, 'Pipeline runs retrieved successfully');
    }

    public function showRun(string $id): JsonResponse
    {
        $run = $this->pipelineService->getRunById($id);
        
        if (!$run) {
            return $this->error('Pipeline run not found', 404);
        }
        
        return $this->success($run, 'Pipeline run retrieved successfully');
    }

    public function updateRunStatus(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,running,success,failed,cancelled,blocked,timeout,skipped',
            'metadata' => 'nullable|array',
        ]);

        $run = $this->pipelineService->updateRunStatus($id, $validated['status'], $validated['metadata'] ?? []);
        
        if (!$run) {
            return $this->error('Pipeline run not found', 404);
        }
        
        return $this->success($run, 'Pipeline run status updated successfully');
    }
}
