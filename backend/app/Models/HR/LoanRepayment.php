<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanRepayment extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'loan_repayments';

    protected $fillable = [
        'uuid',
        'loan_id',
        'installment_no',
        'amount',
        'principal_amount',
        'interest_amount',
        'payment_date',
        'payment_month',
        'payment_year',
        'status',
        'processed_by',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'payment_date' => 'date',
        'payment_month' => 'integer',
        'payment_year' => 'integer',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_SKIPPED = 'skipped';
    public const STATUS_CANCELLED = 'cancelled';

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'processed_by');
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PAID => 'Paid',
            self::STATUS_SKIPPED => 'Skipped',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }
}
