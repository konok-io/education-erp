<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObservabilityStatusPage extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'observability_status_pages';

    protected $fillable = [
        'name',
        'slug',
        'title',
        'description',
        'logo_url',
        'timezone',
        'status',
        'header_settings',
        'footer_settings',
        'custom_css',
        'show_incident_history',
        'is_active',
    ];

    protected $casts = [
        'header_settings' => 'array',
        'footer_settings' => 'array',
        'custom_css' => 'array',
        'show_incident_history' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function components(): HasMany
    {
        return $this->hasMany(ObservabilityStatusPageComponent::class, 'status_page_id');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(ObservabilityStatusPageIncident::class, 'status_page_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    public function getOverallStatus(): string
    {
        $components = $this->components;
        
        if ($components->isEmpty()) {
            return 'unknown';
        }

        if ($components->where('status', 'major_outage')->isNotEmpty()) {
            return 'major_outage';
        }

        if ($components->where('status', 'partial_outage')->isNotEmpty()) {
            return 'partial_outage';
        }

        if ($components->where('status', 'degraded')->isNotEmpty()) {
            return 'degraded';
        }

        if ($components->where('status', 'maintenance')->isNotEmpty()) {
            return 'maintenance';
        }

        return 'operational';
    }

    public function calculateOverallStatus(): void
    {
        $this->update(['status' => $this->getOverallStatus()]);
    }
}
