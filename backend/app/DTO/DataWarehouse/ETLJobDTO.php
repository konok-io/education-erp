<?php

declare(strict_types=1);

namespace App\DTO\DataWarehouse;

use App\Enums\DataWarehouse\DataStatus;
use App\Enums\DataWarehouse\SyncStatus;
use Illuminate\Http\Request;

final class ETLJobDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $job_name,
        public readonly string $source_type,
        public readonly string $target_type,
        public readonly ?string $source_config,
        public readonly ?string $target_config,
        public readonly ?string $transformation_rules,
        public readonly SyncStatus $sync_status = SyncStatus::IDLE,
        public readonly DataStatus $data_status = DataStatus::PENDING,
        public readonly ?int $records_processed,
        public readonly ?int $records_failed,
        public readonly ?string $last_run_at,
        public readonly ?string $next_run_at,
        public readonly ?string $created_by,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            job_name: $request->input('job_name'),
            source_type: $request->input('source_type'),
            target_type: $request->input('target_type'),
            source_config: $request->input('source_config'),
            target_config: $request->input('target_config'),
            transformation_rules: $request->input('transformation_rules'),
            sync_status: SyncStatus::tryFrom($request->input('sync_status', 'idle')) ?? SyncStatus::IDLE,
            data_status: DataStatus::tryFrom($request->input('data_status', 'pending')) ?? DataStatus::PENDING,
            records_processed: $request->input('records_processed') ? (int) $request->input('records_processed') : null,
            records_failed: $request->input('records_failed') ? (int) $request->input('records_failed') : null,
            last_run_at: $request->input('last_run_at'),
            next_run_at: $request->input('next_run_at'),
            created_by: $request->input('created_by'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'job_name' => $this->job_name,
            'source_type' => $this->source_type,
            'target_type' => $this->target_type,
            'source_config' => $this->source_config,
            'target_config' => $this->target_config,
            'transformation_rules' => $this->transformation_rules,
            'sync_status' => $this->sync_status->value,
            'data_status' => $this->data_status->value,
            'records_processed' => $this->records_processed,
            'records_failed' => $this->records_failed,
            'last_run_at' => $this->last_run_at,
            'next_run_at' => $this->next_run_at,
            'created_by' => $this->created_by,
        ];
    }
}
