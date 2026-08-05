<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PFWithdrawal extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'pf_withdrawals';

    protected $fillable = [
        'uuid',
        'pf_id',
        'withdrawal_no',
        'amount',
        'withdrawal_type',
        'purpose',
        'status',
        'approved_by',
        'approved_at',
        'processed_at',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    // ===================== TYPES =====================
    public const TYPE_FINAL = 'final';
    public const TYPE_PARTIAL = 'partial';
    public const TYPE_EMERGENCY = 'emergency';

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_REJECTED = 'rejected';

    // ===================== RELATIONSHIPS =====================

    public function providentFund(): BelongsTo
    {
        return $this->belongsTo(ProvidentFund::class, 'pf_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // ===================== METHODS =====================

    public static function generateWithdrawalNo(): string
    {
        $prefix = 'WDL';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%04d', $prefix, $year, $count);
    }

    public static function withdrawalTypes(): array
    {
        return [
            self::TYPE_FINAL => 'Final Settlement',
            self::TYPE_PARTIAL => 'Partial Withdrawal',
            self::TYPE_EMERGENCY => 'Emergency Withdrawal',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PROCESSED => 'Processed',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }
}
