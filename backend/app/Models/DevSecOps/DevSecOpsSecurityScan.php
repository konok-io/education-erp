<?php

declare(strict_types=1);

namespace App\Models\DevSecOps;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevSecOpsSecurityScan extends Model
{
    use HasFactory, HasUuids, HasUuid;

    protected $table = 'devsecops_security_scans';

    protected $fillable = [
        'pipeline_run_id',
        'artifact_id',
        'type',
        'tool',
        'status',
        'severity',
        'results',
        'vulnerabilities',
        'secrets_found',
        'compliance',
        'vulnerability_count',
        'critical_count',
        'high_count',
        'medium_count',
        'low_count',
        'info_count',
        'report_path',
        'summary',
        'started_at',
        'completed_at',
        'duration',
        'metadata',
        'scan_by',
    ];

    protected $casts = [
        'results' => 'array',
        'vulnerabilities' => 'array',
        'secrets_found' => 'array',
        'compliance' => 'array',
        'metadata' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration' => 'integer',
        'vulnerability_count' => 'integer',
        'critical_count' => 'integer',
        'high_count' => 'integer',
        'medium_count' => 'integer',
        'low_count' => 'integer',
        'info_count' => 'integer',
    ];

    public function pipelineRun(): BelongsTo
    {
        return $this->belongsTo(DevSecOpsPipelineRun::class, 'pipeline_run_id');
    }

    public function artifact(): BelongsTo
    {
        return $this->belongsTo(DevSecOpsArtifact::class, 'artifact_id');
    }

    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scan_by');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfTool($query, string $tool)
    {
        return $query->where('tool', $tool);
    }

    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOfSeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function hasVulnerabilities(): bool
    {
        return $this->vulnerability_count > 0;
    }

    public function hasCriticalVulnerabilities(): bool
    {
        return $this->critical_count > 0;
    }

    public function hasSecrets(): bool
    {
        return !empty($this->secrets_found);
    }
}
