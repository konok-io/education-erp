<?php

declare(strict_types=1);

namespace App\DTO\DevSecOps;

class PipelineDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly string $type,
        public readonly string $provider,
        public readonly ?string $description = null,
        public readonly ?string $repository = null,
        public readonly string $branch = 'main',
        public readonly string $yamlPath = '.github/workflows/pipeline.yml',
        public readonly ?array $stages = null,
        public readonly ?array $config = null,
        public readonly ?string $status = 'inactive',
        public readonly int $timeout = 3600,
        public readonly bool $autoTrigger = true,
        public readonly bool $requireApproval = false,
        public readonly ?array $approvalRoles = null,
        public readonly int $minCoverage = 80,
        public readonly bool $isActive = true,
        public readonly ?string $environmentId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'],
            type: $data['type'],
            provider: $data['provider'],
            description: $data['description'] ?? null,
            repository: $data['repository'] ?? null,
            branch: $data['branch'] ?? 'main',
            yamlPath: $data['yaml_path'] ?? '.github/workflows/pipeline.yml',
            stages: $data['stages'] ?? null,
            config: $data['config'] ?? null,
            status: $data['status'] ?? 'inactive',
            timeout: $data['timeout'] ?? 3600,
            autoTrigger: $data['auto_trigger'] ?? true,
            requireApproval: $data['require_approval'] ?? false,
            approvalRoles: $data['approval_roles'] ?? null,
            minCoverage: $data['min_coverage'] ?? 80,
            isActive: $data['is_active'] ?? true,
            environmentId: $data['environment_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'provider' => $this->provider,
            'description' => $this->description,
            'repository' => $this->repository,
            'branch' => $this->branch,
            'yaml_path' => $this->yamlPath,
            'stages' => $this->stages,
            'config' => $this->config,
            'status' => $this->status,
            'timeout' => $this->timeout,
            'auto_trigger' => $this->autoTrigger,
            'require_approval' => $this->requireApproval,
            'approval_roles' => $this->approvalRoles,
            'min_coverage' => $this->minCoverage,
            'is_active' => $this->isActive,
            'environment_id' => $this->environmentId,
        ];
    }
}
