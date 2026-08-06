<?php

declare(strict_types=1);

namespace App\Enums\Observability;

enum LogLevel: string
{
    case DEBUG = 'debug';
    case INFO = 'info';
    case NOTICE = 'notice';
    case WARNING = 'warning';
    case ERROR = 'error';
    case CRITICAL = 'critical';
    case ALERT = 'alert';
    case EMERGENCY = 'emergency';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function severity(): int
    {
        return match($this) {
            self::DEBUG => 100,
            self::INFO => 200,
            self::NOTICE => 250,
            self::WARNING => 300,
            self::ERROR => 400,
            self::CRITICAL => 500,
            self::ALERT => 550,
            self::EMERGENCY => 600,
        };
    }
}
