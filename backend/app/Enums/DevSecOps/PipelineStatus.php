<?php

declare(strict_types=1);

namespace App\Enums\DevSecOps;

enum PipelineStatus: string
{
    case INACTIVE = 'inactive';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match($this) {
            self::INACTIVE => 'Inactive',
            self::ACTIVE => 'Active',
            self::PAUSED => 'Paused',
            self::ARCHIVED => 'Archived',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::INACTIVE => 'gray',
            self::ACTIVE => 'green',
            self::PAUSED => 'yellow',
            self::ARCHIVED => 'red',
        };
    }
}
