<?php

declare(strict_types=1);

namespace App\DTO\DevSecOps;

class PipelineRunDTO
{
    public function __construct(
        public readonly string $pipelineId,
        public readonly string $runNumber,
        public readonly string $status,
        public readonly string $trigger,
        public readonly ?string $branch = null,
        public readonly ?string $commitSha = null,
        public readonly ?string $commitMessage = null,
        public readonly ?string $author = null,
        public readonly ?array $stages = null,
        public readonly ?array $jobs = null,
        public readonly ?array $artifacts = null,
        public readonly ?array $metadata = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            pipelineId: $data['pipeline_id'],
            runNumber: $data['run_number'],
            status: $data['status'],
            trigger: $data['trigger'],
            branch: $data['branch'] ?? null,
            commitSha: $data['commit_sha'] ?? null,
            commitMessage: $data['commit_message'] ?? null,
            author: $data['author'] ?? null,
            stages: $data['stages'] ?? null,
            jobs: $data['jobs'] ?? null,
            artifacts: $data['artifacts'] ?? null,
            metadata: $data['metadata'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'pipeline_id' => $this->pipelineId,
            'run_number' => $this->runNumber,
            'status' => $this->status,
            'trigger' => $this->trigger,
            'branch' => $this->branch,
            'commit_sha' => $this->commitSha,
            'commit_message' => $this->commitMessage,
            'author' => $this->author,
            'stages' => $this->stages,
            'jobs' => $this->jobs,
            'artifacts' => $this->artifacts,
            'metadata' => $this->metadata,
        ];
    }
}
