<?php

declare(strict_types=1);

namespace App\Models\Research;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Innovation extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'innovations';

    protected $fillable = [
        'uuid', 'innovation_code', 'title', 'description', 'innovation_type',
        'project_id', 'stage', 'technology_details', 'market_potential',
        'has_patent', 'patent_number', 'trademark', 'prototype_url', 'demo_video',
        'status', 'team_members', 'funding_required', 'funding_currency',
        'thumbnail', 'images', 'created_by',
    ];

    protected $casts = [
        'team_members' => 'array',
        'images' => 'array',
        'funding_required' => 'decimal:2',
        'has_patent' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_IDEA = 'idea';
    public const STATUS_IN_DEVELOPMENT = 'in_development';
    public const STATUS_PROTOTYPE = 'prototype';
    public const STATUS_TESTING = 'testing';
    public const STATUS_LAUNCHED = 'launched';
    public const STATUS_COMMERCIALIZED = 'commercialized';

    // ===================== STAGES =====================
    public const STAGE_IDEA = 'idea';
    public const STAGE_RESEARCH = 'research';
    public const STAGE_DEVELOPMENT = 'development';
    public const STAGE_PROTOTYPE = 'prototype';
    public const STAGE_BETA = 'beta';
    public const STAGE_LAUNCH = 'launch';

    // ===================== TYPES =====================
    public const TYPE_PRODUCT = 'product';
    public const TYPE_PROCESS = 'process';
    public const TYPE_SERVICE = 'service';
    public const TYPE_SOFTWARE = 'software';
    public const TYPE_TECHNOLOGY = 'technology';

    // ===================== RELATIONSHIPS =====================

    public function project(): BelongsTo
    {
        return $this->belongsTo(ResearchProject::class, 'project_id');
    }

    // ===================== METHODS =====================

    public static function generateInnovationCode(): string
    {
        return 'INN-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_IDEA => 'Idea',
            self::STATUS_IN_DEVELOPMENT => 'In Development',
            self::STATUS_PROTOTYPE => 'Prototype',
            self::STATUS_TESTING => 'Testing',
            self::STATUS_LAUNCHED => 'Launched',
            self::STATUS_COMMERCIALIZED => 'Commercialized',
        ];
    }

    public static function stages(): array
    {
        return [
            self::STAGE_IDEA => 'Idea',
            self::STAGE_RESEARCH => 'Research',
            self::STAGE_DEVELOPMENT => 'Development',
            self::STAGE_PROTOTYPE => 'Prototype',
            self::STAGE_BETA => 'Beta Testing',
            self::STAGE_LAUNCH => 'Launch',
        ];
    }
}
