<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Models\Finance\Account;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'journal_entries';

    protected $fillable = [
        'uuid',
        'voucher_no',
        'voucher_type',
        'fiscal_year_id',
        'entry_date',
        'reference',
        'description',
        'total_amount',
        'status',
        'is_posted',
        'posted_at',
        'posted_by',
        'approved_by',
        'approved_at',
        'locked_at',
        'created_by',
        'remarks',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'total_amount' => 'decimal:2',
        'is_posted' => 'boolean',
        'posted_at' => 'datetime',
        'approved_at' => 'datetime',
        'locked_at' => 'datetime',
    ];

    // ===================== VOUCHER TYPES =====================
    public const VOUCHER_JOURNAL = 'journal';
    public const VOUCHER_PAYMENT = 'payment';
    public const VOUCHER_RECEIPT = 'receipt';
    public const VOUCHER_CONTRA = 'contra';
    public const VOUCHER_ADJUSTMENT = 'adjustment';
    public const VOUCHER_OPENING = 'opening';
    public const VOUCHER_CLOSING = 'closing';

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_POSTED = 'posted';
    public const STATUS_LOCKED = 'locked';

    // ===================== RELATIONSHIPS =====================

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(JournalEntryDetail::class, 'journal_entry_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'posted_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // ===================== SCOPES =====================

    public function scopePosted($query)
    {
        return $query->where('is_posted', true);
    }

    public function scopeByDate($query, $start, $end)
    {
        return $query->whereBetween('entry_date', [$start, $end]);
    }

    // ===================== METHODS =====================

    public static function generateVoucherNo(string $type): string
    {
        $prefix = strtoupper(substr($type, 0, 2));
        $year = now()->format('Y');
        $month = now()->format('m');
        $count = self::where('voucher_type', $type)->whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $count);
    }

    public static function voucherTypes(): array
    {
        return [
            self::VOUCHER_JOURNAL => 'Journal Voucher',
            self::VOUCHER_PAYMENT => 'Payment Voucher',
            self::VOUCHER_RECEIPT => 'Receipt Voucher',
            self::VOUCHER_CONTRA => 'Contra Voucher',
            self::VOUCHER_ADJUSTMENT => 'Adjustment Voucher',
            self::VOUCHER_OPENING => 'Opening Voucher',
            self::VOUCHER_CLOSING => 'Closing Voucher',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_VERIFIED => 'Verified',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_POSTED => 'Posted',
            self::STATUS_LOCKED => 'Locked',
        ];
    }

    public function getTotalDebit(): float
    {
        return (float) $this->details()->where('dr_cr', 'dr')->sum('amount');
    }

    public function getTotalCredit(): float
    {
        return (float) $this->details()->where('dr_cr', 'cr')->sum('amount');
    }

    public function isBalanced(): bool
    {
        return $this->getTotalDebit() === $this->getTotalCredit();
    }

    public function post(int $userId): void
    {
        $this->update([
            'is_posted' => true,
            'status' => self::STATUS_POSTED,
            'posted_at' => now(),
            'posted_by' => $userId,
        ]);
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }
}
