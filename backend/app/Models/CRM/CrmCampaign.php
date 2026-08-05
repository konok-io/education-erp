<?php

declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_campaigns';

    const TYPE_ADMISSION = 'admission';
    const TYPE_MARKETING = 'marketing';
    const TYPE_AWARENESS = 'awareness';
    const TYPE_EVENT = 'event';
    const TYPE_SCHOLARSHIP = 'scholarship';
    const TYPE_REENGAGEMENT = 'reengagement';

    const CHANNEL_EMAIL = 'email';
    const CHANNEL_SMS = 'sms';
    const CHANNEL_WHATSAPP = 'whatsapp';
    const CHANNEL_PUSH = 'push';
    const CHANNEL_MULTI = 'multi';

    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_RUNNING = 'running';
    const STATUS_PAUSED = 'paused';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'uuid',
        'campaign_no',
        'name',
        'description',
        'campaign_type',
        'channel',
        'status',
        'created_by',
        'start_date',
        'end_date',
        'scheduled_at',
        'target_audience',
        'audience_filters',
        'template_data',
        'total_recipients',
        'sent_count',
        'delivered_count',
        'opened_count',
        'clicked_count',
        'responded_count',
        'converted_count',
        'budget',
        'cost_per_send',
        'conversion_rate',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'scheduled_at' => 'datetime',
        'audience_filters' => 'array',
        'template_data' => 'array',
        'budget' => 'decimal:2',
        'cost_per_send' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
    ];

    public static function generateCampaignNo(): string
    {
        $prefix = 'CMP';
        $year = date('Y');
        $lastCampaign = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastCampaign ? ((int) substr($lastCampaign->campaign_no, -5)) + 1 : 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    public static function campaignTypes(): array
    {
        return [
            self::TYPE_ADMISSION => 'Admission Campaign',
            self::TYPE_MARKETING => 'Marketing Campaign',
            self::TYPE_AWARENESS => 'Awareness Campaign',
            self::TYPE_EVENT => 'Event Campaign',
            self::TYPE_SCHOLARSHIP => 'Scholarship Campaign',
            self::TYPE_REENGAGEMENT => 'Re-engagement Campaign',
        ];
    }

    public static function channels(): array
    {
        return [
            self::CHANNEL_EMAIL => 'Email',
            self::CHANNEL_SMS => 'SMS',
            self::CHANNEL_WHATSAPP => 'WhatsApp',
            self::CHANNEL_PUSH => 'Push Notification',
            self::CHANNEL_MULTI => 'Multi-Channel',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function communications(): HasMany
    {
        return $this->hasMany(CrmCommunication::class, 'campaign_id');
    }

    public function calculateMetrics(): void
    {
        $communications = $this->communications();
        $this->update([
            'sent_count' => $communications->where('delivery_status', 'sent')->count(),
            'delivered_count' => $communications->whereIn('delivery_status', ['delivered', 'read'])->count(),
            'opened_count' => $communications->where('delivery_status', 'read')->count(),
            'conversion_rate' => $this->sent_count > 0 
                ? ($this->converted_count / $this->sent_count) * 100 
                : 0,
        ]);
    }
}
