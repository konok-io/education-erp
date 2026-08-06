<?php

declare(strict_types=1);

namespace App\Enums\DevSecOps;

enum EnvironmentType: string
{
    case DEVELOPMENT = 'development';
    case QA = 'qa';
    case UAT = 'uat';
    case STAGING = 'staging';
    case PRODUCTION = 'production';

    public function label(): string
    {
        return match($this) {
            self::DEVELOPMENT => 'Development',
            self::QA => 'QA',
            self::UAT => 'UAT',
            self::STAGING => 'Staging',
            self::PRODUCTION => 'Production',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DEVELOPMENT => 'gray',
            self::QA => 'blue',
            self::UAT => 'purple',
            self::STAGING => 'orange',
            self::PRODUCTION => 'green',
        };
    }

    public function order(): int
    {
        return match($this) {
            self::DEVELOPMENT => 1,
            self::QA => 2,
            self::UAT => 3,
            self::STAGING => 4,
            self::PRODUCTION => 5,
        };
    }

    public function isProduction(): bool
    {
        return $this === self::PRODUCTION;
    }

    public function requiresApproval(): bool
    {
        return in_array($this, [self::STAGING, self::PRODUCTION]);
    }
}
