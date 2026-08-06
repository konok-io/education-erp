<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamInvigilator extends Model
{
    use HasFactory;

    protected $table = 'exam_invigilators';

    const STATUS_ASSIGNED = 'assigned';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_ABSENT = 'absent';

    const SHIFT_MORNING = 'morning';
    const SHIFT_EVENING = 'evening';

    protected $fillable = [
        'uuid',
        'exam_id',
        'user_id',
        'name',
        'center_id',
        'room',
        'shift',
        'reporting_time',
        'status',
        'remarks',
    ];

    protected $casts = [
        'reporting_time' => 'datetime',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(ExamCenter::class, 'center_id');
    }
}
