<?php

declare(strict_types=1);

namespace App\Models\Facility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'maintenance_requests';

    const CATEGORY_ELECTRICAL = 'electrical';
    const CATEGORY_PLUMBING = 'plumbing';
    const CATEGORY_FURNITURE = 'furniture';
    const CATEGORY_CLEANING = 'cleaning';
    const CATEGORY_IT_SUPPORT = 'it_support';
    const CATEGORY_BUILDING = 'building';
    const CATEGORY_VEHICLE = 'vehicle';
    const CATEGORY_OTHER = 'other';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    const STATUS_PENDING = 'pending';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_VERIFIED = 'verified';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'uuid',
        'request_no',
        'reported_by',
        'category',
        'priority',
        'location',
        'description',
        'resolution',
        'status',
        'assigned_to',
        'assigned_at',
        'started_at',
        'completed_at',
        'verified_at',
        'verified_by',
        'cost',
        'remarks',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public static function generateRequestNo(): string
    {
        $prefix = 'MR';
        $year = date('Y');
        $month = date('m');
        $last = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $last ? ((int) substr($last->request_no, -5)) + 1 : 1;
        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    public static function categories(): array
    {
        return [
            self::CATEGORY_ELECTRICAL => 'Electrical',
            self::CATEGORY_PLUMBING => 'Plumbing',
            self::CATEGORY_FURNITURE => 'Furniture',
            self::CATEGORY_CLEANING => 'Cleaning',
            self::CATEGORY_IT_SUPPORT => 'IT Support',
            self::CATEGORY_BUILDING => 'Building',
            self::CATEGORY_VEHICLE => 'Vehicle',
            self::CATEGORY_OTHER => 'Other',
        ];
    }

    public static function priorities(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reported_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    public function assignTo(int $userId): void
    {
        $this->update([
            'status' => self::STATUS_ASSIGNED,
            'assigned_to' => $userId,
            'assigned_at' => now(),
        ]);
    }

    public function complete(string $resolution): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'resolution' => $resolution,
            'completed_at' => now(),
        ]);
    }

    public function verify(): void
    {
        $this->update([
            'status' => self::STATUS_VERIFIED,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);
    }
}
