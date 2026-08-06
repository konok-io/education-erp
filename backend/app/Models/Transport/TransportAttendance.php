<?php

declare(strict_types=1);

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportAttendance extends Model
{
    use HasFactory;

    protected $table = 'transport_attendances';

    const STATUS_ON_TIME = 'on_time';
    const STATUS_LATE = 'late';
    const STATUS_ABSENT = 'absent';
    const STATUS_NOT_AVAILABLE = 'not_available';

    protected $fillable = [
        'uuid',
        'student_id',
        'route_id',
        'vehicle_id',
        'date',
        'trip_type',
        'pickup_status',
        'drop_status',
        'pickup_time',
        'drop_time',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'pickup_time' => 'datetime',
        'drop_time' => 'datetime',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(TransportVehicle::class, 'vehicle_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }
}
