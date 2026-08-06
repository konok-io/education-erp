<?php

declare(strict_types=1);

namespace App\DTO\DevSecOps;

class EnvironmentDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly string $type,
        public readonly ?string $description = null,
        public readonly ?string $cluster = null,
        public readonly ?string $namespace = null,
        public readonly ?array $config = null,
        public readonly ?array $variables = null,
        public readonly bool $isActive = true,
        public readonly int $sortOrder = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'],
            type: $data['type'],
            description: $data['description'] ?? null,
            cluster: $data['cluster'] ?? null,
            namespace: $data['namespace'] ?? null,
            config: $data['config'] ?? null,
            variables: $data['variables'] ?? null,
            isActive: $data['is_active'] ?? true,
            sortOrder: $data['sort_order'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'description' => $this->description,
            'cluster' => $this->cluster,
            'namespace' => $this->namespace,
            'config' => $this->config,
            'variables' => $this->variables,
            'is_active' => $this->isActive,
            'sort_order' => $this->sortOrder,
        ];
    }
}
