<?php

declare(strict_types=1);

namespace App\Models\Routine;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Period extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'periods';

    protected $fillable = [
        'uuid',
        'period_name',
        'period_number',
        'time_slot_id',
        'duration_minutes',
        'is_break',
        'break_type',
        'status',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'period_number' => 'integer',
        'is_break' => 'boolean',
    ];

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class, 'time_slot_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('period_number');
    }
}
