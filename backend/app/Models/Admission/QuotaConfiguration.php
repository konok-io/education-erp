<?php

declare(strict_types=1);

namespace App\Models\Admission;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotaConfiguration extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'quota_configurations';

    protected $fillable = [
        'uuid',
        'quota_type',
        'campaign_id',
        'percentage',
        'reserved_seats',
        'min_gpa',
        'required_documents',
        'is_active',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'reserved_seats' => 'integer',
        'min_gpa' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public static function getByType(string $quotaType, ?int $campaignId = null): ?self
    {
        $query = self::where('quota_type', $quotaType)->where('is_active', true);

        if ($campaignId) {
            $query->where('campaign_id', $campaignId);
        }

        return $query->first();
    }

    public static function quotas(): array
    {
        return [
            AdmissionApplication::QUOTA_GENERAL => 'General',
            AdmissionApplication::QUOTA_FREEDOM_FIGHTER => 'Freedom Fighter',
            AdmissionApplication::QUOTA_TRIBAL => 'Tribal',
            AdmissionApplication::QUOTA_DISABLED => 'Disabled',
            AdmissionApplication::QUOTA_WOMEN => 'Women',
            AdmissionApplication::QUOTA_EMPLOYEE => 'Employee Children',
        ];
    }
}
