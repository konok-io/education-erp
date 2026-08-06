<?php

declare(strict_types=1);

namespace App\Models\DevSecOps;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DevSecOpsArtifact extends Model
{
    use HasFactory, HasUuids, HasUuid;

    protected $table = 'devsecops_artifacts';

    protected $fillable = [
        'pipeline_run_id',
        'name',
        'version',
        'type',
        'path',
        'registry',
        'repository',
        'digest',
        'size',
        'metadata',
        'labels',
        'sbom',
        'provenance',
        'signed',
        'signature',
        'scan_status',
        'scan_results',
        'vulnerability_count',
        'critical_vulnerabilities',
        'license',
        'dependencies',
        'created_by',
    ];

    protected $casts = [
        'metadata' => 'array',
        'labels' => 'array',
        'sbom' => 'array',
        'provenance' => 'array',
        'scan_results' => 'array',
        'dependencies' => 'array',
        'signed' => 'boolean',
        'vulnerability_count' => 'integer',
        'critical_vulnerabilities' => 'integer',
    ];

    public function pipelineRun(): BelongsTo
    {
        return $this->belongsTo(DevSecOpsPipelineRun::class, 'pipeline_run_id');
    }

    public function securityScans(): HasMany
    {
        return $this->hasMany(DevSecOpsSecurityScan::class, 'artifact_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfRegistry($query, string $registry)
    {
        return $query->where('registry', $registry);
    }

    public function scopeScanned($query)
    {
        return $query->where('scan_status', 'completed');
    }

    public function scopeHasVulnerabilities($query)
    {
        return $query->where('vulnerability_count', '>', 0);
    }

    public function isScanned(): bool
    {
        return $this->scan_status === 'completed';
    }

    public function hasCriticalVulnerabilities(): bool
    {
        return $this->critical_vulnerabilities > 0;
    }

    public function isSigned(): bool
    {
        return $this->signed;
    }
}
