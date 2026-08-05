<?php

declare(strict_types=1);

namespace App\Models\Research;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResearchActivity extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'research_activities';

    protected $fillable = [
        'uuid', 'user_id', 'activity_type', 'entity_type', 'entity_id',
        'old_values', 'new_values', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // ===================== ACTIVITY TYPES =====================
    public const ACTIVITY_PROJECT_CREATED = 'project_created';
    public const ACTIVITY_PROJECT_UPDATED = 'project_updated';
    public const ACTIVITY_PROJECT_APPROVED = 'project_approved';
    public const ACTIVITY_PROJECT_COMPLETED = 'project_completed';
    public const ACTIVITY_GRANT_APPROVED = 'grant_approved';
    public const ACTIVITY_PUBLICATION_ADDED = 'publication_added';
    public const ACTIVITY_PATENT_REGISTERED = 'patent_registered';
    public const ACTIVITY_REPOSITORY_UPDATED = 'repository_updated';
    public const ACTIVITY_MILESTONE_COMPLETED = 'milestone_completed';
    public const ACTIVITY_TEAM_ADDED = 'team_added';

    // ===================== RELATIONSHIPS =====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // ===================== METHODS =====================

    public static function activityTypes(): array
    {
        return [
            self::ACTIVITY_PROJECT_CREATED => 'Project Created',
            self::ACTIVITY_PROJECT_UPDATED => 'Project Updated',
            self::ACTIVITY_PROJECT_APPROVED => 'Project Approved',
            self::ACTIVITY_PROJECT_COMPLETED => 'Project Completed',
            self::ACTIVITY_GRANT_APPROVED => 'Grant Approved',
            self::ACTIVITY_PUBLICATION_ADDED => 'Publication Added',
            self::ACTIVITY_PATENT_REGISTERED => 'Patent Registered',
            self::ACTIVITY_REPOSITORY_UPDATED => 'Repository Updated',
            self::ACTIVITY_MILESTONE_COMPLETED => 'Milestone Completed',
            self::ACTIVITY_TEAM_ADDED => 'Team Member Added',
        ];
    }

    public static function log(
        string $activityType,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        return self::create([
            'user_id' => auth()->id(),
            'activity_type' => $activityType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
