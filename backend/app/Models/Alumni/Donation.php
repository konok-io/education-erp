<?php

declare(strict_types=1);

namespace App\Models\Alumni;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'donations';

    protected $fillable = [
        'uuid',
        'donation_number',
        'campaign_id',
        'alumni_profile_id',
        'donor_id',
        'donor_name',
        'donor_email',
        'donor_phone',
        'donor_type',
        'company_name',
        'amount',
        'currency',
        'payment_method',
        'transaction_id',
        'payment_status',
        'donation_type',
        'fund_category',
        'notes',
        'is_anonymous',
        'is_tax_deductible',
        'receipt_path',
        'received_by',
        'donated_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'is_tax_deductible' => 'boolean',
        'donated_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_COMPLETED = 'completed';
    public const PAYMENT_FAILED = 'failed';
    public const PAYMENT_REFUNDED = 'refunded';

    // ===================== TYPES =====================
    public const TYPE_ONE_TIME = 'one_time';
    public const TYPE_MONTHLY = 'monthly';
    public const TYPE_ANNUAL = 'annual';

    // ===================== RELATIONSHIPS =====================

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(FundraisingCampaign::class, 'campaign_id');
    }

    public function alumniProfile(): BelongsTo
    {
        return $this->belongsTo(AlumniProfile::class, 'alumni_profile_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'received_by');
    }

    // ===================== METHODS =====================

    public static function generateDonationNumber(): string
    {
        $prefix = 'DON';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public static function paymentStatuses(): array
    {
        return [
            self::PAYMENT_PENDING => 'Pending',
            self::PAYMENT_COMPLETED => 'Completed',
            self::PAYMENT_FAILED => 'Failed',
            self::PAYMENT_REFUNDED => 'Refunded',
        ];
    }

    public static function donationTypes(): array
    {
        return [
            self::TYPE_ONE_TIME => 'One Time',
            self::TYPE_MONTHLY => 'Monthly',
            self::TYPE_ANNUAL => 'Annual',
        ];
    }

    public function markAsPaid(): void
    {
        $this->update([
            'payment_status' => self::PAYMENT_COMPLETED,
            'donated_at' => now(),
        ]);
    }
}
