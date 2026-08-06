<?php

declare(strict_types=1);

namespace App\Enums\APIGateway;

enum RateLimitTier: string
{
    case FREE = 'free';
    case BASIC = 'basic';
    case STANDARD = 'standard';
    case PROFESSIONAL = 'professional';
    case ENTERPRISE = 'enterprise';

    public function label(): string
    {
        return match($this) {
            self::FREE => 'Free',
            self::BASIC => 'Basic',
            self::STANDARD => 'Standard',
            self::PROFESSIONAL => 'Professional',
            self::ENTERPRISE => 'Enterprise',
        };
    }

    public function requestsPerMinute(): int
    {
        return match($this) {
            self::FREE => 60,
            self::BASIC => 300,
            self::STANDARD => 1000,
            self::PROFESSIONAL => 5000,
            self::ENTERPRISE => 60000,
        };
    }
}
