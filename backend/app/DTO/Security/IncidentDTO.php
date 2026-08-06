<?php

declare(strict_types=1);

namespace App\DTO\Security;

use App\Enums\Security\ComplianceStandard;
use App\Enums\Security\IncidentSeverity;
use App\Enums\Security\IncidentStatus;
use App\Enums\Security\VulnerabilitySeverity;
use Illuminate\Http\Request;

final class IncidentDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $title,
        public readonly string $description,
        public readonly IncidentSeverity $severity,
        public readonly IncidentStatus $status = IncidentStatus::DETECTED,
        public readonly ?string $category,
        public readonly ?string $affected_system,
        public readonly ?string $affected_module,
        public readonly ?string $reported_by,
        public readonly ?string $assigned_to,
        public readonly ?\DateTimeInterface $detected_at,
        public readonly ?\DateTimeInterface $resolved_at,
        public readonly ?string $resolution_notes,
        public readonly ?array $evidence,
        public readonly ?string $lessons_learned,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            title: $request->input('title'),
            description: $request->input('description'),
            severity: IncidentSeverity::from($request->input('severity')),
            status: IncidentStatus::tryFrom($request->input('status', 'detected')) ?? IncidentStatus::DETECTED,
            category: $request->input('category'),
            affected_system: $request->input('affected_system'),
            affected_module: $request->input('affected_module'),
            reported_by: $request->input('reported_by'),
            assigned_to: $request->input('assigned_to'),
            detected_at: $request->input('detected_at') ? new \DateTime($request->input('detected_at')) : null,
            resolved_at: $request->input('resolved_at') ? new \DateTime($request->input('resolved_at')) : null,
            resolution_notes: $request->input('resolution_notes'),
            evidence: $request->input('evidence'),
            lessons_learned: $request->input('lessons_learned'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'severity' => $this->severity->value,
            'status' => $this->status->value,
            'category' => $this->category,
            'affected_system' => $this->affected_system,
            'affected_module' => $this->affected_module,
            'reported_by' => $this->reported_by,
            'assigned_to' => $this->assigned_to,
            'detected_at' => $this->detected_at?->format('Y-m-d H:i:s'),
            'resolved_at' => $this->resolved_at?->format('Y-m-d H:i:s'),
            'resolution_notes' => $this->resolution_notes,
            'evidence' => $this->evidence,
            'lessons_learned' => $this->lessons_learned,
        ];
    }
}

final class VulnerabilityDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $title,
        public readonly string $description,
        public readonly VulnerabilitySeverity $severity,
        public readonly string $cwe_id,
        public readonly ?string $cve_id,
        public readonly ?string $affected_component,
        public readonly ?string $affected_version,
        public readonly ?string $fixed_version,
        public readonly string $status = 'open',
        public readonly ?string $remediation_steps,
        public readonly ?string $references,
        public readonly ?string $reported_by,
        public readonly ?\DateTimeInterface $discovered_at,
        public readonly ?\DateTimeInterface $fixed_at,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            title: $request->input('title'),
            description: $request->input('description'),
            severity: VulnerabilitySeverity::from($request->input('severity')),
            cwe_id: $request->input('cwe_id'),
            cve_id: $request->input('cve_id'),
            affected_component: $request->input('affected_component'),
            affected_version: $request->input('affected_version'),
            fixed_version: $request->input('fixed_version'),
            status: $request->input('status', 'open'),
            remediation_steps: $request->input('remediation_steps'),
            references: $request->input('references'),
            reported_by: $request->input('reported_by'),
            discovered_at: $request->input('discovered_at') ? new \DateTime($request->input('discovered_at')) : null,
            fixed_at: $request->input('fixed_at') ? new \DateTime($request->input('fixed_at')) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'severity' => $this->severity->value,
            'cwe_id' => $this->cwe_id,
            'cve_id' => $this->cve_id,
            'affected_component' => $this->affected_component,
            'affected_version' => $this->affected_version,
            'fixed_version' => $this->fixed_version,
            'status' => $this->status,
            'remediation_steps' => $this->remediation_steps,
            'references' => $this->references,
            'reported_by' => $this->reported_by,
            'discovered_at' => $this->discovered_at?->format('Y-m-d'),
            'fixed_at' => $this->fixed_at?->format('Y-m-d'),
        ];
    }
}
