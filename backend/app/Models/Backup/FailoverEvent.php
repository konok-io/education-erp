<?php

declare(strict_types=1);

namespace App\Models\Backup;

use App\Enums\Backup\FailoverStatus;
use App\Enums\Backup\FailoverType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FailoverEvent extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'failover_events';

    protected $fillable = [
        'name',
        'type',
        'status',
        'source_site',
        'destination_site',
        'trigger_reason',
        'trigger_details',
        'affected_services',
        'affected_users',
        'downtime_seconds',
        'initiated_at',
        'completed_at',
        'recovery_time_seconds',
        'error_message',
        'rollback_config',
        'is_rolled_back',
        'metadata',
        'tenant_id',
        'initiated_by',
        'approved_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'affected_services' => 'array',
        'metadata' => 'array',
        'rollback_config' => 'array',
        'affected_users' => 'integer',
        'downtime_seconds' => 'integer',
        'recovery_time_seconds' => 'integer',
        'is_rolled_back' => 'boolean',
        'initiated_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeAutomatic($query)
    {
        return $query->where('type', FailoverType::AUTOMATIC->value);
    }

    public function scopeManual($query)
    {
        return $query->where('type', FailoverType::MANUAL->value);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', FailoverStatus::IN_PROGRESS->value);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', FailoverStatus::COMPLETED->value);
    }

    public function initiate(string $initiatedById, ?string $approvedById = null): void
    {
        $this->update([
            'status' => FailoverStatus::IN_PROGRESS->value,
            'initiated_at' => now(),
            'initiated_by' => $initiatedById,
            'approved_by' => $approvedById,
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => FailoverStatus::COMPLETED->value,
            'completed_at' => now(),
            'recovery_time_seconds' => $this->initiated_at ? $this->initiated_at->diffInSeconds(now()) : 0,
        ]);
    }

    public function fail(string $errorMessage): void
    {
        $this->update([
            'status' => FailoverStatus::FAILED->value,
            'completed_at' => now(),
            'error_message' => $errorMessage,
        ]);
    }

    public function rollback(): void
    {
        $this->update([
            'status' => FailoverStatus::ROLLED_BACK->value,
            'is_rolled_back' => true,
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => FailoverStatus::CANCELLED->value,
            'completed_at' => now(),
        ]);
    }

    public function isAutomatic(): bool
    {
        return $this->type === FailoverType::AUTOMATIC->value;
    }

    public function isSuccessful(): bool
    {
        return $this->status === FailoverStatus::COMPLETED->value;
    }
}
