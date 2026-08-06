<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservabilityNotificationTemplate extends Model
{
    use HasUuids, HasUuid;

    protected $table = 'observability_notification_templates';

    protected $fillable = [
        'channel_id',
        'name',
        'event_type',
        'subject_template',
        'body_template',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(ObservabilityNotificationChannel::class, 'channel_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeByChannel($query, string $channelId)
    {
        return $query->where('channel_id', $channelId);
    }

    public function render(array $data): array
    {
        $subject = $this->subject_template ?? '';
        $body = $this->body_template;

        foreach ($this->variables ?? [] as $variable) {
            $value = $data[$variable] ?? '';
            $subject = str_replace('{{' . $variable . '}}', $value, $subject);
            $body = str_replace('{{' . $variable . '}}', $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }
}
