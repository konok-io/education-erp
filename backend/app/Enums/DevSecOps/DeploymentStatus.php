<?php

declare(strict_types=1);

namespace App\Enums\DevSecOps;

enum DeploymentStatus: string
{
    case PENDING = 'pending';
    case DEPLOYING = 'deploying';
    case DEPLOYED = 'deployed';
    case FAILED = 'failed';
    case ROLLED_BACK = 'rolled_back';
    case SCALING = 'scaling';
    case MONITORING = 'monitoring';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::DEPLOYING => 'Deploying',
            self::DEPLOYED => 'Deployed',
            self::FAILED => 'Failed',
            self::ROLLED_BACK => 'Rolled Back',
            self::SCALING => 'Scaling',
            self::MONITORING => 'Monitoring',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'gray',
            self::DEPLOYING => 'blue',
            self::DEPLOYED => 'green',
            self::FAILED => 'red',
            self::ROLLED_BACK => 'orange',
            self::SCALING => 'blue',
            self::MONITORING => 'cyan',
        };
    }
}
