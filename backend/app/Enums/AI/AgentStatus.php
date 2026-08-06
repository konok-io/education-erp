<?php

declare(strict_types=1);

namespace App\Enums\AI;

enum AgentStatus: string
{
    case IDLE = 'idle';
    case RUNNING = 'running';
    case PAUSED = 'paused';
    case STOPPED = 'stopped';
    case ERROR = 'error';

    public function label(): string
    {
        return match($this) {
            self::IDLE => 'Idle',
            self::RUNNING => 'Running',
            self::PAUSED => 'Paused',
            self::STOPPED => 'Stopped',
            self::ERROR => 'Error',
        };
    }
}
