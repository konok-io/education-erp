<?php

declare(strict_types=1);

namespace App\Enums\DevSecOps;

enum ReleaseType: string
{
    case MAJOR = 'major';
    case MINOR = 'minor';
    case PATCH = 'patch';
    case RC = 'rc';
    case LTS = 'lts';
    case HOTFIX = 'hotfix';

    public function label(): string
    {
        return match($this) {
            self::MAJOR => 'Major',
            self::MINOR => 'Minor',
            self::PATCH => 'Patch',
            self::RC => 'Release Candidate',
            self::LTS => 'Long Term Support',
            self::HOTFIX => 'Hotfix',
        };
    }

    public function incrementPart(): string
    {
        return match($this) {
            self::MAJOR => 'major',
            self::MINOR => 'minor',
            self::PATCH => 'patch',
            self::RC => 'prerelease',
            self::LTS => 'major',
            self::HOTFIX => 'patch',
        };
    }

    public function isPrerelease(): bool
    {
        return in_array($this, [self::RC]);
    }
}
