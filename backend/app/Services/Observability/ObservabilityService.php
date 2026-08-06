<?php

declare(strict_types=1);

namespace App\Services\Observability;

use App\Enums\Observability\ServiceStatus;
use App\Enums\Observability\ServiceType;
use App\Models\Observability\ObservabilityService as ObservabilityServiceModel;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ObservabilityService extends BaseService
{
    public function __construct()
    {
        parent::__construct(ObservabilityServiceModel::class);
    }

    public function index(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ObservabilityServiceModel::query();

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['environment'])) {
            $query->where('environment', $filters['environment']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        $query->orderBy('name');

        return $query->paginate($perPage);
    }

    public function getAllActive(): Collection
    {
        return ObservabilityServiceModel::active()
            ->orderBy('name')
            ->get();
    }

    public function getByType(ServiceType $type): Collection
    {
        return ObservabilityServiceModel::active()
            ->byType($type->value)
            ->orderBy('name')
            ->get();
    }

    public function getByEnvironment(string $environment): Collection
    {
        return ObservabilityServiceModel::active()
            ->byEnvironment($environment)
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): ObservabilityServiceModel
    {
        return ObservabilityServiceModel::create($data);
    }

    public function update(string $id, array $data): ObservabilityServiceModel
    {
        $service = $this->findOrFail($id);
        $service->update($data);
        return $service->fresh();
    }

    public function updateStatus(string $id, ServiceStatus $status): ObservabilityServiceModel
    {
        $service = $this->findOrFail($id);
        $service->update(['status' => $status->value]);
        return $service->fresh();
    }

    public function toggleActive(string $id): ObservabilityServiceModel
    {
        $service = $this->findOrFail($id);
        $service->update(['is_active' => !$service->is_active]);
        return $service->fresh();
    }

    public function getHealthSummary(): array
    {
        $services = ObservabilityServiceModel::active()->get();

        return [
            'total' => $services->count(),
            'healthy' => $services->where('status', ServiceStatus::HEALTHY)->count(),
            'degraded' => $services->where('status', ServiceStatus::DEGRADED)->count(),
            'down' => $services->where('status', ServiceStatus::DOWN)->count(),
            'unknown' => $services->where('status', ServiceStatus::UNKNOWN)->count(),
        ];
    }

    public function delete(string $id): bool
    {
        return $this->findOrFail($id)->delete();
    }
}
