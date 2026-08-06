<?php

declare(strict_types=1);

namespace App\Services\DevSecOps;

use App\DTO\DevSecOps\SecurityScanDTO;
use App\Models\DevSecOps\DevSecOpsSecurityScan;
use Illuminate\Support\Collection;

class SecurityScanService extends DevSecOpsBaseService
{
    public function getAll(array $filters = [], int $perPage = 15)
    {
        $query = DevSecOpsSecurityScan::with('pipelineRun', 'artifact');

        if (isset($filters['type'])) {
            $query->ofType($filters['type']);
        }

        if (isset($filters['tool'])) {
            $query->ofTool($filters['tool']);
        }

        if (isset($filters['status'])) {
            $query->ofStatus($filters['status']);
        }

        if (isset($filters['severity'])) {
            $query->ofSeverity($filters['severity']);
        }

        if (isset($filters['pipeline_run_id'])) {
            $query->where('pipeline_run_id', $filters['pipeline_run_id']);
        }

        if (isset($filters['artifact_id'])) {
            $query->where('artifact_id', $filters['artifact_id']);
        }

        $query->orderBy('created_at', 'desc');

        return $this->paginate($query, $perPage);
    }

    public function getById(string $id): ?DevSecOpsSecurityScan
    {
        return DevSecOpsSecurityScan::with('pipelineRun', 'artifact', 'scannedBy')->find($id);
    }

    public function create(SecurityScanDTO $dto): DevSecOpsSecurityScan
    {
        $data = $dto->toArray();
        $data['status'] = 'pending';
        $data['severity'] = 'none';
        $data['scan_by'] = auth()->id();

        $scan = DevSecOpsSecurityScan::create($data);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_SECURITY_SCAN,
            action: 'created',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $scan->id,
            resourceType: DevSecOpsSecurityScan::class,
            resourceName: "{$scan->type} scan using {$scan->tool}",
            metadata: ['type' => $scan->type, 'tool' => $scan->tool],
            message: "Security scan {$scan->type} created using {$scan->tool}",
        );

        return $scan;
    }

    public function updateResults(string $id, array $results, array $vulnerabilities = [], array $secretsFound = []): ?DevSecOpsSecurityScan
    {
        $scan = $this->getById($id);

        if (!$scan) {
            return null;
        }

        $criticalCount = collect($vulnerabilities)->where('severity', 'critical')->count();
        $highCount = collect($vulnerabilities)->where('severity', 'high')->count();
        $mediumCount = collect($vulnerabilities)->where('severity', 'medium')->count();
        $lowCount = collect($vulnerabilities)->where('severity', 'low')->count();
        $infoCount = collect($vulnerabilities)->where('severity', 'info')->count();

        $severity = 'none';
        if ($criticalCount > 0) {
            $severity = 'critical';
        } elseif ($highCount > 0) {
            $severity = 'high';
        } elseif ($mediumCount > 0) {
            $severity = 'medium';
        } elseif ($lowCount > 0) {
            $severity = 'low';
        }

        $scan->update([
            'status' => 'completed',
            'severity' => $severity,
            'results' => $results,
            'vulnerabilities' => $vulnerabilities,
            'secrets_found' => $secretsFound,
            'vulnerability_count' => count($vulnerabilities),
            'critical_count' => $criticalCount,
            'high_count' => $highCount,
            'medium_count' => $mediumCount,
            'low_count' => $lowCount,
            'info_count' => $infoCount,
            'completed_at' => now(),
            'duration' => $scan->started_at ? now()->diffInSeconds($scan->started_at) : null,
        ]);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_SECURITY_SCAN,
            action: 'completed',
            status: $criticalCount > 0 ? DevSecOpsActivityLog::STATUS_WARNING : DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $scan->id,
            resourceType: DevSecOpsSecurityScan::class,
            resourceName: "{$scan->type} scan",
            metadata: [
                'vulnerability_count' => count($vulnerabilities),
                'critical_count' => $criticalCount,
                'severity' => $severity,
            ],
            message: "Security scan completed: " . count($vulnerabilities) . " vulnerabilities found",
        );

        return $scan->fresh();
    }

    public function startScan(string $id): ?DevSecOpsSecurityScan
    {
        $scan = $this->getById($id);

        if (!$scan) {
            return null;
        }

        $scan->update([
            'status' => 'running',
            'started_at' => now(),
        ]);

        return $scan->fresh();
    }

    public function failScan(string $id, string $errorMessage): ?DevSecOpsSecurityScan
    {
        $scan = $this->getById($id);

        if (!$scan) {
            return null;
        }

        $scan->update([
            'status' => 'failed',
            'summary' => $errorMessage,
            'completed_at' => now(),
            'duration' => $scan->started_at ? now()->diffInSeconds($scan->started_at) : null,
        ]);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_SECURITY_SCAN,
            action: 'failed',
            status: DevSecOpsActivityLog::STATUS_FAILED,
            resourceId: $scan->id,
            resourceType: DevSecOpsSecurityScan::class,
            resourceName: "{$scan->type} scan",
            message: "Security scan failed: {$errorMessage}",
        );

        return $scan->fresh();
    }

    public function getByPipelineRun(string $pipelineRunId): Collection
    {
        return DevSecOpsSecurityScan::where('pipeline_run_id', $pipelineRunId)
            ->orderBy('type')
            ->get();
    }

    public function getByArtifact(string $artifactId): Collection
    {
        return DevSecOpsSecurityScan::where('artifact_id', $artifactId)
            ->orderBy('type')
            ->get();
    }

    public function getRecentWithVulnerabilities(int $days = 7, int $limit = 20): Collection
    {
        return DevSecOpsSecurityScan::where('status', 'completed')
            ->where('vulnerability_count', '>', 0)
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('critical_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getStats(): array
    {
        $totalScans = DevSecOpsSecurityScan::count();
        $completedScans = DevSecOpsSecurityScan::where('status', 'completed')->count();
        $failedScans = DevSecOpsSecurityScan::where('status', 'failed')->count();
        $scansWithVulnerabilities = DevSecOpsSecurityScan::where('status', 'completed')
            ->where('vulnerability_count', '>', 0)
            ->count();
        $criticalVulnerabilities = DevSecOpsSecurityScan::sum('critical_count');

        return [
            'total_scans' => $totalScans,
            'completed_scans' => $completedScans,
            'failed_scans' => $failedScans,
            'scans_with_vulnerabilities' => $scansWithVulnerabilities,
            'critical_vulnerabilities' => $criticalVulnerabilities,
            'success_rate' => $totalScans > 0 ? round(($completedScans / $totalScans) * 100, 2) : 0,
        ];
    }
}
