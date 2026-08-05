<?php

declare(strict_types=1);

namespace App\Models\Transport;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'trips';

    protected $fillable = [
        'uuid',
        'trip_no',
        'vehicle_id',
        'driver_id',
        'route_id',
        'trip_type',
        'trip_date',
        'start_time',
        'end_time',
        'start_odometer',
        'end_odometer',
        'distance',
        'passenger_count',
        'status',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'trip_date' => 'date',
        'distance' => 'decimal:2',
        'passenger_count' => 'integer',
    ];

    // ===================== STATUS =====================
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_STARTED = 'started';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== TRIP TYPES =====================
    public const TYPE_REGULAR = 'regular';
    public const TYPE_MORNING = 'morning';
    public const TYPE_EVENING = 'evening';
    public const TYPE_SPECIAL = 'special';
    public const TYPE_EXAM = 'exam';
    public const TYPE_HOLIDAY = 'holiday';

    // ===================== RELATIONSHIPS =====================

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // ===================== SCOPES =====================

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeToday($query)
    {
        return $query->where('trip_date', now()->toDateString());
    }

    // ===================== METHODS =====================

    public static function generateTripNo(): string
    {
        $prefix = 'TRIP';
        $date = now()->format('Ymd');
        $count = self::whereDate('trip_date', now()->toDateString())->count() + 1;
        return sprintf('%s-%s-%02d', $prefix, $date, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_STARTED => 'Started',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function tripTypes(): array
    {
        return [
            self::TYPE_REGULAR => 'Regular',
            self::TYPE_MORNING => 'Morning',
            self::TYPE_EVENING => 'Evening',
            self::TYPE_SPECIAL => 'Special',
            self::TYPE_EXAM => 'Exam',
            self::TYPE_HOLIDAY => 'Holiday',
        ];
    }

    public function start(): void
    {
        $this->update([
            'status' => self::STATUS_STARTED,
            'start_time' => now()->format('H:i:s'),
        ]);
    }

    public function complete(string $endOdometer, float $distance = null, int $passengers = 0): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'end_time' => now()->format('H:i:s'),
            'end_odometer' => $endOdometer,
            'distance' => $distance,
            'passenger_count' => $passengers,
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }
}
