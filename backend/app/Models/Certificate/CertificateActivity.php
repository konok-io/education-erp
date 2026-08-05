<?php

declare(strict_types=1);

namespace App\Models\Certificate;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateActivity extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'certificate_activities';

    protected $fillable = [
        'uuid',
        'user_id',
        'activity_type',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // ===================== ACTIVITY TYPES =====================
    public const ACTIVITY_CERTIFICATE_CREATED = 'certificate_created';
    public const ACTIVITY_CERTIFICATE_APPROVED = 'certificate_approved';
    public const ACTIVITY_CERTIFICATE_ISSUED = 'certificate_issued';
    public const ACTIVITY_CERTIFICATE_REJECTED = 'certificate_rejected';
    public const ACTIVITY_CERTIFICATE_REVOKED = 'certificate_revoked';
    public const ACTIVITY_TRANSCRIPT_GENERATED = 'transcript_generated';
    public const ACTIVITY_MARKESHEET_GENERATED = 'marksheet_generated';
    public const ACTIVITY_QR_VERIFIED = 'qr_verified';
    public const ACTIVITY_DUPLICATE_REQUESTED = 'duplicate_requested';
    public const ACTIVITY_DUPLICATE_APPROVED = 'duplicate_approved';
    public const ACTIVITY_TEMPLATE_UPDATED = 'template_updated';
    public const ACTIVITY_DOCUMENT_ARCHIVED = 'document_archived';

    // ===================== RELATIONSHIPS =====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // ===================== METHODS =====================

    public static function activityTypes(): array
    {
        return [
            self::ACTIVITY_CERTIFICATE_CREATED => 'Certificate Created',
            self::ACTIVITY_CERTIFICATE_APPROVED => 'Certificate Approved',
            self::ACTIVITY_CERTIFICATE_ISSUED => 'Certificate Issued',
            self::ACTIVITY_CERTIFICATE_REJECTED => 'Certificate Rejected',
            self::ACTIVITY_CERTIFICATE_REVOKED => 'Certificate Revoked',
            self::ACTIVITY_TRANSCRIPT_GENERATED => 'Transcript Generated',
            self::ACTIVITY_MARKESHEET_GENERATED => 'Marksheet Generated',
            self::ACTIVITY_QR_VERIFIED => 'QR Verified',
            self::ACTIVITY_DUPLICATE_REQUESTED => 'Duplicate Requested',
            self::ACTIVITY_DUPLICATE_APPROVED => 'Duplicate Approved',
            self::ACTIVITY_TEMPLATE_UPDATED => 'Template Updated',
            self::ACTIVITY_DOCUMENT_ARCHIVED => 'Document Archived',
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
