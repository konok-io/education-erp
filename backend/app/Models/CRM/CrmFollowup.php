<?php

declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmFollowup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_followups';

    const TYPE_PHONE_CALL = 'phone_call';
    const TYPE_EMAIL = 'email';
    const TYPE_WHATSAPP = 'whatsapp';
    const TYPE_SMS = 'sms';
    const TYPE_MEETING = 'meeting';
    const TYPE_VIDEO_CALL = 'video_call';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_RESCHEDULED = 'rescheduled';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_RESPONSE = 'no_response';

    protected $fillable = [
        'uuid',
        'followup_no',
        'lead_id',
        'inquiry_id',
        'contact_id',
        'conducted_by',
        'followup_type',
        'scheduled_date',
        'scheduled_time',
        'conducted_date',
        'status',
        'purpose',
        'outcome',
        'duration_minutes',
        'next_followup_date',
        'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'conducted_date' => 'date',
        'next_followup_date' => 'date',
        'duration_minutes' => 'integer',
    ];

    public static function generateFollowupNo(): string
    {
        $prefix = 'FUP';
        $year = date('Y');
        $lastFollowup = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastFollowup ? ((int) substr($lastFollowup->followup_no, -5)) + 1 : 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    public static function followupTypes(): array
    {
        return [
            self::TYPE_PHONE_CALL => 'Phone Call',
            self::TYPE_EMAIL => 'Email',
            self::TYPE_WHATSAPP => 'WhatsApp',
            self::TYPE_SMS => 'SMS',
            self::TYPE_MEETING => 'Meeting',
            self::TYPE_VIDEO_CALL => 'Video Call',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_RESCHEDULED => 'Rescheduled',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_NO_RESPONSE => 'No Response',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(CrmInquiry::class, 'inquiry_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'contact_id');
    }

    public function conductor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'conducted_by');
    }

    public function markCompleted(string $outcome, ?int $duration = null): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'conducted_date' => now(),
            'outcome' => $outcome,
            'duration_minutes' => $duration,
        ]);
    }
}
