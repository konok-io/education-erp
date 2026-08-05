<?php

declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmCommunication extends Model
{
    use HasFactory;

    protected $table = 'crm_communications';

    const CHANNEL_EMAIL = 'email';
    const CHANNEL_SMS = 'sms';
    const CHANNEL_WHATSAPP = 'whatsapp';
    const CHANNEL_PUSH = 'push';
    const CHANNEL_PHONE = 'phone';

    const DIRECTION_INBOUND = 'inbound';
    const DIRECTION_OUTBOUND = 'outbound';

    const TYPE_TRANSACTIONAL = 'transactional';
    const TYPE_PROMOTIONAL = 'promotional';
    const TYPE_NOTIFICATION = 'notification';
    const TYPE_REMINDER = 'reminder';
    const TYPE_CAMPAIGN = 'campaign';
    const TYPE_AUTORESPONSE = 'autoresponse';
    const TYPE_BROADCAST = 'broadcast';

    const STATUS_QUEUED = 'queued';
    const STATUS_SENDING = 'sending';
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_READ = 'read';
    const STATUS_FAILED = 'failed';
    const STATUS_BOUNCED = 'bounced';
    const STATUS_UNDELIVERED = 'undelivered';

    protected $fillable = [
        'uuid',
        'communication_no',
        'contact_id',
        'lead_id',
        'student_id',
        'campaign_id',
        'channel',
        'direction',
        'type',
        'subject',
        'content',
        'metadata',
        'attachments',
        'recipient_name',
        'recipient_email',
        'recipient_mobile',
        'delivery_status',
        'sent_at',
        'delivered_at',
        'read_at',
        'failure_reason',
        'sent_by',
        'related_ticket_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'attachments' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public static function generateCommunicationNo(): string
    {
        $prefix = 'COMM';
        $year = date('Y');
        $lastComm = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastComm ? ((int) substr($lastComm->communication_no, -6)) + 1 : 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $sequence);
    }

    public static function channels(): array
    {
        return [
            self::CHANNEL_EMAIL => 'Email',
            self::CHANNEL_SMS => 'SMS',
            self::CHANNEL_WHATSAPP => 'WhatsApp',
            self::CHANNEL_PUSH => 'Push Notification',
            self::CHANNEL_PHONE => 'Phone Call',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'contact_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student\Student::class, 'student_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(CrmCampaign::class, 'campaign_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'sent_by');
    }

    public function markAsSent(): void
    {
        $this->update([
            'delivery_status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'delivery_status' => self::STATUS_DELIVERED,
            'delivered_at' => now(),
        ]);
    }

    public function markAsRead(): void
    {
        $this->update([
            'delivery_status' => self::STATUS_READ,
            'read_at' => now(),
        ]);
    }

    public function markAsFailed(string $reason): void
    {
        $this->update([
            'delivery_status' => self::STATUS_FAILED,
            'failure_reason' => $reason,
        ]);
    }
}
