<?php

declare(strict_types=1);

namespace App\Enums\DevSecOps;

enum ReleaseStatus: string
{
    case DRAFT = 'draft';
    case RC = 'rc';
    case STABLE = 'stable';
    case LTS = 'lts';
    case DEPRECATED = 'deprecated';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::RC => 'Release Candidate',
            self::STABLE => 'Stable',
            self::LTS => 'Long Term Support',
            self::DEPRECATED => 'Deprecated',
            self::ARCHIVED => 'Archived',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::RC => 'yellow',
            self::STABLE => 'green',
            self::LTS => 'blue',
            self::DEPRECATED => 'orange',
            self::ARCHIVED => 'red',
        };
    }
}
