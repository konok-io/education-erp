<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\DevSecOps;

use App\DTO\DevSecOps\EnvironmentDTO;
use App\Http\Requests\DevSecOps\EnvironmentRequest;
use App\Services\DevSecOps\EnvironmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnvironmentController extends BaseController
{
    public function __construct(
        protected EnvironmentService $environmentService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'is_active']);
        $perPage = (int) $request->get('per_page', 15);
        
        $environments = $this->environmentService->getAll($filters, $perPage);
        
        return $this->paginated($environments, 'Environments retrieved successfully');
    }

    public function listActive(): JsonResponse
    {
        $environments = $this->environmentService->getActive();
        
        return $this->success($environments, 'Active environments retrieved successfully');
    }

    public function show(string $id): JsonResponse
    {
        $environment = $this->environmentService->getById($id);
        
        if (!$environment) {
            return $this->error('Environment not found', 404);
        }
        
        return $this->success($environment, 'Environment retrieved successfully');
    }

    public function store(EnvironmentRequest $request): JsonResponse
    {
        $dto = EnvironmentDTO::fromArray($request->validated());
        $environment = $this->environmentService->create($dto);
        
        return $this->created($environment, 'Environment created successfully');
    }

    public function update(EnvironmentRequest $request, string $id): JsonResponse
    {
        $dto = EnvironmentDTO::fromArray($request->validated());
        $environment = $this->environmentService->update($id, $dto);
        
        if (!$environment) {
            return $this->error('Environment not found', 404);
        }
        
        return $this->success($environment, 'Environment updated successfully');
    }

    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->environmentService->delete($id);
        
        if (!$deleted) {
            return $this->error('Environment not found', 404);
        }
        
        return $this->success(null, 'Environment deleted successfully');
    }

    public function toggleActive(string $id): JsonResponse
    {
        $environment = $this->environmentService->toggleActive($id);
        
        if (!$environment) {
            return $this->error('Environment not found', 404);
        }
        
        return $this->success($environment, 'Environment status updated successfully');
    }
}
