<?php

declare(strict_types=1);

namespace App\DTO\DevSecOps;

class SecurityScanDTO
{
    public function __construct(
        public readonly string $type,
        public readonly string $tool,
        public readonly ?string $pipelineRunId = null,
        public readonly ?string $artifactId = null,
        public readonly ?array $results = null,
        public readonly ?array $vulnerabilities = null,
        public readonly ?array $secretsFound = null,
        public readonly ?array $compliance = null,
        public readonly ?string $reportPath = null,
        public readonly ?string $summary = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            tool: $data['tool'],
            pipelineRunId: $data['pipeline_run_id'] ?? null,
            artifactId: $data['artifact_id'] ?? null,
            results: $data['results'] ?? null,
            vulnerabilities: $data['vulnerabilities'] ?? null,
            secretsFound: $data['secrets_found'] ?? null,
            compliance: $data['compliance'] ?? null,
            reportPath: $data['report_path'] ?? null,
            summary: $data['summary'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'tool' => $this->tool,
            'pipeline_run_id' => $this->pipelineRunId,
            'artifact_id' => $this->artifactId,
            'results' => $this->results,
            'vulnerabilities' => $this->vulnerabilities,
            'secrets_found' => $this->secretsFound,
            'compliance' => $this->compliance,
            'report_path' => $this->reportPath,
            'summary' => $this->summary,
        ];
    }
}
