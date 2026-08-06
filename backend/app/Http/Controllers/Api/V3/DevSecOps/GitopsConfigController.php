<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\DevSecOps;

use App\DTO\DevSecOps\GitopsConfigDTO;
use App\Http\Requests\DevSecOps\GitopsConfigRequest;
use App\Services\DevSecOps\GitopsConfigService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GitopsConfigController extends BaseController
{
    public function __construct(
        protected GitopsConfigService $gitopsConfigService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['provider', 'environment_id', 'sync_policy', 'is_active']);
        $perPage = (int) $request->get('per_page', 15);
        
        $configs = $this->gitopsConfigService->getAll($filters, $perPage);
        
        return $this->paginated($configs, 'GitOps configs retrieved successfully');
    }

    public function listActive(): JsonResponse
    {
        $configs = $this->gitopsConfigService->getActive();
        
        return $this->success($configs, 'Active GitOps configs retrieved successfully');
    }

    public function show(string $id): JsonResponse
    {
        $config = $this->gitopsConfigService->getById($id);
        
        if (!$config) {
            return $this->error('GitOps config not found', 404);
        }
        
        return $this->success($config, 'GitOps config retrieved successfully');
    }

    public function store(GitopsConfigRequest $request): JsonResponse
    {
        $dto = GitopsConfigDTO::fromArray($request->validated());
        $config = $this->gitopsConfigService->create($dto);
        
        return $this->created($config, 'GitOps config created successfully');
    }

    public function update(GitopsConfigRequest $request, string $id): JsonResponse
    {
        $dto = GitopsConfigDTO::fromArray($request->validated());
        $config = $this->gitopsConfigService->update($id, $dto);
        
        if (!$config) {
            return $this->error('GitOps config not found', 404);
        }
        
        return $this->success($config, 'GitOps config updated successfully');
    }

    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->gitopsConfigService->delete($id);
        
        if (!$deleted) {
            return $this->error('GitOps config not found', 404);
        }
        
        return $this->success(null, 'GitOps config deleted successfully');
    }

    public function toggleActive(string $id): JsonResponse
    {
        $config = $this->gitopsConfigService->toggleActive($id);
        
        if (!$config) {
            return $this->error('GitOps config not found', 404);
        }
        
        return $this->success($config, 'GitOps config status updated successfully');
    }

    public function sync(string $id): JsonResponse
    {
        $config = $this->gitopsConfigService->sync($id);
        
        if (!$config) {
            return $this->error('GitOps config not found', 404);
        }
        
        return $this->success($config, 'GitOps sync initiated successfully');
    }

    public function updateSyncStatus(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:synced,drifted,syncing,failed',
        ]);

        $config = $this->gitopsConfigService->updateSyncStatus($id, $validated['status']);
        
        if (!$config) {
            return $this->error('GitOps config not found', 404);
        }
        
        return $this->success($config, 'Sync status updated successfully');
    }

    public function byEnvironment(string $environmentId): JsonResponse
    {
        $configs = $this->gitopsConfigService->getByEnvironment($environmentId);
        
        return $this->success($configs, 'Environment GitOps configs retrieved successfully');
    }

    public function byProvider(string $provider): JsonResponse
    {
        $configs = $this->gitopsConfigService->getByProvider($provider);
        
        return $this->success($configs, 'Provider GitOps configs retrieved successfully');
    }
}
