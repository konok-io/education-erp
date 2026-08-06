<?php

declare(strict_types=1);

namespace App\Models\Document;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentVerification extends Model
{
    use HasFactory;

    protected $table = 'document_verifications';

    const STATUS_PENDING = 'pending';
    const STATUS_VERIFIED = 'verified';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';

    const TYPE_SELF = 'self';
    const TYPE_THIRD_PARTY = 'third_party';
    const TYPE_EMPLOYER = 'employer';
    const TYPE_INSTITUTION = 'institution';

    const DOC_CERTIFICATE = 'certificate';
    const DOC_TRANSCRIPT = 'transcript';
    const DOC_MARKSHET = 'marksheet';
    const DOC_NID = 'nid';
    const DOC_PASSPORT = 'passport';
    const DOC_OTHER = 'other';

    protected $fillable = [
        'uuid',
        'verification_no',
        'certificate_id',
        'student_id',
        'applicant_name',
        'applicant_email',
        'applicant_phone',
        'document_type',
        'document_name',
        'document_number',
        'document_path',
        'issue_date',
        'verification_type',
        'verifier_name',
        'verifier_email',
        'verifier_organization',
        'status',
        'verification_details',
        'verified_at',
        'verified_by',
        'qr_code',
        'verification_link',
        'remarks',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'verified_at' => 'datetime',
    ];

    public static function generateVerificationNo(): string
    {
        $prefix = 'VRF';
        $year = date('Y');
        $last = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $sequence = $last ? ((int) substr($last->verification_no, -5)) + 1 : 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    public static function generateVerificationLink(string $uuid): string
    {
        return url("/verify/{$uuid}");
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Certificate\Certificate::class, 'certificate_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student\Student::class, 'student_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    public function markAsVerified(int $verifiedBy, ?string $details = null): void
    {
        $this->update([
            'status' => self::STATUS_VERIFIED,
            'verified_at' => now(),
            'verified_by' => $verifiedBy,
            'verification_details' => $details,
        ]);
    }

    public function markAsRejected(int $verifiedBy, ?string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'verified_at' => now(),
            'verified_by' => $verifiedBy,
            'verification_details' => $reason,
        ]);
    }
}
