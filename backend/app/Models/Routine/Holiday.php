<?php

declare(strict_types=1);

namespace App\Models\Routine;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'holidays';

    protected $fillable = [
        'uuid',
        'title',
        'title_bn',
        'description',
        'holiday_type',
        'date',
        'end_date',
        'is_recurring',
        'recurring_year',
        'color',
        'is_published',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'end_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    // ===================== HOLIDAY TYPES =====================
    public const TYPE_NATIONAL = 'national';
    public const TYPE_WEEKLY = 'weekly';
    public const TYPE_RELIGIOUS = 'religious';
    public const TYPE_SPECIAL = 'special';
    public const TYPE_EMERGENCY = 'emergency';

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public static function holidayTypes(): array
    {
        return [
            self::TYPE_NATIONAL => 'National Holiday',
            self::TYPE_WEEKLY => 'Weekly Holiday',
            self::TYPE_RELIGIOUS => 'Religious Holiday',
            self::TYPE_SPECIAL => 'Special Holiday',
            self::TYPE_EMERGENCY => 'Emergency Holiday',
        ];
    }

    public function isRecurringOnYear(int $year): bool
    {
        if ($this->is_recurring) {
            return $this->recurring_year === $year || $this->recurring_year === null;
        }

        return $this->date->year === $year;
    }
}
