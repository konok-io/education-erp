<?php

declare(strict_types=1);

namespace App\Models\Examination;

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
        'building',
        'floor',
        'room_no',
        'capacity',
        'rows',
        'columns',
        'description',
        'status',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'rows' => 'integer',
        'columns' => 'integer',
        'is_active' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_RESERVED = 'reserved';

    // ===================== RELATIONSHIPS =====================

    public function seatPlans(): HasMany
    {
        return $this->hasMany(ExamSeatPlan::class, 'exam_hall_id');
    }

    public function invigilators(): HasMany
    {
        return $this->hasMany(ExamInvigilator::class, 'exam_hall_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)->where('is_active', true);
    }

    // ===================== METHODS =====================

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_RESERVED => 'Reserved',
        ];
    }

    public function getTotalSeatsAttribute(): int
    {
        return $this->rows * $this->columns;
    }

    public function getAvailableSeatsAttribute(): int
    {
        return $this->capacity;
    }
}
