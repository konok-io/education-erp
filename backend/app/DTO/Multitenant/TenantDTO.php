<?php

declare(strict_types=1);

namespace App\DTO\Multitenant;

use App\Enums\Multitenant\SubscriptionStatus;
use App\Enums\Multitenant\SubscriptionTier;
use App\Enums\Multitenant\TenantStatus;
use Illuminate\Http\Request;

final class TenantDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $tenant_code,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly ?string $logo,
        public readonly ?string $address,
        public readonly ?string $website,
        public readonly TenantStatus $status = TenantStatus::PENDING,
        public readonly SubscriptionTier $subscription_tier = SubscriptionTier::STARTER,
        public readonly SubscriptionStatus $subscription_status = SubscriptionStatus::TRIAL,
        public readonly ?\DateTimeInterface $subscription_expires_at,
        public readonly ?int $max_users,
        public readonly ?int $current_users,
        public readonly ?string $database_name,
        public readonly ?string $subdomain,
        public readonly ?string $custom_domain,
        public readonly bool $is_verified = false,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            tenant_code: $request->input('tenant_code'),
            name: $request->input('name'),
            email: $request->input('email'),
            phone: $request->input('phone'),
            logo: $request->input('logo'),
            address: $request->input('address'),
            website: $request->input('website'),
            status: TenantStatus::tryFrom($request->input('status', 'pending')) ?? TenantStatus::PENDING,
            subscription_tier: SubscriptionTier::tryFrom($request->input('subscription_tier', 'starter')) ?? SubscriptionTier::STARTER,
            subscription_status: SubscriptionStatus::tryFrom($request->input('subscription_status', 'trial')) ?? SubscriptionStatus::TRIAL,
            subscription_expires_at: $request->input('subscription_expires_at') ? new \DateTime($request->input('subscription_expires_at')) : null,
            max_users: $request->input('max_users') ? (int) $request->input('max_users') : null,
            current_users: $request->input('current_users') ? (int) $request->input('current_users') : null,
            database_name: $request->input('database_name'),
            subdomain: $request->input('subdomain'),
            custom_domain: $request->input('custom_domain'),
            is_verified: (bool) $request->input('is_verified', false),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'tenant_code' => $this->tenant_code,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'logo' => $this->logo,
            'address' => $this->address,
            'website' => $this->website,
            'status' => $this->status->value,
            'subscription_tier' => $this->subscription_tier->value,
            'subscription_status' => $this->subscription_status->value,
            'subscription_expires_at' => $this->subscription_expires_at?->format('Y-m-d H:i:s'),
            'max_users' => $this->max_users,
            'current_users' => $this->current_users,
            'database_name' => $this->database_name,
            'subdomain' => $this->subdomain,
            'custom_domain' => $this->custom_domain,
            'is_verified' => $this->is_verified,
        ];
    }
}
