<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamCenter extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exam_centers';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'uuid',
        'center_code',
        'name',
        'name_bn',
        'building',
        'floor',
        'address',
        'capacity',
        'current_capacity',
        'status',
        'description',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'current_capacity' => 'integer',
    ];

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'center_id');
    }

    public function seatPlans(): HasMany
    {
        return $this->hasMany(SeatPlan::class, 'center_id');
    }
}
