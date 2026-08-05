<?php

declare(strict_types=1);

namespace App\Models\Payment;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'payments';

    protected $fillable = [
        'uuid',
        'payment_no',
        'receipt_no',
        'invoice_id',
        'student_id',
        'amount',
        'payment_type',
        'payment_method',
        'gateway_name',
        'transaction_id',
        'gateway_response',
        'payment_date',
        'collected_by',
        'collected_by_name',
        'status',
        'bank_name',
        'branch_name',
        'account_number',
        'check_number',
        'check_date',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'check_date' => 'date',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== PAYMENT METHODS =====================
    public const METHOD_CASH = 'cash';
    public const METHOD_BANK = 'bank';
    public const METHOD_CHEQUE = 'cheque';
    public const METHOD_BKASH = 'bkash';
    public const METHOD_NAGAD = 'nagad';
    public const METHOD_ROCKET = 'rocket';
    public const METHOD_SSLCOMMERZ = 'sslcommerz';
    public const METHOD_STRIPE = 'stripe';
    public const METHOD_PAYPAL = 'paypal';

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student\Student::class, 'student_id');
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'collected_by');
    }

    public static function generatePaymentNo(): string
    {
        $prefix = 'PAY';
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now()->toDateString())->count() + 1;
        return sprintf('%s-%s-%04d', $prefix, $date, $count);
    }

    public static function generateReceiptNo(): string
    {
        $prefix = 'RCP';
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
            self::METHOD_CASH => 'Cash',
            self::METHOD_BANK => 'Bank Transfer',
            self::METHOD_CHEQUE => 'Cheque',
            self::METHOD_BKASH => 'bKash',
            self::METHOD_NAGAD => 'Nagad',
            self::METHOD_ROCKET => 'Rocket',
            self::METHOD_SSLCOMMERZ => 'SSLCommerz',
            self::METHOD_STRIPE => 'Stripe',
            self::METHOD_PAYPAL => 'PayPal',
        ];
    }
}
