<?php

declare(strict_types=1);

namespace App\Models\Certificate;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateTemplate extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'certificate_templates';

    protected $fillable = [
        'uuid',
        'template_name',
        'template_code',
        'certificate_type',
        'template_content',
        'template_config',
        'background_image',
        'header_logo',
        'footer_image',
        'digital_seal',
        'signature_positions',
        'qr_position',
        'barcode_position',
        'css_styles',
        'status',
        'is_default',
    ];

    protected $casts = [
        'template_config' => 'array',
        'signature_positions' => 'array',
        'is_default' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_DRAFT = 'draft';

    // ===================== RELATIONSHIPS =====================

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'template_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // ===================== METHODS =====================

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_DRAFT => 'Draft',
        ];
    }

    public function setAsDefault(): void
    {
        self::where('certificate_type', $this->certificate_type)
            ->where('is_default', true)
            ->update(['is_default' => false]);
        
        $this->update(['is_default' => true]);
    }
}
