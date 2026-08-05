<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryFine extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'library_fines';

    protected $fillable = [
        'uuid',
        'fine_no',
        'member_id',
        'issue_id',
        'fine_type',
        'reason',
        'amount',
        'paid_amount',
        'waived_amount',
        'fine_date',
        'due_date',
        'paid_date',
        'status',
        'payment_method',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'fine_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'waived_amount' => 'decimal:2',
    ];

    // ===================== FINE TYPES =====================
    public const TYPE_LATE_RETURN = 'late_return';
    public const TYPE_LOST_BOOK = 'lost_book';
    public const TYPE_DAMAGED_BOOK = 'damaged_book';
    public const TYPE_MEMBERSHIP_VIOLATION = 'membership_violation';

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_PAID = 'paid';
    public const STATUS_WAIVED = 'waived';

    // ===================== RELATIONSHIPS =====================

    public function member(): BelongsTo
    {
        return $this->belongsTo(LibraryMember::class, 'member_id');
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(BookIssue::class, 'issue_id');
    }

    // ===================== SCOPES =====================

    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PARTIAL]);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('fine_type', $type);
    }

    // ===================== METHODS =====================

    public static function generateFineNo(): string
    {
        $prefix = 'FINE';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%04d', $prefix, $year, $count);
    }

    public static function fineTypes(): array
    {
        return [
            self::TYPE_LATE_RETURN => 'Late Return',
            self::TYPE_LOST_BOOK => 'Lost Book',
            self::TYPE_DAMAGED_BOOK => 'Damaged Book',
            self::TYPE_MEMBERSHIP_VIOLATION => 'Membership Violation',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PARTIAL => 'Partial Payment',
            self::STATUS_PAID => 'Paid',
            self::STATUS_WAIVED => 'Waived',
        ];
    }

    public function getRemainingAmount(): float
    {
        return (float) ($this->amount - $this->paid_amount - $this->waived_amount);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isPending(): bool
    {
        return $this->getRemainingAmount() > 0;
    }

    public function pay(float $amount, ?string $method = null): void
    {
        $newPaidAmount = $this->paid_amount + $amount;
        
        if ($newPaidAmount >= $this->amount) {
            $this->update([
                'paid_amount' => $this->amount,
                'status' => self::STATUS_PAID,
                'paid_date' => now(),
                'payment_method' => $method,
            ]);
        } else {
            $this->update([
                'paid_amount' => $newPaidAmount,
                'status' => self::STATUS_PARTIAL,
                'payment_method' => $method,
            ]);
        }
    }

    public function waive(float $amount): void
    {
        $newWaivedAmount = $this->waived_amount + $amount;
        
        if ($newWaivedAmount >= $this->amount) {
            $this->update([
                'waived_amount' => $this->amount,
                'status' => self::STATUS_WAIVED,
            ]);
        } else {
            $this->update([
                'waived_amount' => $newWaivedAmount,
            ]);
        }
    }
}
