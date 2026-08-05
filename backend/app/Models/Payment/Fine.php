<?php

declare(strict_types=1);

namespace App\Models\Payment;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fine extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'fines';

    protected $fillable = [
        'uuid',
        'student_id',
        'invoice_id',
        'fine_type',
        'amount',
        'reason',
        'due_date',
        'paid_date',
        'paid_amount',
        'waived_amount',
        'status',
        'created_by',
        'waived_by',
        'waived_at',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'waived_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
        'waived_at' => 'datetime',
    ];

    // ===================== TYPES =====================
    public const TYPE_LATE_PAYMENT = 'late_payment';
    public const TYPE_LATE_REGISTRATION = 'late_registration';
    public const TYPE_LATE_EXAM_FEE = 'late_exam_fee';
    public const TYPE_DAMAGE = 'damage';
    public const TYPE_LIBRARY = 'library';
    public const TYPE_OTHER = 'other';

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_WAIVED = 'waived';
    public const STATUS_CANCELLED = 'cancelled';

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student\Student::class, 'student_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public static function types(): array
    {
        return [
            self::TYPE_LATE_PAYMENT => 'Late Payment',
            self::TYPE_LATE_REGISTRATION => 'Late Registration',
            self::TYPE_LATE_EXAM_FEE => 'Late Exam Fee',
            self::TYPE_DAMAGE => 'Damage',
            self::TYPE_LIBRARY => 'Library Fine',
            self::TYPE_OTHER => 'Other',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PAID => 'Paid',
            self::STATUS_WAIVED => 'Waived',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }
}
