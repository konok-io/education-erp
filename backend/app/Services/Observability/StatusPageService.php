<?php

declare(strict_types=1);

namespace App\Services\Observability;

use App\Models\Observability\ObservabilityIncident;
use App\Models\Observability\ObservabilityStatusPage;
use App\Models\Observability\ObservabilityStatusPageComponent;
use App\DTO\Observability\StatusPageDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class StatusPageService
{
    public function index(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ObservabilityStatusPage::query();

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $query->orderBy('name');

        return $query->paginate($perPage);
    }

    public function getActivePages(): Collection
    {
        return ObservabilityStatusPage::active()->orderBy('name')->get();
    }

    public function findOrFail(string $id): ObservabilityStatusPage
    {
        return ObservabilityStatusPage::with(['components.service'])->findOrFail($id);
    }

    public function findBySlug(string $slug): ?ObservabilityStatusPage
    {
        return ObservabilityStatusPage::with(['components.service'])
            ->bySlug($slug)
            ->first();
    }

    public function getPublicStatusPage(string $slug): ?StatusPageDTO
    {
        $statusPage = $this->findBySlug($slug);

        if (!$statusPage || !$statusPage->is_active) {
            return null;
        }

        $components = $statusPage->components()
            ->orderBy('position')
            ->get()
            ->map(function ($component) {
                return [
                    'id' => $component->id,
                    'name' => $component->name,
                    'description' => $component->description,
                    'status' => $component->status,
                    'position' => $component->position,
                    'show_history' => $component->show_history,
                    'service_id' => $component->service_id,
                ];
            })
            ->toArray();

        $activeIncidents = ObservabilityIncident::active()
            ->whereHas('statusPageIncidents', function ($query) use ($statusPage) {
                $query->where('status_page_id', $statusPage->id)
                    ->where('is_visible', true);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($incident) {
                return [
                    'id' => $incident->id,
                    'incident_number' => $incident->incident_number,
                    'title' => $incident->title,
                    'severity' => $incident->severity->value,
                    'status' => $incident->status->value,
                    'started_at' => $incident->started_at->toIso8601String(),
                    'resolved_at' => $incident->resolved_at?->toIso8601String(),
                ];
            })
            ->toArray();

        return StatusPageDTO::fromModel($statusPage, $components, $activeIncidents);
    }

    public function create(array $data): ObservabilityStatusPage
    {
        $data['slug'] = $data['slug'] ?? \Str::slug($data['name']);
        return ObservabilityStatusPage::create($data);
    }

    public function update(string $id, array $data): ObservabilityStatusPage
    {
        $statusPage = $this->findOrFail($id);

        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = \Str::slug($data['name']);
        }

        $statusPage->update($data);
        return $statusPage->fresh();
    }

    public function delete(string $id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    public function addComponent(string $statusPageId, array $data): ObservabilityStatusPageComponent
    {
        $statusPage = $this->findOrFail($statusPageId);

        $maxPosition = $statusPage->components()->max('position') ?? 0;

        return ObservabilityStatusPageComponent::create([
            'status_page_id' => $statusPageId,
            'service_id' => $data['service_id'] ?? null,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'position' => $data['position'] ?? ($maxPosition + 1),
            'status' => $data['status'] ?? 'operational',
            'show_history' => $data['show_history'] ?? true,
        ]);
    }

    public function updateComponent(string $componentId, array $data): ObservabilityStatusPageComponent
    {
        $component = ObservabilityStatusPageComponent::findOrFail($componentId);
        $component->update($data);

        $statusPage = $component->statusPage;
        $statusPage->calculateOverallStatus();

        return $component->fresh();
    }

    public function updateComponentStatus(string $componentId, string $status): ObservabilityStatusPageComponent
    {
        return $this->updateComponent($componentId, ['status' => $status]);
    }

    public function deleteComponent(string $componentId): bool
    {
        $component = ObservabilityStatusPageComponent::findOrFail($componentId);
        $statusPageId = $component->status_page_id;

        $result = $component->delete();

        $statusPage = ObservabilityStatusPage::find($statusPageId);
        if ($statusPage) {
            $statusPage->calculateOverallStatus();
        }

        return $result;
    }

    public function addIncident(string $statusPageId, string $incidentId, bool $isVisible = true): void
    {
        ObservabilityStatusPageIncident::create([
            'status_page_id' => $statusPageId,
            'incident_id' => $incidentId,
            'is_visible' => $isVisible,
        ]);

        $statusPage = $this->findOrFail($statusPageId);
        $statusPage->calculateOverallStatus();
    }

    public function removeIncident(string $statusPageId, string $incidentId): bool
    {
        $result = ObservabilityStatusPageIncident::where('status_page_id', $statusPageId)
            ->where('incident_id', $incidentId)
            ->delete();

        $statusPage = ObservabilityStatusPage::find($statusPageId);
        if ($statusPage) {
            $statusPage->calculateOverallStatus();
        }

        return $result > 0;
    }

    public function refreshStatus(string $id): ObservabilityStatusPage
    {
        $statusPage = $this->findOrFail($id);

        foreach ($statusPage->components as $component) {
            if ($component->service) {
                $component->update(['status' => $component->service->status->value]);
            }
        }

        $statusPage->calculateOverallStatus();

        return $statusPage->fresh();
    }
}
