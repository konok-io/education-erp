<?php

declare(strict_types=1);

namespace App\Models\Examination;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamCommittee extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'exam_committees';

    protected $fillable = [
        'uuid',
        'committee_name',
        'committee_code',
        'exam_session_id',
        'chairman_id',
        'controller_id',
        'coordinator_id',
        'responsibilities',
        'description',
        'effective_from',
        'effective_to',
        'status',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    // ===================== RELATIONSHIPS =====================

    public function session(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function chairman(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'chairman_id');
    }

    public function controller(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'controller_id');
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'coordinator_id');
    }

    // ===================== METHODS =====================

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }
}
