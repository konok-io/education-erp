<?php

declare(strict_types=1);

namespace App\Models\Admission;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdmissionDocument extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'admission_documents';

    protected $fillable = [
        'uuid',
        'application_id',
        'document_type',
        'document_name',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'is_verified',
        'verified_by',
        'verified_at',
        'rejection_reason',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public const TYPE_PHOTO = 'photo';
    public const TYPE_SIGNATURE = 'signature';
    public const TYPE_SSC_CERTIFICATE = 'ssc_certificate';
    public const TYPE_SSC_MARKSHEET = 'ssc_marksheet';
    public const TYPE_HSC_CERTIFICATE = 'hsc_certificate';
    public const TYPE_HSC_MARKSHEET = 'hsc_marksheet';
    public const TYPE_BIRTH_CERTIFICATE = 'birth_certificate';
    public const TYPE_NID = 'nid';
    public const TYPE_PASSPORT = 'passport';
    public const TYPE_CHARACTER_CERTIFICATE = 'character_certificate';
    public const TYPE_TRANSFER_CERTIFICATE = 'tc';
    public const TYPE_QUOTA_CERTIFICATE = 'quota_certificate';
    public const TYPE_OTHER = 'other';

    public function application(): BelongsTo
    {
        return $this->belongsTo(AdmissionApplication::class, 'application_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    public static function documentTypes(): array
    {
        return [
            self::TYPE_PHOTO => 'Applicant Photo',
            self::TYPE_SIGNATURE => 'Signature',
            self::TYPE_SSC_CERTIFICATE => 'SSC Certificate',
            self::TYPE_SSC_MARKSHEET => 'SSC Marksheet',
            self::TYPE_HSC_CERTIFICATE => 'HSC Certificate',
            self::TYPE_HSC_MARKSHEET => 'HSC Marksheet',
            self::TYPE_BIRTH_CERTIFICATE => 'Birth Certificate',
            self::TYPE_NID => 'National ID',
            self::TYPE_PASSPORT => 'Passport',
            self::TYPE_CHARACTER_CERTIFICATE => 'Character Certificate',
            self::TYPE_TRANSFER_CERTIFICATE => 'Transfer Certificate',
            self::TYPE_QUOTA_CERTIFICATE => 'Quota Certificate',
            self::TYPE_OTHER => 'Other',
        ];
    }
}
