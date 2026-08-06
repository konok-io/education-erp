<?php

declare(strict_types=1);

namespace App\DTO\DevSecOps;

class DeploymentDTO
{
    public function __construct(
        public readonly string $environmentId,
        public readonly string $strategy,
        public readonly ?string $pipelineRunId = null,
        public readonly ?string $releaseId = null,
        public readonly ?string $version = null,
        public readonly ?string $namespace = null,
        public readonly ?array $config = null,
        public readonly ?array $replicas = null,
        public readonly ?array $resources = null,
        public readonly ?array $healthChecks = null,
        public readonly ?array $rollbackConfig = null,
        public readonly ?string $previousVersion = null,
        public readonly ?string $commitSha = null,
        public readonly bool $autoRollback = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            environmentId: $data['environment_id'],
            strategy: $data['strategy'],
            pipelineRunId: $data['pipeline_run_id'] ?? null,
            releaseId: $data['release_id'] ?? null,
            version: $data['version'] ?? null,
            namespace: $data['namespace'] ?? null,
            config: $data['config'] ?? null,
            replicas: $data['replicas'] ?? null,
            resources: $data['resources'] ?? null,
            healthChecks: $data['health_checks'] ?? null,
            rollbackConfig: $data['rollback_config'] ?? null,
            previousVersion: $data['previous_version'] ?? null,
            commitSha: $data['commit_sha'] ?? null,
            autoRollback: $data['auto_rollback'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'environment_id' => $this->environmentId,
            'strategy' => $this->strategy,
            'pipeline_run_id' => $this->pipelineRunId,
            'release_id' => $this->releaseId,
            'version' => $this->version,
            'namespace' => $this->namespace,
            'config' => $this->config,
            'replicas' => $this->replicas,
            'resources' => $this->resources,
            'health_checks' => $this->healthChecks,
            'rollback_config' => $this->rollbackConfig,
            'previous_version' => $this->previousVersion,
            'commit_sha' => $this->commitSha,
            'auto_rollback' => $this->autoRollback,
        ];
    }
}
