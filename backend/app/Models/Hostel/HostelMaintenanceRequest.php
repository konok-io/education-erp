<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostelMaintenanceRequest extends Model
{
    use HasUuid;

    protected $table = 'hostel_maintenance_requests';

    protected $fillable = [
        'uuid',
        'request_no',
        'hostel_id',
        'room_id',
        'student_id',
        'request_type',
        'priority',
        'description',
        'status',
        'estimated_cost',
        'actual_cost',
        'vendor',
        'scheduled_date',
        'completed_date',
        'work_done',
        'created_by',
        'remarks',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'scheduled_date' => 'date',
        'completed_date' => 'date',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== PRIORITIES =====================
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    // ===================== TYPES =====================
    public const TYPE_ELECTRICAL = 'electrical';
    public const TYPE_PLUMBING = 'plumbing';
    public const TYPE_PAINITING = 'painting';
    public const TYPE_FURNITURE = 'furniture';
    public const TYPE_CLEANING = 'cleaning';
    public const TYPE_INTERNET = 'internet';
    public const TYPE_AC = 'ac';
    public const TYPE_OTHER = 'other';

    // ===================== RELATIONSHIPS =====================

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // ===================== SCOPES =====================

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeDue($query)
    {
        return $query->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED])
            ->where('scheduled_date', '<=', now());
    }

    // ===================== METHODS =====================

    public static function generateRequestNo(): string
    {
        $prefix = 'MNT';
        $year = now()->format('Y');
        $month = now()->format('m');
        $count = self::whereMonth('created_at', now()->month)->count() + 1;
        return sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function priorities(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_NORMAL => 'Normal',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }

    public static function requestTypes(): array
    {
        return [
            self::TYPE_ELECTRICAL => 'Electrical',
            self::TYPE_PLUMBING => 'Plumbing',
            self::TYPE_PAINITING => 'Painting',
            self::TYPE_FURNITURE => 'Furniture Repair',
            self::TYPE_CLEANING => 'Cleaning',
            self::TYPE_INTERNET => 'Internet',
            self::TYPE_AC => 'Air Conditioner',
            self::TYPE_OTHER => 'Other',
        ];
    }

    public function approve(): void
    {
        $this->update(['status' => self::STATUS_APPROVED]);
    }

    public function start(): void
    {
        $this->update(['status' => self::STATUS_IN_PROGRESS]);
    }

    public function complete(string $workDone, float $cost = null): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'work_done' => $workDone,
            'actual_cost' => $cost ?? $this->actual_cost,
            'completed_date' => now(),
        ]);
    }
}
