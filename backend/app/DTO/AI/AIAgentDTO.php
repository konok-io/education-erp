<?php

declare(strict_types=1);

namespace App\DTO\AI;

use App\Enums\AI\AgentStatus;
use Illuminate\Http\Request;

final class AIAgentDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $name,
        public readonly string $agent_type,
        public readonly ?string $description,
        public readonly ?string $system_prompt,
        public readonly ?string $model_name,
        public readonly ?float $temperature,
        public readonly ?int $max_tokens,
        public readonly AgentStatus $status = AgentStatus::IDLE,
        public readonly ?array $tools,
        public readonly ?array $capabilities,
        public readonly ?array $memory_config,
        public readonly ?string $icon,
        public readonly ?string $color,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            name: $request->input('name'),
            agent_type: $request->input('agent_type'),
            description: $request->input('description'),
            system_prompt: $request->input('system_prompt'),
            model_name: $request->input('model_name'),
            temperature: $request->input('temperature') ? (float) $request->input('temperature') : null,
            max_tokens: $request->input('max_tokens') ? (int) $request->input('max_tokens') : null,
            status: AgentStatus::tryFrom($request->input('status', 'idle')) ?? AgentStatus::IDLE,
            tools: $request->input('tools'),
            capabilities: $request->input('capabilities'),
            memory_config: $request->input('memory_config'),
            icon: $request->input('icon'),
            color: $request->input('color'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'agent_type' => $this->agent_type,
            'description' => $this->description,
            'system_prompt' => $this->system_prompt,
            'model_name' => $this->model_name,
            'temperature' => $this->temperature,
            'max_tokens' => $this->max_tokens,
            'status' => $this->status->value,
            'tools' => $this->tools,
            'capabilities' => $this->capabilities,
            'memory_config' => $this->memory_config,
            'icon' => $this->icon,
            'color' => $this->color,
        ];
    }
}
