<?php

declare(strict_types=1);

namespace App\Enums\Multitenant;

enum TenantStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case PENDING = 'pending';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::SUSPENDED => 'Suspended',
            self::PENDING => 'Pending',
            self::CANCELLED => 'Cancelled',
        };
    }
}

enum SubscriptionStatus: string
{
    case TRIAL = 'trial';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::TRIAL => 'Trial',
            self::ACTIVE => 'Active',
            self::PAUSED => 'Paused',
            self::EXPIRED => 'Expired',
            self::CANCELLED => 'Cancelled',
        };
    }
}

enum SubscriptionTier: string
{
    case STARTER = 'starter';
    case BASIC = 'basic';
    case STANDARD = 'standard';
    case PROFESSIONAL = 'professional';
    case ENTERPRISE = 'enterprise';

    public function label(): string
    {
        return match($this) {
            self::STARTER => 'Starter',
            self::BASIC => 'Basic',
            self::STANDARD => 'Standard',
            self::PROFESSIONAL => 'Professional',
            self::ENTERPRISE => 'Enterprise',
        };
    }

    public function features(): array
    {
        return match($this) {
            self::STARTER => ['Basic Features', 'Up to 100 Users'],
            self::BASIC => ['Standard Features', 'Up to 500 Users'],
            self::STANDARD => ['Advanced Features', 'Up to 2000 Users'],
            self::PROFESSIONAL => ['Premium Features', 'Up to 10000 Users'],
            self::ENTERPRISE => ['All Features', 'Unlimited Users'],
        };
    }
}
