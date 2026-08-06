<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeatPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'seat_plans';

    protected $fillable = [
        'uuid',
        'plan_code',
        'exam_id',
        'center_id',
        'room',
        'floor',
        'exam_date',
        'start_time',
        'total_seats',
        'seats',
        'status',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime',
        'total_seats' => 'integer',
        'seats' => 'array',
    ];

    public static function generatePlanCode(): string
    {
        $prefix = 'SP';
        $year = date('Y');
        $last = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $sequence = $last ? ((int) substr($last->plan_code, -4)) + 1 : 1;
        return sprintf('%s-%s-%04d', $prefix, $year, $sequence);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(ExamCenter::class, 'center_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(SeatAssignment::class, 'seat_plan_id');
    }
}
