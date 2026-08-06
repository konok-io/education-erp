<?php

declare(strict_types=1);

namespace App\Models\DevSecOps;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevSecOpsActivityLog extends Model
{
    use HasFactory, HasUuids, HasUuid;

    protected $table = 'devsecops_activity_logs';

    protected $fillable = [
        'type',
        'action',
        'status',
        'resource_id',
        'resource_type',
        'resource_name',
        'details',
        'metadata',
        'message',
        'changes',
        'actor_id',
        'actor_type',
        'actor_name',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'details' => 'array',
        'metadata' => 'array',
        'changes' => 'array',
    ];

    public const TYPE_PIPELINE = 'pipeline';
    public const TYPE_DEPLOYMENT = 'deployment';
    public const TYPE_RELEASE = 'release';
    public const TYPE_SECURITY_SCAN = 'security_scan';
    public const TYPE_ROLLBACK = 'rollback';
    public const TYPE_ARTIFACT = 'artifact';
    public const TYPE_GITOPS = 'gitops';

    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    public const STATUS_WARNING = 'warning';

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForResource($query, string $resourceType, string $resourceId)
    {
        return $query->where('resource_type', $resourceType)->where('resource_id', $resourceId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByActor($query, string $actorId)
    {
        return $query->where('actor_id', $actorId);
    }

    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public static function log(
        string $type,
        string $action,
        string $status,
        ?string $resourceId = null,
        ?string $resourceType = null,
        ?string $resourceName = null,
        ?array $details = null,
        ?array $metadata = null,
        ?string $message = null,
        ?array $changes = null,
        ?string $actorId = null,
        ?string $actorType = null,
        ?string $actorName = null
    ): self {
        return self::create([
            'type' => $type,
            'action' => $action,
            'status' => $status,
            'resource_id' => $resourceId,
            'resource_type' => $resourceType,
            'resource_name' => $resourceName,
            'details' => $details,
            'metadata' => $metadata,
            'message' => $message,
            'changes' => $changes,
            'actor_id' => $actorId,
            'actor_type' => $actorType,
            'actor_name' => $actorName,
        ]);
    }
}
