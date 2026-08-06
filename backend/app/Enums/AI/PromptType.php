<?php

declare(strict_types=1);

namespace App\Enums\AI;

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
