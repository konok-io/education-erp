<?php

declare(strict_types=1);

namespace App\Models\Routine;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeSlot extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'time_slots';

    protected $fillable = [
        'uuid',
        'slot_name',
        'start_time',
        'end_time',
        'duration_minutes',
        'break_before',
        'break_after',
        'slot_order',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'duration_minutes' => 'integer',
        'break_before' => 'integer',
        'break_after' => 'integer',
        'slot_order' => 'integer',
    ];

    public function periods(): HasMany
    {
        return $this->hasMany(Period::class, 'time_slot_id');
    }

    public function routines(): HasMany
    {
        return $this->hasMany(Routine::class, 'time_slot_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('slot_order');
    }
}
