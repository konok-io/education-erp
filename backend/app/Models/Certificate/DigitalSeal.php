<?php

declare(strict_types=1);

namespace App\Models\Certificate;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DigitalSeal extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'digital_seals';

    protected $fillable = [
        'uuid',
        'seal_name',
        'seal_code',
        'institution_name',
        'seal_image',
        'seal_type',
        'encryption_key',
        'metadata',
        'status',
        'is_active',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    // ===================== TYPES =====================
    public const TYPE_OFFICIAL = 'official';
    public const TYPE_ACADEMIC = 'academic';
    public const TYPE_CONTROLLER = 'controller';
    public const TYPE_PRINCIPAL = 'principal';

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('is_active', true);
    }

    // ===================== METHODS =====================

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    public static function sealTypes(): array
    {
        return [
            self::TYPE_OFFICIAL => 'Official Seal',
            self::TYPE_ACADEMIC => 'Academic Seal',
            self::TYPE_CONTROLLER => 'Controller Seal',
            self::TYPE_PRINCIPAL => 'Principal Seal',
        ];
    }
}
