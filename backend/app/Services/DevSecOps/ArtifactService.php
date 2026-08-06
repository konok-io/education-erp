<?php

declare(strict_types=1);

namespace App\Services\DevSecOps;

use App\DTO\DevSecOps\ArtifactDTO;
use App\Models\DevSecOps\DevSecOpsArtifact;
use Illuminate\Support\Collection;

class ArtifactService extends DevSecOpsBaseService
{
    public function getAll(array $filters = [], int $perPage = 15)
    {
        $query = DevSecOpsArtifact::with('pipelineRun');

        if (isset($filters['type'])) {
            $query->ofType($filters['type']);
        }

        if (isset($filters['registry'])) {
            $query->ofRegistry($filters['registry']);
        }

        if (isset($filters['scan_status'])) {
            $query->where('scan_status', $filters['scan_status']);
        }

        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        $query->orderBy('created_at', 'desc');

        return $this->paginate($query, $perPage);
    }

    public function getById(string $id): ?DevSecOpsArtifact
    {
        return DevSecOpsArtifact::with('pipelineRun', 'securityScans')->find($id);
    }

    public function getByNameAndVersion(string $name, string $version, string $registry): ?DevSecOpsArtifact
    {
        return DevSecOpsArtifact::where('name', $name)
            ->where('version', $version)
            ->where('registry', $registry)
            ->first();
    }

    public function create(ArtifactDTO $dto): DevSecOpsArtifact
    {
        $data = $dto->toArray();
        $data['created_by'] = auth()->id();
        $data['scan_status'] = 'pending';

        $artifact = DevSecOpsArtifact::create($data);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_ARTIFACT,
            action: 'created',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $artifact->id,
            resourceType: DevSecOpsArtifact::class,
            resourceName: "{$artifact->name}:{$artifact->version}",
            metadata: ['type' => $artifact->type, 'registry' => $artifact->registry],
            message: "Artifact {$artifact->name}:{$artifact->version} created",
        );

        return $artifact;
    }

    public function updateScanResults(string $id, array $scanResults, array $vulnerabilities): ?DevSecOpsArtifact
    {
        $artifact = $this->getById($id);

        if (!$artifact) {
            return null;
        }

        $criticalCount = collect($vulnerabilities)->where('severity', 'critical')->count();
        $highCount = collect($vulnerabilities)->where('severity', 'high')->count();
        $mediumCount = collect($vulnerabilities)->where('severity', 'medium')->count();
        $lowCount = collect($vulnerabilities)->where('severity', 'low')->count();

        $artifact->update([
            'scan_status' => 'completed',
            'scan_results' => $scanResults,
            'vulnerabilities' => $vulnerabilities,
            'vulnerability_count' => count($vulnerabilities),
            'critical_vulnerabilities' => $criticalCount,
        ]);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_ARTIFACT,
            action: 'scanned',
            status: $criticalCount > 0 ? DevSecOpsActivityLog::STATUS_WARNING : DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $artifact->id,
            resourceType: DevSecOpsArtifact::class,
            resourceName: "{$artifact->name}:{$artifact->version}",
            metadata: [
                'vulnerability_count' => count($vulnerabilities),
                'critical_count' => $criticalCount,
            ],
            message: "Artifact scanned: " . count($vulnerabilities) . " vulnerabilities found",
        );

        return $artifact->fresh();
    }

    public function updateSbom(string $id, array $sbom): ?DevSecOpsArtifact
    {
        $artifact = $this->getById($id);

        if (!$artifact) {
            return null;
        }

        $artifact->update(['sbom' => $sbom]);

        return $artifact->fresh();
    }

    public function updateProvenance(string $id, array $provenance): ?DevSecOpsArtifact
    {
        $artifact = $this->getById($id);

        if (!$artifact) {
            return null;
        }

        $artifact->update(['provenance' => $provenance]);

        return $artifact->fresh();
    }

    public function sign(string $id, string $signature): ?DevSecOpsArtifact
    {
        $artifact = $this->getById($id);

        if (!$artifact) {
            return null;
        }

        $artifact->update([
            'signed' => true,
            'signature' => $signature,
        ]);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_ARTIFACT,
            action: 'signed',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $artifact->id,
            resourceType: DevSecOpsArtifact::class,
            resourceName: "{$artifact->name}:{$artifact->version}",
            message: "Artifact {$artifact->name}:{$artifact->version} signed",
        );

        return $artifact->fresh();
    }

    public function getLatestByType(string $type, int $limit = 10): Collection
    {
        return DevSecOpsArtifact::where('type', $type)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getVulnerable(): Collection
    {
        return DevSecOpsArtifact::hasVulnerabilities()
            ->scanned()
            ->orderBy('critical_vulnerabilities', 'desc')
            ->get();
    }

    public function delete(string $id): bool
    {
        $artifact = $this->getById($id);

        if (!$artifact) {
            return false;
        }

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_ARTIFACT,
            action: 'deleted',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $artifact->id,
            resourceType: DevSecOpsArtifact::class,
            resourceName: "{$artifact->name}:{$artifact->version}",
            message: "Artifact {$artifact->name}:{$artifact->version} deleted",
        );

        return $artifact->delete();
    }
}
