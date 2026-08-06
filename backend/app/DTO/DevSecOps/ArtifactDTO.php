<?php

declare(strict_types=1);

namespace App\DTO\DevSecOps;

class ArtifactDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly string $registry,
        public readonly ?string $version = null,
        public readonly ?string $pipelineRunId = null,
        public readonly ?string $path = null,
        public readonly ?string $repository = null,
        public readonly ?string $digest = null,
        public readonly ?string $size = null,
        public readonly ?array $metadata = null,
        public readonly ?array $labels = null,
        public readonly bool $signed = false,
        public readonly ?string $license = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            type: $data['type'],
            registry: $data['registry'],
            version: $data['version'] ?? null,
            pipelineRunId: $data['pipeline_run_id'] ?? null,
            path: $data['path'] ?? null,
            repository: $data['repository'] ?? null,
            digest: $data['digest'] ?? null,
            size: $data['size'] ?? null,
            metadata: $data['metadata'] ?? null,
            labels: $data['labels'] ?? null,
            signed: $data['signed'] ?? false,
            license: $data['license'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'registry' => $this->registry,
            'version' => $this->version,
            'pipeline_run_id' => $this->pipelineRunId,
            'path' => $this->path,
            'repository' => $this->repository,
            'digest' => $this->digest,
            'size' => $this->size,
            'metadata' => $this->metadata,
            'labels' => $this->labels,
            'signed' => $this->signed,
            'license' => $this->license,
        ];
    }
}
