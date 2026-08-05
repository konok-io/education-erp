<?php

declare(strict_types=1);

namespace App\Models\Certificate;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DuplicateCertificateRequest extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'duplicate_certificate_requests';

    protected $fillable = [
        'uuid',
        'request_number',
        'certificate_type',
        'student_id',
        'student_name',
        'student_roll',
        'registration_no',
        'father_name',
        'phone',
        'email',
        'reason',
        'description',
        'police_clearance',
        'newspaper_ad',
        'fee_amount',
        'payment_status',
        'status',
        'admin_remarks',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ISSUED = 'issued';

    // ===================== PAYMENT STATUS =====================
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_EXEMPTED = 'exempted';

    // ===================== RELATIONSHIPS =====================

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }

    // ===================== SCOPES =====================

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // ===================== METHODS =====================

    public static function generateRequestNumber(): string
    {
        $prefix = 'DCR';
        $year = now()->format('Y');
        $month = now()->format('m');
        $count = self::whereMonth('created_at', now()->month)->count() + 1;
        return sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_VERIFIED => 'Verified',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_ISSUED => 'Issued',
        ];
    }

    public static function paymentStatuses(): array
    {
        return [
            self::PAYMENT_PENDING => 'Pending',
            self::PAYMENT_PAID => 'Paid',
            self::PAYMENT_EXEMPTED => 'Exempted',
        ];
    }

    public function verify(): void
    {
        $this->update(['status' => self::STATUS_VERIFIED]);
    }

    public function approve(): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
    }

    public function markAsIssued(): void
    {
        $this->update(['status' => self::STATUS_ISSUED]);
    }
}
