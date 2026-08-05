<?php

declare(strict_types=1);

namespace App\Models\Employee;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'shifts';

    protected $fillable = [
        'uuid',
        'shift_name',
        'shift_code',
        'start_time',
        'end_time',
        'late_after_minutes',
        'early_leave_before_minutes',
        'working_hours',
        'break_time_minutes',
        'description',
        'status',
    ];

    protected $casts = [
        'working_hours' => 'decimal:2',
        'late_after_minutes' => 'integer',
        'early_leave_before_minutes' => 'integer',
        'break_time_minutes' => 'integer',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'shift_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getWorkingHoursInMinutesAttribute(): int
    {
        $totalMinutes = $this->working_hours * 60;
        return $totalMinutes - $this->break_time_minutes;
    }
}
