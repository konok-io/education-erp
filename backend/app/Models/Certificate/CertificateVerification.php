<?php

declare(strict_types=1);

namespace App\Models\Certificate;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateVerification extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'certificate_verifications';

    protected $fillable = [
        'uuid',
        'certificate_number',
        'verification_token',
        'verifier_name',
        'verifier_email',
        'verifier_ip',
        'verification_method',
        'verified_at',
        'status',
        'remarks',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    public const STATUS_INVALID = 'invalid';

    // ===================== METHODS =====================
    public const METHOD_QR = 'qr';
    public const METHOD_NUMBER = 'number';
    public const METHOD_MANUAL = 'manual';

    public static function statuses(): array
    {
        return [
            self::STATUS_SUCCESS => 'Success',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_INVALID => 'Invalid',
        ];
    }

    public static function verificationMethods(): array
    {
        return [
            self::METHOD_QR => 'QR Code',
            self::METHOD_NUMBER => 'Certificate Number',
            self::METHOD_MANUAL => 'Manual',
        ];
    }

    public function markAsVerified(): void
    {
        $this->update([
            'status' => self::STATUS_SUCCESS,
            'verified_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'verified_at' => now(),
        ]);
    }
}
