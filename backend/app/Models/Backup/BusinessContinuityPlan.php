<?php

declare(strict_types=1);

namespace App\Models\Backup;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessContinuityPlan extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'business_continuity_plans';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'scope',
        'critical_systems',
        'recovery_priorities',
        'rto_minutes',
        'rpo_minutes',
        'emergency_contacts',
        'escalation_matrix',
        'communication_plan',
        'recovery_procedures',
        'resource_requirements',
        'roles_responsibilities',
        'testing_frequency',
        'last_tested_at',
        'next_test_at',
        'metadata',
        'tenant_id',
        'owner_id',
        'approved_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'critical_systems' => 'array',
        'recovery_priorities' => 'array',
        'emergency_contacts' => 'array',
        'escalation_matrix' => 'array',
        'communication_plan' => 'array',
        'recovery_procedures' => 'array',
        'resource_requirements' => 'array',
        'roles_responsibilities' => 'array',
        'metadata' => 'array',
        'rto_minutes' => 'integer',
        'rpo_minutes' => 'integer',
        'last_tested_at' => 'datetime',
        'next_test_at' => 'datetime',
    ];

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByScope($query, string $scope)
    {
        return $query->where('scope', $scope);
    }

    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
    }

    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }

    public function updateTestSchedule(\DateTime $lastTested, \DateTime $nextTest): void
    {
        $this->update([
            'last_tested_at' => $lastTested,
            'next_test_at' => $nextTest,
        ]);
    }

    public function getFormattedRTOAttribute(): string
    {
        $minutes = $this->rto_minutes;
        if ($minutes < 60) {
            return $minutes . ' minutes';
        }
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return $mins > 0 ? "{$hours}h {$mins}m" : "{$hours} hours";
    }

    public function getFormattedRPOAttribute(): string
    {
        $minutes = $this->rpo_minutes;
        if ($minutes < 60) {
            return $minutes . ' minutes';
        }
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return $mins > 0 ? "{$hours}h {$mins}m" : "{$hours} hours";
    }

    public function needsTesting(): bool
    {
        if (!$this->next_test_at) {
            return true;
        }

        return $this->next_test_at->isPast();
    }
}
