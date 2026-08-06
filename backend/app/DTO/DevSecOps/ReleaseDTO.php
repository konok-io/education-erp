<?php

declare(strict_types=1);

namespace App\DTO\DevSecOps;

class ReleaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $version,
        public readonly string $type,
        public readonly string $status = 'draft',
        public readonly string $channel = 'stable',
        public readonly ?string $description = null,
        public readonly ?string $gitTag = null,
        public readonly ?string $gitCommit = null,
        public readonly ?array $changelog = null,
        public readonly ?array $breakingChanges = null,
        public readonly ?array $knownIssues = null,
        public readonly ?array $upgradeGuide = null,
        public readonly ?array $artifacts = null,
        public readonly ?string $environmentId = null,
        public readonly bool $isPrerelease = false,
        public readonly bool $isDraft = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            version: $data['version'],
            type: $data['type'],
            status: $data['status'] ?? 'draft',
            channel: $data['channel'] ?? 'stable',
            description: $data['description'] ?? null,
            gitTag: $data['git_tag'] ?? null,
            gitCommit: $data['git_commit'] ?? null,
            changelog: $data['changelog'] ?? null,
            breakingChanges: $data['breaking_changes'] ?? null,
            knownIssues: $data['known_issues'] ?? null,
            upgradeGuide: $data['upgrade_guide'] ?? null,
            artifacts: $data['artifacts'] ?? null,
            environmentId: $data['environment_id'] ?? null,
            isPrerelease: $data['is_prerelease'] ?? false,
            isDraft: $data['is_draft'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'version' => $this->version,
            'type' => $this->type,
            'status' => $this->status,
            'channel' => $this->channel,
            'description' => $this->description,
            'git_tag' => $this->gitTag,
            'git_commit' => $this->gitCommit,
            'changelog' => $this->changelog,
            'breaking_changes' => $this->breakingChanges,
            'known_issues' => $this->knownIssues,
            'upgrade_guide' => $this->upgradeGuide,
            'artifacts' => $this->artifacts,
            'environment_id' => $this->environmentId,
            'is_prerelease' => $this->isPrerelease,
            'is_draft' => $this->isDraft,
        ];
    }
}
