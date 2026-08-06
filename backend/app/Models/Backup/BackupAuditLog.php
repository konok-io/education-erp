<?php

declare(strict_types=1);

namespace App\Models\Backup;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BackupAuditLog extends Model
{
    use HasUuids, HasUuid;

    protected $table = 'backup_audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'event_type',
        'severity',
        'category',
        'reference_id',
        'reference_type',
        'message',
        'event_data',
        'metadata',
        'ip_address',
        'user_agent',
        'environment',
        'region',
        'user_id',
        'user_name',
        'occurred_at',
    ];

    protected $casts = [
        'event_data' => 'array',
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public const CATEGORY_BACKUP = 'backup';
    public const CATEGORY_RECOVERY = 'recovery';
    public const CATEGORY_REPLICATION = 'replication';
    public const CATEGORY_FAILOVER = 'failover';
    public const CATEGORY_ARCHIVE = 'archive';

    public const EVENT_BACKUP_STARTED = 'backup_started';
    public const EVENT_BACKUP_COMPLETED = 'backup_completed';
    public const EVENT_BACKUP_FAILED = 'backup_failed';
    public const EVENT_RESTORE_INITIATED = 'restore_initiated';
    public const EVENT_RESTORE_COMPLETED = 'restore_completed';
    public const EVENT_REPLICATION_STARTED = 'replication_started';
    public const EVENT_REPLICATION_FAILED = 'replication_failed';
    public const EVENT_FAILOVER_EXECUTED = 'failover_executed';
    public const EVENT_FAILOVER_COMPLETED = 'failover_completed';
    public const EVENT_RECOVERY_VERIFIED = 'recovery_verified';
    public const EVENT_ARCHIVE_CREATED = 'archive_created';
    public const EVENT_ARCHIVE_RESTORED = 'archive_restored';

    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByReference($query, string $type, string $id)
    {
        return $query->where('reference_type', $type)
            ->where('reference_id', $id);
    }

    public function scopeByEnvironment($query, string $environment)
    {
        return $query->where('environment', $environment);
    }

    public function scopeByUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBetween($query, $start, $end)
    {
        return $query->whereBetween('occurred_at', [$start, $end]);
    }

    public function scopeLatest($query, int $limit = 100)
    {
        return $query->orderBy('occurred_at', 'desc')->limit($limit);
    }

    public static function log(
        string $eventType,
        string $severity,
        string $category,
        string $message,
        ?string $referenceId = null,
        ?string $referenceType = null,
        ?array $eventData = null,
        ?array $metadata = null,
        ?string $userId = null,
        ?string $userName = null,
        string $environment = 'production',
        ?string $region = null
    ): self {
        return self::create([
            'event_type' => $eventType,
            'severity' => $severity,
            'category' => $category,
            'message' => $message,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'event_data' => $eventData,
            'metadata' => $metadata,
            'user_id' => $userId,
            'user_name' => $userName,
            'environment' => $environment,
            'region' => $region,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'occurred_at' => now(),
        ]);
    }
}
