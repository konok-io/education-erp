<?php

declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_tickets';

    const CATEGORY_ADMISSION = 'admission';
    const CATEGORY_ACCOUNTS = 'accounts';
    const CATEGORY_RESULT = 'result';
    const CATEGORY_ATTENDANCE = 'attendance';
    const CATEGORY_ROUTINE = 'routine';
    const CATEGORY_LIBRARY = 'library';
    const CATEGORY_HOSTEL = 'hostel';
    const CATEGORY_TRANSPORT = 'transport';
    const CATEGORY_TECHNICAL = 'technical';
    const CATEGORY_GENERAL = 'general';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';
    const PRIORITY_CRITICAL = 'critical';

    const STATUS_OPEN = 'open';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_WAITING = 'waiting';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'uuid',
        'ticket_no',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'contact_id',
        'created_by',
        'assigned_to',
        'department_id',
        'cc',
        'attachments',
        'tags',
        'parent_ticket_id',
        'related_student_id',
        'related_employee_id',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'closed_by',
        'resolution_notes',
        'response_count',
        'resolution_time_hours',
    ];

    protected $casts = [
        'cc' => 'array',
        'attachments' => 'array',
        'tags' => 'array',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'resolution_time_hours' => 'integer',
    ];

    public static function generateTicketNo(): string
    {
        $prefix = 'TKT';
        $year = date('Y');
        $lastTicket = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastTicket ? ((int) substr($lastTicket->ticket_no, -6)) + 1 : 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $sequence);
    }

    public static function categories(): array
    {
        return [
            self::CATEGORY_ADMISSION => 'Admission',
            self::CATEGORY_ACCOUNTS => 'Accounts',
            self::CATEGORY_RESULT => 'Result',
            self::CATEGORY_ATTENDANCE => 'Attendance',
            self::CATEGORY_ROUTINE => 'Routine',
            self::CATEGORY_LIBRARY => 'Library',
            self::CATEGORY_HOSTEL => 'Hostel',
            self::CATEGORY_TRANSPORT => 'Transport',
            self::CATEGORY_TECHNICAL => 'Technical',
            self::CATEGORY_GENERAL => 'General',
        ];
    }

    public static function priorities(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
            self::PRIORITY_CRITICAL => 'Critical',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_WAITING => 'Waiting',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'contact_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Department::class, 'department_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(CrmTicketReply::class, 'ticket_id');
    }

    public function calculateResolutionTime(): void
    {
        if ($this->resolved_at && $this->created_at) {
            $hours = $this->created_at->diffInHours($this->resolved_at);
            $this->update(['resolution_time_hours' => $hours]);
        }
    }
}
