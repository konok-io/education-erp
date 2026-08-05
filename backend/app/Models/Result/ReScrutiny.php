<?php

declare(strict_types=1);

namespace App\Models\Result;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReScrutiny extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 're_scrutinies';

    protected $fillable = [
        'uuid',
        'application_no',
        'result_detail_id',
        'student_id',
        'exam_id',
        'subject_id',
        'reason',
        'fee_amount',
        'is_paid',
        'paid_at',
        'status',
        'reviewed_by',
        'reviewed_at',
        'old_marks',
        'new_marks',
        'change_reason',
        'notes',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'old_marks' => 'decimal:2',
        'new_marks' => 'decimal:2',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REJECTED = 'rejected';

    public function resultDetail(): BelongsTo
    {
        return $this->belongsTo(ResultDetail::class, 'result_detail_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student\Student::class, 'student_id');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Subject::class, 'subject_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }

    public static function generateApplicationNo(): string
    {
        $prefix = 'RS';
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now()->toDateString())->count() + 1;
        return sprintf('%s-%s-%04d', $prefix, $date, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PAID => 'Paid',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }
}
