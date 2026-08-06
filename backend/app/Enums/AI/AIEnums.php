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

enum AutomationStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::ACTIVE => 'Active',
            self::PAUSED => 'Paused',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }
}

enum PromptType: string
{
    case SYSTEM = 'system';
    case USER = 'user';
    case ASSISTANT = 'assistant';
    case WORKFLOW = 'workflow';

    public function label(): string
    {
        return match($this) {
            self::SYSTEM => 'System',
            self::USER => 'User',
            self::ASSISTANT => 'Assistant',
            self::WORKFLOW => 'Workflow',
        };
    }
}
