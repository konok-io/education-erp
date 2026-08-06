<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservabilityStatusPageIncident extends Model
{
    use HasUuids, HasUuid;

    protected $table = 'observability_status_page_incidents';

    protected $fillable = [
        'status_page_id',
        'incident_id',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function statusPage(): BelongsTo
    {
        return $this->belongsTo(ObservabilityStatusPage::class, 'status_page_id');
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(ObservabilityIncident::class, 'incident_id');
    }

    public function scopeByStatusPage($query, string $statusPageId)
    {
        return $query->where('status_page_id', $statusPageId);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }
}
