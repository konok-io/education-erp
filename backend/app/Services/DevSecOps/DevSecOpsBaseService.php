<?php

declare(strict_types=1);

namespace App\Services\DevSecOps;

use App\Models\DevSecOps\DevSecOpsActivityLog;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class DevSecOpsBaseService extends BaseService
{
    protected function logActivity(
        string $type,
        string $action,
        string $status,
        ?string $resourceId = null,
        ?string $resourceType = null,
        ?string $resourceName = null,
        ?array $details = null,
        ?array $metadata = null,
        ?string $message = null,
        ?array $changes = null
    ): void {
        $user = auth()->user();

        DevSecOpsActivityLog::log(
            type: $type,
            action: $action,
            status: $status,
            resourceId: $resourceId,
            resourceType: $resourceType,
            resourceName: $resourceName,
            details: $details,
            metadata: $metadata,
            message: $message,
            changes: $changes,
            actorId: $user?->id,
            actorType: $user ? get_class($user) : null,
            actorName: $user?->name,
        );
    }

    protected function paginate($query, int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $query->paginate($perPage, $columns);
    }

    protected function generateSlug(string $name): string
    {
        return \Illuminate\Support\Str::slug($name);
    }

    protected function generateUniqueSlug(string $name, string $table): string
    {
        $slug = $this->generateSlug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (\Illuminate\Support\Facades\DB::table($table)->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
