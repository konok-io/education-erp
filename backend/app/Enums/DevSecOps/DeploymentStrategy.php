<?php

declare(strict_types=1);

namespace App\Enums\DevSecOps;

enum DeploymentStrategy: string
{
    case ROLLING = 'rolling';
    case BLUE_GREEN = 'blue_green';
    case CANARY = 'canary';
    case AB = 'ab';
    case SHADOW = 'shadow';
    case RECREATE = 'recreate';

    public function label(): string
    {
        return match($this) {
            self::ROLLING => 'Rolling Update',
            self::BLUE_GREEN => 'Blue/Green',
            self::CANARY => 'Canary',
            self::AB => 'A/B Testing',
            self::SHADOW => 'Shadow Deployment',
            self::RECREATE => 'Recreate',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::ROLLING => 'Gradually replace old pods with new ones',
            self::BLUE_GREEN => 'Deploy new version alongside old, then switch traffic',
            self::CANARY => 'Gradually shift traffic to new version',
            self::AB => 'Split traffic between versions for testing',
            self::SHADOW => 'Mirror traffic to new version without affecting users',
            self::RECREATE => 'Terminate all old pods before creating new ones',
        };
    }
}
