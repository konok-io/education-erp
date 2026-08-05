<?php

declare(strict_types=1);

namespace App\Models\Research;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundingAgency extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'funding_agencies';

    protected $fillable = [
        'uuid', 'agency_code', 'agency_name', 'agency_type', 'description',
        'website', 'contact_person', 'email', 'phone', 'address', 'country',
        'is_active', 'eligibility_criteria', 'funding_types',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===================== AGENCY TYPES =====================
    public const TYPE_GOVERNMENT = 'government';
    public const TYPE_PRIVATE = 'private';
    public const TYPE_UNIVERSITY = 'university';
    public const TYPE_NGO = 'ngo';
    public const TYPE_INTERNATIONAL = 'international';
    public const TYPE_INDUSTRY = 'industry';

    // ===================== RELATIONSHIPS =====================

    public function grants(): HasMany
    {
        return $this->hasMany(ResearchGrant::class, 'funding_agency_id');
    }

    // ===================== METHODS =====================

    public static function generateAgencyCode(): string
    {
        return 'AGN-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }

    public static function agencyTypes(): array
    {
        return [
            self::TYPE_GOVERNMENT => 'Government',
            self::TYPE_PRIVATE => 'Private',
            self::TYPE_UNIVERSITY => 'University',
            self::TYPE_NGO => 'NGO',
            self::TYPE_INTERNATIONAL => 'International Organization',
            self::TYPE_INDUSTRY => 'Industry',
        ];
    }
}
