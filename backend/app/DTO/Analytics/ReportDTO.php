<?php

declare(strict_types=1);

namespace App\DTO\Analytics;

use App\Enums\Analytics\ChartType;
use App\Enums\Analytics\ReportType;
use App\Enums\Analytics\VisualizationType;
use Illuminate\Http\Request;

final class ReportDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $name,
        public readonly ReportType $type,
        public readonly string $query_definition,
        public readonly ?string $description,
        public readonly ?string $created_by,
        public readonly ?string $schedule,
        public readonly bool $is_public = false,
        public readonly bool $is_scheduled = false,
        public readonly ?array $filters,
        public readonly ?array $visualization_config,
        public readonly VisualizationType $visualization_type = VisualizationType::TABLE,
        public readonly ?ChartType $chart_type,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            name: $request->input('name'),
            type: ReportType::from($request->input('type')),
            query_definition: $request->input('query_definition'),
            description: $request->input('description'),
            created_by: $request->input('created_by'),
            schedule: $request->input('schedule'),
            is_public: (bool) $request->input('is_public', false),
            is_scheduled: (bool) $request->input('is_scheduled', false),
            filters: $request->input('filters'),
            visualization_config: $request->input('visualization_config'),
            visualization_type: VisualizationType::tryFrom($request->input('visualization_type', 'table')) ?? VisualizationType::TABLE,
            chart_type: $request->input('chart_type') ? ChartType::from($request->input('chart_type')) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'type' => $this->type->value,
            'query_definition' => $this->query_definition,
            'description' => $this->description,
            'created_by' => $this->created_by,
            'schedule' => $this->schedule,
            'is_public' => $this->is_public,
            'is_scheduled' => $this->is_scheduled,
            'filters' => $this->filters,
            'visualization_config' => $this->visualization_config,
            'visualization_type' => $this->visualization_type->value,
            'chart_type' => $this->chart_type?->value,
        ];
    }
}
