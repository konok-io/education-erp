<?php

declare(strict_types=1);

namespace App\Enums\Multitenant;

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
