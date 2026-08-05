<?php

declare(strict_types=1);

namespace App\Models\Result;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamHall extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'exam_halls';

    protected $fillable = [
        'uuid',
        'hall_name',
        'hall_code',
        'room_number',
        'building',
        'floor',
        'capacity',
        'current_capacity',
        'status',
        'description',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'current_capacity' => 'integer',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(ExamSchedule::class, 'hall_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
