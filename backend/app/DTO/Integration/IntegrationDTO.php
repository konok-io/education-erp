<?php

declare(strict_types=1);

namespace App\DTO\Integration;

use App\Enums\Integration\IntegrationStatus;
use App\Enums\Integration\IntegrationType;
use Illuminate\Http\Request;

final class IntegrationDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $name,
        public readonly IntegrationType $type,
        public readonly IntegrationStatus $status = IntegrationStatus::PENDING,
        public readonly ?string $provider,
        public readonly ?string $api_endpoint,
        public readonly ?string $api_key,
        public readonly ?string $api_secret,
        public readonly ?array $configuration,
        public readonly ?array $webhook_urls,
        public readonly ?string $created_by,
        public readonly ?string $last_sync_at,
        public readonly bool $is_enabled = true,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            name: $request->input('name'),
            type: IntegrationType::from($request->input('type')),
            status: IntegrationStatus::tryFrom($request->input('status', 'pending')) ?? IntegrationStatus::PENDING,
            provider: $request->input('provider'),
            api_endpoint: $request->input('api_endpoint'),
            api_key: $request->input('api_key'),
            api_secret: $request->input('api_secret'),
            configuration: $request->input('configuration'),
            webhook_urls: $request->input('webhook_urls'),
            created_by: $request->input('created_by'),
            last_sync_at: $request->input('last_sync_at'),
            is_enabled: (bool) $request->input('is_enabled', true),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'type' => $this->type->value,
            'status' => $this->status->value,
            'provider' => $this->provider,
            'api_endpoint' => $this->api_endpoint,
            'api_key' => $this->api_key,
            'api_secret' => $this->api_secret,
            'configuration' => $this->configuration,
            'webhook_urls' => $this->webhook_urls,
            'created_by' => $this->created_by,
            'last_sync_at' => $this->last_sync_at,
            'is_enabled' => $this->is_enabled,
        ];
    }
}
