<?php

declare(strict_types=1);

namespace App\DTO\AI;

use App\Enums\AI\AutomationStatus;
use Illuminate\Http\Request;

final class AutomationDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $name,
        public readonly string $trigger_type,
        public readonly ?string $trigger_config,
        public readonly ?string $workflow_definition,
        public readonly ?string $agent_uuid,
        public readonly AutomationStatus $status = AutomationStatus::DRAFT,
        public readonly ?int $execution_count,
        public readonly ?int $success_count,
        public readonly ?int $failure_count,
        public readonly ?string $last_executed_at,
        public readonly ?string $created_by,
        public readonly ?string $description,
        public readonly ?array $schedule,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            name: $request->input('name'),
            trigger_type: $request->input('trigger_type'),
            trigger_config: $request->input('trigger_config'),
            workflow_definition: $request->input('workflow_definition'),
            agent_uuid: $request->input('agent_uuid'),
            status: AutomationStatus::tryFrom($request->input('status', 'draft')) ?? AutomationStatus::DRAFT,
            execution_count: $request->input('execution_count') ? (int) $request->input('execution_count') : null,
            success_count: $request->input('success_count') ? (int) $request->input('success_count') : null,
            failure_count: $request->input('failure_count') ? (int) $request->input('failure_count') : null,
            last_executed_at: $request->input('last_executed_at'),
            created_by: $request->input('created_by'),
            description: $request->input('description'),
            schedule: $request->input('schedule'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'trigger_type' => $this->trigger_type,
            'trigger_config' => $this->trigger_config,
            'workflow_definition' => $this->workflow_definition,
            'agent_uuid' => $this->agent_uuid,
            'status' => $this->status->value,
            'execution_count' => $this->execution_count,
            'success_count' => $this->success_count,
            'failure_count' => $this->failure_count,
            'last_executed_at' => $this->last_executed_at,
            'created_by' => $this->created_by,
            'description' => $this->description,
            'schedule' => $this->schedule,
        ];
    }
}
