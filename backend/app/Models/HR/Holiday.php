<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'holidays';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'holiday_date',
        'holiday_type',
        'is_repeating',
        'is_active',
        'description',
    ];

    protected $casts = [
        'holiday_date' => 'date',
        'is_repeating' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ===================== TYPES =====================
    public const TYPE_WEEKLY = 'weekly';
    public const TYPE_NATIONAL = 'national';
    public const TYPE_RELIGIOUS = 'religious';
    public const TYPE_INSTITUTION = 'institution';
    public const TYPE_EMERGENCY = 'emergency';

    public static function holidayTypes(): array
    {
        return [
            self::TYPE_WEEKLY => 'Weekly Holiday',
            self::TYPE_NATIONAL => 'National Holiday',
            self::TYPE_RELIGIOUS => 'Religious Holiday',
            self::TYPE_INSTITUTION => 'Institution Holiday',
            self::TYPE_EMERGENCY => 'Emergency Holiday',
        ];
    }

    public static function getWeekDays(): array
    {
        return ['Friday', 'Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
    }

    public static function getForYear(int $year): \Illuminate\Database\Eloquent\Collection
    {
        return self::whereYear('holiday_date', $year)
            ->orWhere('is_repeating', true)
            ->get();
    }

    public static function isHoliday(\Carbon\Carbon $date): bool
    {
        return self::where('holiday_date', $date->format('Y-m-d'))
            ->orWhere(function ($q) use ($date) {
                $q->where('is_repeating', true)
                    ->whereRaw('DAYOFYEAR(holiday_date) = ?', [$date->dayOfYear]);
            })
            ->exists();
    }
}
