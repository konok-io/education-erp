<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    /**
     * The repository instance.
     */
    protected BaseRepository $repository;

    /**
     * Get all records.
     */
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->all();
    }

    /**
     * Find a record by ID.
     */
    public function find(int|string $id): ?Model
    {
        return $this->repository->find($id);
    }

    /**
     * Find by UUID.
     */
    public function findByUuid(string $uuid): ?Model
    {
        return $this->repository->findByUuid($uuid);
    }

    /**
     * Find or fail.
     */
    public function findOrFail(int|string $id): Model
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Find by UUID or fail.
     */
    public function findByUuidOrFail(string $uuid): Model
    {
        return $this->repository->findByUuidOrFail($uuid);
    }

    /**
     * Create a new record.
     */
    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    /**
     * Update a record.
     */
    public function update(Model $model, array $data): Model
    {
        return $this->repository->update($model, $data);
    }

    /**
     * Delete a record.
     */
    public function delete(Model $model): bool
    {
        return $this->repository->delete($model);
    }

    /**
     * Paginate records.
     */
    public function paginate(
        int $perPage = 15,
        string $orderBy = 'created_at',
        string $sort = 'desc'
    ): LengthAwarePaginator {
        return $this->repository->paginate($perPage, ['*'], $orderBy, $sort);
    }

    /**
     * Get count.
     */
    public function count(): int
    {
        return $this->repository->count();
    }

    /**
     * Get only trashed.
     */
    public function trashed(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->trashed();
    }

    /**
     * Restore a soft deleted record.
     */
    public function restore(Model $model): Model
    {
        return $this->repository->restore($model);
    }
}
