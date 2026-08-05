<?php

declare(strict_types=1);

namespace App\Models\Alumni;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventRegistration extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'event_registrations';

    protected $fillable = [
        'uuid',
        'event_id',
        'alumni_profile_id',
        'student_id',
        'registrant_name',
        'email',
        'phone',
        'ticket_type',
        'amount_paid',
        'payment_status',
        'transaction_id',
        'attended',
        'certificate_generated',
        'certificate_number',
        'feedback',
        'status',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'attended' => 'boolean',
        'certificate_generated' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_REGISTERED = 'registered';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_NO_SHOW = 'no_show';

    // ===================== PAYMENT STATUS =====================
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_COMPLETED = 'completed';
    public const PAYMENT_REFUNDED = 'refunded';

    // ===================== RELATIONSHIPS =====================

    public function event(): BelongsTo
    {
        return $this->belongsTo(AlumniEvent::class, 'event_id');
    }

    public function alumniProfile(): BelongsTo
    {
        return $this->belongsTo(AlumniProfile::class, 'alumni_profile_id');
    }

    // ===================== METHODS =====================

    public static function generateCertificateNumber(): string
    {
        return 'CERT-' . strtoupper(substr(md5(uniqid()), 0, 10));
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_REGISTERED => 'Registered',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_NO_SHOW => 'No Show',
        ];
    }

    public static function paymentStatuses(): array
    {
        return [
            self::PAYMENT_PENDING => 'Pending',
            self::PAYMENT_COMPLETED => 'Completed',
            self::PAYMENT_REFUNDED => 'Refunded',
        ];
    }

    public function markAttended(): void
    {
        $this->update(['attended' => true]);
    }

    public function generateCertificate(): void
    {
        $this->update([
            'certificate_generated' => true,
            'certificate_number' => self::generateCertificateNumber(),
        ]);
    }
}
