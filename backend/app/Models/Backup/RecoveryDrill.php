<?php

declare(strict_types=1);

namespace App\Models\Backup;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecoveryDrill extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'recovery_drills';

    protected $fillable = [
        'backup_policy_id',
        'dr_site_id',
        'name',
        'type',
        'status',
        'scenario',
        'description',
        'drill_config',
        'scheduled_at',
        'started_at',
        'completed_at',
        'planned_duration_minutes',
        'actual_duration_minutes',
        'recovery_time_minutes',
        'data_loss_minutes',
        'rto_achieved',
        'rpo_achieved',
        'findings',
        'recommendations',
        'lessons_learned',
        'attachments',
        'participants',
        'metadata',
        'tenant_id',
        'conducted_by',
        'approved_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'drill_config' => 'array',
        'attachments' => 'array',
        'participants' => 'array',
        'metadata' => 'array',
        'planned_duration_minutes' => 'integer',
        'actual_duration_minutes' => 'integer',
        'recovery_time_minutes' => 'integer',
        'data_loss_minutes' => 'integer',
        'rto_achieved' => 'boolean',
        'rpo_achieved' => 'boolean',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function backupPolicy(): BelongsTo
    {
        return $this->belongsTo(BackupPolicy::class, 'backup_policy_id');
    }

    public function drSite(): BelongsTo
    {
        return $this->belongsTo(DRSite::class, 'dr_site_id');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function schedule(\DateTime $scheduledAt): void
    {
        $this->update([
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]);
    }

    public function start(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function complete(
        int $recoveryTimeMinutes,
        int $dataLossMinutes,
        bool $rtoAchieved,
        bool $rpoAchieved
    ): void {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'actual_duration_minutes' => $this->started_at
                ? $this->started_at->diffInMinutes(now())
                : 0,
            'recovery_time_minutes' => $recoveryTimeMinutes,
            'data_loss_minutes' => $dataLossMinutes,
            'rto_achieved' => $rtoAchieved,
            'rpo_achieved' => $rpoAchieved,
        ]);
    }

    public function fail(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'findings' => $reason,
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'completed_at' => now(),
        ]);
    }

    public function addFindings(string $findings, ?string $recommendations = null, ?string $lessonsLearned = null): void
    {
        $this->update([
            'findings' => $findings,
            'recommendations' => $recommendations,
            'lessons_learned' => $lessonsLearned,
        ]);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }
}
