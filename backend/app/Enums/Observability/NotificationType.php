<?php

declare(strict_types=1);

namespace App\Enums\Observability;

enum NotificationType: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
    case PUSH = 'push';
    case SLACK = 'slack';
    case TEAMS = 'teams';
    case DISCORD = 'discord';
    case WEBHOOK = 'webhook';
    case PAGERDUTY = 'pagerduty';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
