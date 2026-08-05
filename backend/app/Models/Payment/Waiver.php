<?php

declare(strict_types=1);

namespace App\Models\Payment;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Waiver extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'waivers';

    protected $fillable = [
        'uuid',
        'invoice_id',
        'student_id',
        'waiver_type',
        'amount',
        'percentage',
        'reason',
        'approved_by',
        'approved_at',
        'status',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // ===================== TYPES =====================
    public const TYPE_EMPLOYEE = 'employee';
    public const TYPE_SIBLING = 'sibling';
    public const TYPE_SPECIAL = 'special';
    public const TYPE_SCHOLARSHIP = 'scholarship';
    public const TYPE_MANUAL = 'manual';

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student\Student::class, 'student_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public static function types(): array
    {
        return [
            self::TYPE_EMPLOYEE => 'Employee Child',
            self::TYPE_SIBLING => 'Sibling Discount',
            self::TYPE_SPECIAL => 'Special Waiver',
            self::TYPE_SCHOLARSHIP => 'Scholarship',
            self::TYPE_MANUAL => 'Manual Waiver',
        ];
    }
}
