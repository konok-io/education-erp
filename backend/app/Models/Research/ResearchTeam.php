<?php

declare(strict_types=1);

namespace App\Models\Research;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResearchTeam extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'research_teams';

    protected $fillable = [
        'uuid', 'project_id', 'user_id', 'member_name', 'member_email',
        'designation', 'department', 'institution', 'role', 'responsibilities',
        'start_date', 'end_date', 'is_active', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // ===================== ROLES =====================
    public const ROLE_PI = 'principal_investigator';
    public const ROLE_CO_PI = 'co_investigator';
    public const ROLE_RESEARCHER = 'researcher';
    public const ROLE_RESEARCH_ASSISTANT = 'research_assistant';
    public const ROLE_STUDENT = 'student';
    public const ROLE_EXTERNAL = 'external_member';

    // ===================== RELATIONSHIPS =====================

    public function project(): BelongsTo
    {
        return $this->belongsTo(ResearchProject::class, 'project_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // ===================== METHODS =====================

    public static function roles(): array
    {
        return [
            self::ROLE_PI => 'Principal Investigator',
            self::ROLE_CO_PI => 'Co-Investigator',
            self::ROLE_RESEARCHER => 'Researcher',
            self::ROLE_RESEARCH_ASSISTANT => 'Research Assistant',
            self::ROLE_STUDENT => 'Student Researcher',
            self::ROLE_EXTERNAL => 'External Member',
        ];
    }
}
