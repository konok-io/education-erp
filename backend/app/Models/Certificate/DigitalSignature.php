<?php

declare(strict_types=1);

namespace App\Models\Certificate;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DigitalSignature extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'digital_signatures';

    protected $fillable = [
        'uuid',
        'signature_name',
        'signatory_name',
        'designation',
        'department',
        'signature_image',
        'signature_type',
        'digital_certificate',
        'valid_from',
        'valid_until',
        'metadata',
        'status',
        'is_active',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_EXPIRED = 'expired';

    // ===================== TYPES =====================
    public const TYPE_IMAGE = 'image';
    public const TYPE_DIGITAL = 'digital';
    public const TYPE_QR = 'qr';

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now());
            });
    }

    // ===================== METHODS =====================

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_EXPIRED => 'Expired',
        ];
    }

    public static function signatureTypes(): array
    {
        return [
            self::TYPE_IMAGE => 'Image Signature',
            self::TYPE_DIGITAL => 'Digital Certificate',
            self::TYPE_QR => 'QR Code Signature',
        ];
    }

    public function isValid(): bool
    {
        return $this->is_active && 
               $this->status === self::STATUS_ACTIVE &&
               (!$this->valid_until || $this->valid_until >= now());
    }
}
