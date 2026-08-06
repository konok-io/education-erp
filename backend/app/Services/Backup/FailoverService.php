<?php

declare(strict_types=1);

namespace App\Services\Backup;

use App\Enums\Backup\FailoverStatus;
use App\Enums\Backup\FailoverType;
use App\Models\Backup\FailoverEvent;
use App\Models\Backup\DRSite;
use App\Models\Backup\BackupAuditLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class FailoverService
{
    public function index(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = FailoverEvent::query();

        if (isset($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (isset($filters['source_site'])) {
            $query->where('source_site', $filters['source_site']);
        }

        if (isset($filters['destination_site'])) {
            $query->where('destination_site', $filters['destination_site']);
        }

        $query->orderByDesc('created_at');

        return $query->paginate($perPage);
    }

    public function getActiveFailovers(): Collection
    {
        return FailoverEvent::where('status', FailoverStatus::IN_PROGRESS->value)
            ->orderBy('initiated_at')
            ->get();
    }

    public function getRecentEvents(int $limit = 10): Collection
    {
        return FailoverEvent::orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function findOrFail(string $id): FailoverEvent
    {
        return FailoverEvent::findOrFail($id);
    }

    public function initiate(
        string $name,
        FailoverType $type,
        string $sourceSite,
        string $destinationSite,
        string $initiatedById,
        ?string $triggerReason = null,
        ?string $triggerDetails = null,
        ?string $approvedById = null
    ): FailoverEvent {
        $failoverEvent = FailoverEvent::create([
            'name' => $name,
            'type' => $type->value,
            'status' => FailoverStatus::INITIATED->value,
            'source_site' => $sourceSite,
            'destination_site' => $destinationSite,
            'trigger_reason' => $triggerReason,
            'trigger_details' => $triggerDetails,
            'initiated_by' => $initiatedById,
            'approved_by' => $approvedById,
        ]);

        BackupAuditLog::log(
            eventType: BackupAuditLog::EVENT_FAILOVER_EXECUTED,
            severity: $type === FailoverType::AUTOMATIC ? 'warning' : 'info',
            category: BackupAuditLog::CATEGORY_FAILOVER,
            message: "Failover '{$name}' initiated from {$sourceSite} to {$destinationSite}",
            referenceId: $failoverEvent->id,
            referenceType: 'failover_event',
            eventData: [
                'type' => $type->value,
                'source_site' => $sourceSite,
                'destination_site' => $destinationSite,
                'trigger_reason' => $triggerReason,
            ],
        );

        return $failoverEvent;
    }

    public function start(string $id): FailoverEvent
    {
        $failoverEvent = $this->findOrFail($id);
        $failoverEvent->update(['status' => FailoverStatus::IN_PROGRESS->value]);

        return $failoverEvent->fresh();
    }

    public function complete(string $id): FailoverEvent
    {
        $failoverEvent = $this->findOrFail($id);
        $failoverEvent->complete();

        BackupAuditLog::log(
            eventType: BackupAuditLog::EVENT_FAILOVER_COMPLETED,
            severity: 'info',
            category: BackupAuditLog::CATEGORY_FAILOVER,
            message: "Failover '{$failoverEvent->name}' completed successfully",
            referenceId: $failoverEvent->id,
            referenceType: 'failover_event',
            eventData: [
                'recovery_time_seconds' => $failoverEvent->recovery_time_seconds,
                'affected_users' => $failoverEvent->affected_users,
            ],
        );

        return $failoverEvent->fresh();
    }

    public function fail(string $id, string $errorMessage): FailoverEvent
    {
        $failoverEvent = $this->findOrFail($id);
        $failoverEvent->fail($errorMessage);

        BackupAuditLog::log(
            eventType: BackupAuditLog::EVENT_FAILOVER_EXECUTED,
            severity: 'error',
            category: BackupAuditLog::CATEGORY_FAILOVER,
            message: "Failover '{$failoverEvent->name}' failed: {$errorMessage}",
            referenceId: $failoverEvent->id,
            referenceType: 'failover_event',
            eventData: ['error' => $errorMessage],
        );

        return $failoverEvent->fresh();
    }

    public function rollback(string $id): FailoverEvent
    {
        $failoverEvent = $this->findOrFail($id);
        $failoverEvent->rollback();

        return $failoverEvent->fresh();
    }

    public function cancel(string $id): FailoverEvent
    {
        $failoverEvent = $this->findOrFail($id);
        $failoverEvent->cancel();

        return $failoverEvent->fresh();
    }

    public function updateAffectedCount(string $id, int $affectedUsers, int $downtimeSeconds): FailoverEvent
    {
        $failoverEvent = $this->findOrFail($id);
        $failoverEvent->update([
            'affected_users' => $affectedUsers,
            'downtime_seconds' => $downtimeSeconds,
        ]);

        return $failoverEvent->fresh();
    }

    public function delete(string $id): bool
    {
        $failoverEvent = $this->findOrFail($id);
        return $failoverEvent->delete();
    }

    // DR Sites
    public function getDRSites(array $filters = []): Collection
    {
        $query = DRSite::query();

        if (isset($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (isset($filters['region'])) {
            $query->byRegion($filters['region']);
        }

        $query->orderBy('name');

        return $query->get();
    }

    public function findDRSiteOrFail(string $id): DRSite
    {
        return DRSite::findOrFail($id);
    }

    public function createDRSite(array $data): DRSite
    {
        $data['slug'] = $data['slug'] ?? \Str::slug($data['name']);
        return DRSite::create($data);
    }

    public function updateDRSite(string $id, array $data): DRSite
    {
        $site = $this->findDRSiteOrFail($id);
        $site->update($data);
        return $site->fresh();
    }

    public function updateDRSiteHealth(string $id, string $status): DRSite
    {
        $site = $this->findDRSiteOrFail($id);
        $site->updateHealthStatus($status);
        return $site->fresh();
    }

    public function deleteDRSite(string $id): bool
    {
        $site = $this->findDRSiteOrFail($id);
        return $site->delete();
    }

    public function getSummary(): array
    {
        $events = FailoverEvent::all();
        $sites = DRSite::all();

        return [
            'total_events' => $events->count(),
            'completed_failovers' => $events->where('status', FailoverStatus::COMPLETED->value)->count(),
            'failed_failovers' => $events->where('status', FailoverStatus::FAILED->value)->count(),
            'in_progress' => $events->where('status', FailoverStatus::IN_PROGRESS->value)->count(),
            'total_sites' => $sites->count(),
            'healthy_sites' => $sites->where('health_status', 'healthy')->count(),
            'unhealthy_sites' => $sites->where('health_status', '!=', 'healthy')->count(),
        ];
    }
}
