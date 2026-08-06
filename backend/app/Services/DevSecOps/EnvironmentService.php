<?php

declare(strict_types=1);

namespace App\Services\DevSecOps;

use App\DTO\DevSecOps\EnvironmentDTO;
use App\Models\DevSecOps\DevSecOpsEnvironment;
use Illuminate\Support\Collection;

class EnvironmentService extends DevSecOpsBaseService
{
    public function getAll(array $filters = [], int $perPage = 15)
    {
        $query = DevSecOpsEnvironment::query();

        if (isset($filters['type'])) {
            $query->ofType($filters['type']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $query->orderBy('sort_order')->orderBy('name');

        return $this->paginate($query, $perPage);
    }

    public function getActive(): Collection
    {
        return DevSecOpsEnvironment::active()
            ->orderBy('sort_order')
            ->get();
    }

    public function getById(string $id): ?DevSecOpsEnvironment
    {
        return DevSecOpsEnvironment::find($id);
    }

    public function getBySlug(string $slug): ?DevSecOpsEnvironment
    {
        return DevSecOpsEnvironment::where('slug', $slug)->first();
    }

    public function create(EnvironmentDTO $dto): DevSecOpsEnvironment
    {
        $data = $dto->toArray();
        $data['slug'] = $this->generateUniqueSlug($data['slug'], 'devsecops_environments');
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $environment = DevSecOpsEnvironment::create($data);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_GITOPS,
            action: 'created',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $environment->id,
            resourceType: DevSecOpsEnvironment::class,
            resourceName: $environment->name,
            message: "Environment '{$environment->name}' created",
        );

        return $environment;
    }

    public function update(string $id, EnvironmentDTO $dto): ?DevSecOpsEnvironment
    {
        $environment = $this->getById($id);

        if (!$environment) {
            return null;
        }

        $oldValues = $environment->toArray();
        $environment->update($dto->toArray());
        $environment->update(['updated_by' => auth()->id()]);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_GITOPS,
            action: 'updated',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $environment->id,
            resourceType: DevSecOpsEnvironment::class,
            resourceName: $environment->name,
            changes: [
                'old' => $oldValues,
                'new' => $environment->toArray(),
            ],
            message: "Environment '{$environment->name}' updated",
        );

        return $environment->fresh();
    }

    public function delete(string $id): bool
    {
        $environment = $this->getById($id);

        if (!$environment) {
            return false;
        }

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_GITOPS,
            action: 'deleted',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $environment->id,
            resourceType: DevSecOpsEnvironment::class,
            resourceName: $environment->name,
            message: "Environment '{$environment->name}' deleted",
        );

        return $environment->delete();
    }

    public function toggleActive(string $id): ?DevSecOpsEnvironment
    {
        $environment = $this->getById($id);

        if (!$environment) {
            return null;
        }

        $environment->update([
            'is_active' => !$environment->is_active,
            'updated_by' => auth()->id(),
        ]);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_GITOPS,
            action: $environment->is_active ? 'activated' : 'deactivated',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $environment->id,
            resourceType: DevSecOpsEnvironment::class,
            resourceName: $environment->name,
            message: "Environment '{$environment->name}' " . ($environment->is_active ? 'activated' : 'deactivated'),
        );

        return $environment->fresh();
    }
}
