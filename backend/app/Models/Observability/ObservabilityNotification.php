<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservabilityNotification extends Model
{
    use HasUuids, HasUuid;

    protected $table = 'observability_notifications';

    protected $fillable = [
        'channel_id',
        'alert_id',
        'incident_id',
        'recipient',
        'status',
        'subject',
        'body',
        'metadata',
        'sent_at',
        'delivered_at',
        'error_message',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(ObservabilityNotificationChannel::class, 'channel_id');
    }

    public function alert(): BelongsTo
    {
        return $this->belongsTo(ObservabilityAlert::class, 'alert_id');
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(ObservabilityIncident::class, 'incident_id');
    }

    public function scopeByChannel($query, string $channelId)
    {
        return $query->where('channel_id', $channelId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function markAsSent(): void
    {
        $this->update(['status' => 'sent', 'sent_at' => now()]);
    }

    public function markAsDelivered(): void
    {
        $this->update(['status' => 'delivered', 'delivered_at' => now()]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update(['status' => 'failed', 'error_message' => $error]);
    }
}
