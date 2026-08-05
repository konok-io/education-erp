<?php

declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmFeedback extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_feedbacks';

    const TYPE_SUGGESTION = 'suggestion';
    const TYPE_COMPLAINT = 'complaint';
    const TYPE_COMPLIMENT = 'compliment';
    const TYPE_SERVICE_RATING = 'service_rating';
    const TYPE_EXPERIENCE = 'experience';

    const STATUS_SUBMITTED = 'submitted';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'uuid',
        'feedback_no',
        'contact_id',
        'student_id',
        'employee_id',
        'ticket_id',
        'feedback_type',
        'subject',
        'content',
        'rating',
        'metadata',
        'attachments',
        'status',
        'assigned_to',
        'resolution',
        'resolved_by',
        'resolved_at',
        'ip_address',
    ];

    protected $casts = [
        'metadata' => 'array',
        'attachments' => 'array',
        'rating' => 'integer',
        'resolved_at' => 'datetime',
    ];

    public static function generateFeedbackNo(): string
    {
        $prefix = 'FDBK';
        $year = date('Y');
        $lastFeedback = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastFeedback ? ((int) substr($lastFeedback->feedback_no, -5)) + 1 : 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    public static function feedbackTypes(): array
    {
        return [
            self::TYPE_SUGGESTION => 'Suggestion',
            self::TYPE_COMPLAINT => 'Complaint',
            self::TYPE_COMPLIMENT => 'Compliment',
            self::TYPE_SERVICE_RATING => 'Service Rating',
            self::TYPE_EXPERIENCE => 'Experience',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'contact_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student\Student::class, 'student_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Employee\Employee::class, 'employee_id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(CrmTicket::class, 'ticket_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'resolved_by');
    }
}
