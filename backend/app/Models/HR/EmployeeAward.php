<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAward extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'employee_awards';

    protected $fillable = [
        'uuid',
        'award_no',
        'employee_id',
        'award_type_id',
        'title',
        'award_date',
        'reason',
        'reward_amount',
        'reward_type',
        'certificate_number',
        'certificate_date',
        'certificate_file',
        'presented_by',
        'notes',
    ];

    protected $casts = [
        'award_date' => 'date',
        'certificate_date' => 'date',
        'reward_amount' => 'decimal:2',
    ];

    // ===================== REWARD TYPES =====================
    public const REWARD_CASH = 'cash';
    public const REWARD_CERTIFICATE = 'certificate';
    public const REWARD_TROPHY = 'trophy';
    public const REWARD_PLAQUE = 'plaque';
    public const REWARD_GIFT = 'gift';

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function awardType(): BelongsTo
    {
        return $this->belongsTo(AwardType::class, 'award_type_id');
    }

    public function presenter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'presented_by');
    }

    // ===================== METHODS =====================

    public static function generateAwardNo(): string
    {
        $prefix = 'AWR';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%05d', $prefix, $year, $count);
    }

    public static function rewardTypes(): array
    {
        return [
            self::REWARD_CASH => 'Cash',
            self::REWARD_CERTIFICATE => 'Certificate',
            self::REWARD_TROPHY => 'Trophy',
            self::REWARD_PLAQUE => 'Plaque',
            self::REWARD_GIFT => 'Gift',
        ];
    }
}
