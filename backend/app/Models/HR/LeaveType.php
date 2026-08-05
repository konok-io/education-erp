<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'leave_types';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'code',
        'short_code',
        'leave_days',
        'is_paid',
        'is_encashable',
        'is_carry_forward',
        'max_consecutive_days',
        'max_carry_forward_days',
        'requires_approval',
        'is_active',
        'description',
    ];

    protected $casts = [
        'leave_days' => 'integer',
        'is_paid' => 'boolean',
        'is_encashable' => 'boolean',
        'is_carry_forward' => 'boolean',
        'max_consecutive_days' => 'integer',
        'max_carry_forward_days' => 'integer',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ===================== DEFAULT TYPES =====================
    public const TYPE_CASUAL = 'casual';
    public const TYPE_SICK = 'sick';
    public const TYPE_ANNUAL = 'annual';
    public const TYPE_MATERNITY = 'maternity';
    public const TYPE_PATERNITY = 'paternity';
    public const TYPE_STUDY = 'study';
    public const TYPE_WITHOUT_PAY = 'without_pay';
    public const TYPE_SPECIAL = 'special';

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class, 'leave_type_id');
    }

    public static function defaultTypes(): array
    {
        return [
            [
                'name' => 'Casual Leave',
                'code' => 'CL',
                'leave_days' => 10,
                'is_paid' => true,
                'is_encashable' => false,
                'is_carry_forward' => false,
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SL',
                'leave_days' => 10,
                'is_paid' => true,
                'is_encashable' => false,
                'is_carry_forward' => false,
            ],
            [
                'name' => 'Annual Leave',
                'code' => 'AL',
                'leave_days' => 20,
                'is_paid' => true,
                'is_encashable' => true,
                'is_carry_forward' => true,
                'max_carry_forward_days' => 10,
            ],
            [
                'name' => 'Maternity Leave',
                'code' => 'ML',
                'leave_days' => 90,
                'is_paid' => true,
                'is_encashable' => false,
                'is_carry_forward' => false,
            ],
            [
                'name' => 'Paternity Leave',
                'code' => 'PL',
                'leave_days' => 10,
                'is_paid' => true,
                'is_encashable' => false,
                'is_carry_forward' => false,
            ],
            [
                'name' => 'Study Leave',
                'code' => 'STL',
                'leave_days' => 15,
                'is_paid' => true,
                'is_encashable' => false,
                'is_carry_forward' => false,
            ],
            [
                'name' => 'Leave Without Pay',
                'code' => 'LWP',
                'leave_days' => 30,
                'is_paid' => false,
                'is_encashable' => false,
                'is_carry_forward' => false,
            ],
        ];
    }
}
