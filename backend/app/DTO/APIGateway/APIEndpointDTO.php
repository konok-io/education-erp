<?php

declare(strict_types=1);

namespace App\DTO\APIGateway;

use App\Enums\APIGateway\EndpointStatus;
use App\Enums\APIGateway\HTTPMethod;
use App\Enums\APIGateway\RateLimitTier;
use Illuminate\Http\Request;

final class APIEndpointDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $name,
        public readonly string $path,
        public readonly HTTPMethod $method,
        public readonly string $service_name,
        public readonly string $service_url,
        public readonly EndpointStatus $status = EndpointStatus::ACTIVE,
        public readonly ?string $description,
        public readonly bool $is_auth_required = true,
        public readonly ?string $auth_type,
        public readonly RateLimitTier $rate_limit_tier = RateLimitTier::FREE,
        public readonly ?int $rate_limit_per_minute,
        public readonly bool $is_cached = false,
        public readonly ?int $cache_ttl,
        public readonly ?string $documentation_url,
        public readonly ?array $request_schema,
        public readonly ?array $response_schema,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            name: $request->input('name'),
            path: $request->input('path'),
            method: HTTPMethod::from($request->input('method')),
            service_name: $request->input('service_name'),
            service_url: $request->input('service_url'),
            status: EndpointStatus::tryFrom($request->input('status', 'active')) ?? EndpointStatus::ACTIVE,
            description: $request->input('description'),
            is_auth_required: (bool) $request->input('is_auth_required', true),
            auth_type: $request->input('auth_type'),
            rate_limit_tier: RateLimitTier::tryFrom($request->input('rate_limit_tier', 'free')) ?? RateLimitTier::FREE,
            rate_limit_per_minute: $request->input('rate_limit_per_minute') ? (int) $request->input('rate_limit_per_minute') : null,
            is_cached: (bool) $request->input('is_cached', false),
            cache_ttl: $request->input('cache_ttl') ? (int) $request->input('cache_ttl') : null,
            documentation_url: $request->input('documentation_url'),
            request_schema: $request->input('request_schema'),
            response_schema: $request->input('response_schema'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'path' => $this->path,
            'method' => $this->method->value,
            'service_name' => $this->service_name,
            'service_url' => $this->service_url,
            'status' => $this->status->value,
            'description' => $this->description,
            'is_auth_required' => $this->is_auth_required,
            'auth_type' => $this->auth_type,
            'rate_limit_tier' => $this->rate_limit_tier->value,
            'rate_limit_per_minute' => $this->rate_limit_per_minute,
            'is_cached' => $this->is_cached,
            'cache_ttl' => $this->cache_ttl,
            'documentation_url' => $this->documentation_url,
            'request_schema' => $this->request_schema,
            'response_schema' => $this->response_schema,
        ];
    }
}
