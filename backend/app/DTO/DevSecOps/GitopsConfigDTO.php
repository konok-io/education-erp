<?php

declare(strict_types=1);

namespace App\DTO\DevSecOps;

class GitopsConfigDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly string $provider,
        public readonly string $repository,
        public readonly string $path,
        public readonly string $environmentId,
        public readonly string $syncPolicy = 'manual',
        public readonly ?string $description = null,
        public readonly string $targetBranch = 'main',
        public readonly bool $autoSync = true,
        public readonly bool $selfHeal = true,
        public readonly bool $prune = true,
        public readonly int $syncInterval = 300,
        public readonly ?array $kustomize = null,
        public readonly ?array $helm = null,
        public readonly ?array $values = null,
        public readonly ?string $healthCheckPath = null,
        public readonly ?array $driftDetection = null,
        public readonly ?array $notifications = null,
        public readonly bool $isActive = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'],
            provider: $data['provider'],
            repository: $data['repository'],
            path: $data['path'],
            environmentId: $data['environment_id'],
            syncPolicy: $data['sync_policy'] ?? 'manual',
            description: $data['description'] ?? null,
            targetBranch: $data['target_branch'] ?? 'main',
            autoSync: $data['auto_sync'] ?? true,
            selfHeal: $data['self_heal'] ?? true,
            prune: $data['prune'] ?? true,
            syncInterval: $data['sync_interval'] ?? 300,
            kustomize: $data['kustomize'] ?? null,
            helm: $data['helm'] ?? null,
            values: $data['values'] ?? null,
            healthCheckPath: $data['health_check_path'] ?? null,
            driftDetection: $data['drift_detection'] ?? null,
            notifications: $data['notifications'] ?? null,
            isActive: $data['is_active'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'provider' => $this->provider,
            'repository' => $this->repository,
            'path' => $this->path,
            'environment_id' => $this->environmentId,
            'sync_policy' => $this->syncPolicy,
            'description' => $this->description,
            'target_branch' => $this->targetBranch,
            'auto_sync' => $this->autoSync,
            'self_heal' => $this->selfHeal,
            'prune' => $this->prune,
            'sync_interval' => $this->syncInterval,
            'kustomize' => $this->kustomize,
            'helm' => $this->helm,
            'values' => $this->values,
            'health_check_path' => $this->healthCheckPath,
            'drift_detection' => $this->driftDetection,
            'notifications' => $this->notifications,
            'is_active' => $this->isActive,
        ];
    }
}
