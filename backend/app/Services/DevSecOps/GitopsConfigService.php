<?php

declare(strict_types=1);

namespace App\Services\DevSecOps;

use App\DTO\DevSecOps\GitopsConfigDTO;
use App\Models\DevSecOps\DevSecOpsGitopsConfig;
use Illuminate\Support\Collection;

class GitopsConfigService extends DevSecOpsBaseService
{
    public function getAll(array $filters = [], int $perPage = 15)
    {
        $query = DevSecOpsGitopsConfig::with('environment');

        if (isset($filters['provider'])) {
            $query->ofProvider($filters['provider']);
        }

        if (isset($filters['environment_id'])) {
            $query->where('environment_id', $filters['environment_id']);
        }

        if (isset($filters['sync_policy'])) {
            $query->where('sync_policy', $filters['sync_policy']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $query->orderBy('name');

        return $this->paginate($query, $perPage);
    }

    public function getActive(): Collection
    {
        return DevSecOpsGitopsConfig::active()
            ->with('environment')
            ->orderBy('name')
            ->get();
    }

    public function getById(string $id): ?DevSecOpsGitopsConfig
    {
        return DevSecOpsGitopsConfig::with('environment')->find($id);
    }

    public function getBySlug(string $slug): ?DevSecOpsGitopsConfig
    {
        return DevSecOpsGitopsConfig::where('slug', $slug)->first();
    }

    public function create(GitopsConfigDTO $dto): DevSecOpsGitopsConfig
    {
        $data = $dto->toArray();
        $data['slug'] = $this->generateUniqueSlug($data['slug'], 'devsecops_gitops_configs');
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $config = DevSecOpsGitopsConfig::create($data);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_GITOPS,
            action: 'created',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $config->id,
            resourceType: DevSecOpsGitopsConfig::class,
            resourceName: $config->name,
            metadata: ['provider' => $config->provider, 'environment' => $config->environment->name],
            message: "GitOps config '{$config->name}' created",
        );

        return $config;
    }

    public function update(string $id, GitopsConfigDTO $dto): ?DevSecOpsGitopsConfig
    {
        $config = $this->getById($id);

        if (!$config) {
            return null;
        }

        $oldValues = $config->toArray();
        $config->update($dto->toArray());
        $config->update(['updated_by' => auth()->id()]);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_GITOPS,
            action: 'updated',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $config->id,
            resourceType: DevSecOpsGitopsConfig::class,
            resourceName: $config->name,
            changes: [
                'old' => $oldValues,
                'new' => $config->toArray(),
            ],
            message: "GitOps config '{$config->name}' updated",
        );

        return $config->fresh();
    }

    public function delete(string $id): bool
    {
        $config = $this->getById($id);

        if (!$config) {
            return false;
        }

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_GITOPS,
            action: 'deleted',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $config->id,
            resourceType: DevSecOpsGitopsConfig::class,
            resourceName: $config->name,
            message: "GitOps config '{$config->name}' deleted",
        );

        return $config->delete();
    }

    public function toggleActive(string $id): ?DevSecOpsGitopsConfig
    {
        $config = $this->getById($id);

        if (!$config) {
            return null;
        }

        $config->update([
            'is_active' => !$config->is_active,
            'updated_by' => auth()->id(),
        ]);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_GITOPS,
            action: $config->is_active ? 'activated' : 'deactivated',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $config->id,
            resourceType: DevSecOpsGitopsConfig::class,
            resourceName: $config->name,
            message: "GitOps config '{$config->name}' " . ($config->is_active ? 'activated' : 'deactivated'),
        );

        return $config->fresh();
    }

    public function sync(string $id): ?DevSecOpsGitopsConfig
    {
        $config = $this->getById($id);

        if (!$config) {
            return null;
        }

        $config->update([
            'last_sync_status' => 'syncing',
            'updated_by' => auth()->id(),
        ]);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_GITOPS,
            action: 'sync_started',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $config->id,
            resourceType: DevSecOpsGitopsConfig::class,
            resourceName: $config->name,
            message: "GitOps sync started for '{$config->name}'",
        );

        return $config->fresh();
    }

    public function updateSyncStatus(string $id, string $status): ?DevSecOpsGitopsConfig
    {
        $config = $this->getById($id);

        if (!$config) {
            return null;
        }

        $updateData = ['last_sync_status' => $status];

        if ($status === 'synced' || $status === 'drifted') {
            $updateData['last_synced_at'] = now();
        }

        $config->update($updateData);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_GITOPS,
            action: 'sync_' . $status,
            status: $status === 'synced' ? DevSecOpsActivityLog::STATUS_SUCCESS : DevSecOpsActivityLog::STATUS_WARNING,
            resourceId: $config->id,
            resourceType: DevSecOpsGitopsConfig::class,
            resourceName: $config->name,
            message: "GitOps sync {$status} for '{$config->name}'",
        );

        return $config->fresh();
    }

    public function getByEnvironment(string $environmentId): Collection
    {
        return DevSecOpsGitopsConfig::where('environment_id', $environmentId)
            ->active()
            ->orderBy('name')
            ->get();
    }

    public function getByProvider(string $provider): Collection
    {
        return DevSecOpsGitopsConfig::ofProvider($provider)
            ->active()
            ->with('environment')
            ->orderBy('name')
            ->get();
    }
}
