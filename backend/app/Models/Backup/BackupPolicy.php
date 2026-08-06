<?php

declare(strict_types=1);

namespace App\Models\Backup;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BackupPolicy extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'backup_policies';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'status',
        'schedule_config',
        'backup_type',
        'source_type',
        'source_config',
        'destination_type',
        'destination_config',
        'encryption',
        'compression',
        'retention_days',
        'retention_copies',
        'immutable',
        'immutable_days',
        'verify_on_backup',
        'auto_prune',
        'max_backup_size_bytes',
        'notifications',
        'metadata',
        'environment',
        'tenant_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'schedule_config' => 'array',
        'source_config' => 'array',
        'destination_config' => 'array',
        'notifications' => 'array',
        'metadata' => 'array',
        'retention_days' => 'integer',
        'retention_copies' => 'integer',
        'immutable_days' => 'integer',
        'max_backup_size_bytes' => 'integer',
        'verify_on_backup' => 'boolean',
        'auto_prune' => 'boolean',
        'immutable' => 'boolean',
    ];

    public function recoveryDrills(): HasMany
    {
        return $this->hasMany(RecoveryDrill::class, 'backup_policy_id');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
    }

    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    public function getScheduleDescriptionAttribute(): string
    {
        $config = $this->schedule_config;
        if (!$config) {
            return 'Not scheduled';
        }

        $frequency = $config['frequency'] ?? 'manual';
        $time = $config['time'] ?? '';
        $dayOfWeek = $config['day_of_week'] ?? '';
        $dayOfMonth = $config['day_of_month'] ?? '';

        $schedule = ucfirst($frequency);
        if ($time) {
            $schedule .= " at {$time}";
        }
        if ($dayOfWeek) {
            $schedule .= " on " . ucfirst(implode(', ', (array) $dayOfWeek));
        }
        if ($dayOfMonth) {
            $schedule .= " on day {$dayOfMonth}";
        }

        return $schedule;
    }
}
