<?php

declare(strict_types=1);

namespace App\Models\Admission;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdmissionPayment extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'admission_payments';

    protected $fillable = [
        'uuid',
        'application_id',
        'payment_no',
        'amount',
        'payment_type',
        'payment_method',
        'transaction_id',
        'bank_name',
        'branch_name',
        'account_number',
        'payment_date',
        'status',
        'verified_by',
        'verified_at',
        'refund_amount',
        'refund_date',
        'refund_reason',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'verified_at' => 'datetime',
        'refund_amount' => 'decimal:2',
        'refund_date' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== PAYMENT TYPES =====================
    public const TYPE_APPLICATION = 'application';
    public const TYPE_ADMISSION = 'admission';
    public const TYPE_LATE = 'late';
    public const TYPE_PROCESSING = 'processing';

    // ===================== METHODS =====================
    public const TYPE_BKASH = 'bkash';
    public const TYPE_NAGAD = 'nagad';
    public const TYPE_ROCKET = 'rocket';
    public const TYPE_SSLCOMMERZ = 'sslcommerz';
    public const TYPE_CASH = 'cash';
    public const TYPE_BANK = 'bank';

    public function application(): BelongsTo
    {
        return $this->belongsTo(AdmissionApplication::class, 'application_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    public static function generatePaymentNo(): string
    {
        $prefix = 'PAY';
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now()->toDateString())->count() + 1;
        return sprintf('%s-%s-%04d', $prefix, $date, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PAID => 'Paid',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_REFUNDED => 'Refunded',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function paymentMethods(): array
    {
        return [
            self::TYPE_BKASH => 'bKash',
            self::TYPE_NAGAD => 'Nagad',
            self::TYPE_ROCKET => 'Rocket',
            self::TYPE_SSLCOMMERZ => 'SSLCommerz',
            self::TYPE_CASH => 'Cash',
            self::TYPE_BANK => 'Bank',
        ];
    }

    public static function paymentTypes(): array
    {
        return [
            self::TYPE_APPLICATION => 'Application Fee',
            self::TYPE_ADMISSION => 'Admission Fee',
            self::TYPE_LATE => 'Late Fee',
            self::TYPE_PROCESSING => 'Processing Fee',
        ];
    }
}
