<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservabilityStatusPageComponent extends Model
{
    use HasUuids, HasUuid;

    protected $table = 'observability_status_page_components';

    protected $fillable = [
        'status_page_id',
        'service_id',
        'name',
        'description',
        'position',
        'status',
        'show_history',
    ];

    protected $casts = [
        'show_history' => 'boolean',
    ];

    public function statusPage(): BelongsTo
    {
        return $this->belongsTo(ObservabilityStatusPage::class, 'status_page_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(ObservabilityService::class, 'service_id');
    }

    public function scopeByStatusPage($query, string $statusPageId)
    {
        return $query->where('status_page_id', $statusPageId);
    }

    public function isOperational(): bool
    {
        return $this->status === 'operational';
    }

    public function isDegraded(): bool
    {
        return in_array($this->status, ['degraded', 'partial_outage']);
    }

    public function isDown(): bool
    {
        return in_array($this->status, ['major_outage', 'down']);
    }
}
