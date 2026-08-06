<?php

declare(strict_types=1);

namespace App\DTO\Observability;

use App\Models\Observability\ObservabilityStatusPage;
use Spatie\DataTransferObject\DataTransferObject;

class StatusPageDTO extends DataTransferObject
{
    public string $id;
    public string $name;
    public string $slug;
    public string $title;
    public ?string $description;
    public ?string $logo_url;
    public string $timezone;
    public string $status;
    public ?array $header_settings;
    public ?array $footer_settings;
    public ?array $custom_css;
    public bool $show_incident_history;
    public bool $is_active;
    public array $components;
    public array $active_incidents;
    public array $recent_incidents;
    public string $created_at;
    public string $updated_at;

    public static function fromModel(ObservabilityStatusPage $statusPage, array $components = [], array $incidents = []): self
    {
        return new self(
            id: $statusPage->id,
            name: $statusPage->name,
            slug: $statusPage->slug,
            title: $statusPage->title,
            description: $statusPage->description,
            logo_url: $statusPage->logo_url,
            timezone: $statusPage->timezone,
            status: $statusPage->status,
            header_settings: $statusPage->header_settings,
            footer_settings: $statusPage->footer_settings,
            custom_css: $statusPage->custom_css,
            show_incident_history: $statusPage->show_incident_history,
            is_active: $statusPage->is_active,
            components: $components,
            active_incidents: $incidents,
            recent_incidents: [],
            created_at: $statusPage->created_at->toIso8601String(),
            updated_at: $statusPage->updated_at->toIso8601String(),
        );
    }
}
