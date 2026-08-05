<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostelComplaint extends Model
{
    use HasUuid;

    protected $table = 'hostel_complaints';

    protected $fillable = [
        'uuid',
        'complaint_no',
        'hostel_id',
        'room_id',
        'student_id',
        'complaint_type',
        'priority',
        'description',
        'response',
        'assigned_to',
        'response_date',
        'status',
        'reported_by',
        'resolution',
        'resolved_date',
        'feedback',
    ];

    protected $casts = [
        'response_date' => 'date',
        'resolved_date' => 'date',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    // ===================== PRIORITIES =====================
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    // ===================== TYPES =====================
    public const TYPE_ELECTRICITY = 'electricity';
    public const TYPE_WATER = 'water';
    public const TYPE_FURNITURE = 'furniture';
    public const TYPE_INTERNET = 'internet';
    public const TYPE_CLEANING = 'cleaning';
    public const TYPE_SECURITY = 'security';
    public const TYPE_NOISE = 'noise';
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

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reported_by');
    }

    // ===================== SCOPES =====================

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeUnresolved($query)
    {
        return $query->whereNotIn('status', [self::STATUS_RESOLVED, self::STATUS_CLOSED]);
    }

    // ===================== METHODS =====================

    public static function generateComplaintNo(): string
    {
        $prefix = 'COMP';
        $year = now()->format('Y');
        $month = now()->format('m');
        $count = self::whereMonth('created_at', now()->month)->count() + 1;
        return sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_CLOSED => 'Closed',
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

    public static function complaintTypes(): array
    {
        return [
            self::TYPE_ELECTRICITY => 'Electricity',
            self::TYPE_WATER => 'Water',
            self::TYPE_FURNITURE => 'Furniture',
            self::TYPE_INTERNET => 'Internet',
            self::TYPE_CLEANING => 'Cleaning',
            self::TYPE_SECURITY => 'Security',
            self::TYPE_NOISE => 'Noise',
            self::TYPE_OTHER => 'Other',
        ];
    }

    public function respond(string $response, string $assignedTo): void
    {
        $this->update([
            'response' => $response,
            'assigned_to' => $assignedTo,
            'response_date' => now(),
            'status' => self::STATUS_IN_PROGRESS,
        ]);
    }

    public function resolve(string $resolution): void
    {
        $this->update([
            'resolution' => $resolution,
            'resolved_date' => now(),
            'status' => self::STATUS_RESOLVED,
        ]);
    }

    public function close(): void
    {
        $this->update(['status' => self::STATUS_CLOSED]);
    }
}
