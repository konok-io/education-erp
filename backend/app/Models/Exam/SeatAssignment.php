<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeatAssignment extends Model
{
    use HasFactory;

    protected $table = 'seat_assignments';

    protected $fillable = [
        'uuid',
        'seat_plan_id',
        'student_id',
        'student_name',
        'roll_number',
        'seat_number',
        'row_number',
        'column_number',
        'is_present',
        'remarks',
    ];

    protected $casts = [
        'is_present' => 'boolean',
    ];

    public function seatPlan(): BelongsTo
    {
        return $this->belongsTo(SeatPlan::class, 'seat_plan_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }
}
