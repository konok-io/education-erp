<?php

declare(strict_types=1);

namespace App\Models\Research;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patent extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'patents';

    protected $fillable = [
        'uuid', 'patent_number', 'patent_title', 'abstract', 'project_id',
        'patent_type', 'status', 'country', 'application_date', 'publication_date',
        'grant_date', 'expiry_date', 'inventors', 'applicant', 'assignee',
        'application_number', 'publication_number', 'ip_office', 'claims',
        'patent_document', 'cost', 'cost_currency', 'is_active', 'created_by',
    ];

    protected $casts = [
        'inventors' => 'array',
        'cost' => 'decimal:2',
        'application_date' => 'date',
        'publication_date' => 'date',
        'grant_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_EXAMINED = 'examined';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_GRANTED = 'granted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_LAPSED = 'lapsed';

    // ===================== TYPES =====================
    public const TYPE_INVENTION = 'invention';
    public const TYPE_UTILITY = 'utility';
    public const TYPE_DESIGN = 'design';
    public const TYPE_PLANT = 'plant';

    // ===================== RELATIONSHIPS =====================

    public function project(): BelongsTo
    {
        return $this->belongsTo(ResearchProject::class, 'project_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // ===================== METHODS =====================

    public static function generatePatentNumber(): string
    {
        return 'PAT-' . strtoupper(substr(md5(uniqid()), 0, 10));
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_EXAMINED => 'Examined',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_GRANTED => 'Granted',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_LAPSED => 'Lapsed',
        ];
    }

    public static function patentTypes(): array
    {
        return [
            self::TYPE_INVENTION => 'Invention Patent',
            self::TYPE_UTILITY => 'Utility Patent',
            self::TYPE_DESIGN => 'Design Patent',
            self::TYPE_PLANT => 'Plant Patent',
        ];
    }
}
