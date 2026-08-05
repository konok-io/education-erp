<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    /**
     * The model instance.
     */
    protected Model $model;

    /**
     * Get all records.
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }

    /**
     * Find a record by ID.
     */
    public function find(int|string $id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Find a record by UUID.
     */
    public function findByUuid(string $uuid, array $columns = ['*']): ?Model
    {
        return $this->model->where('uuid', $uuid)->first($columns);
    }

    /**
     * Find a record or fail.
     */
    public function findOrFail(int|string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Find by UUID or fail.
     */
    public function findByUuidOrFail(string $uuid): Model
    {
        return $this->model->where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Create a new record.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a record.
     */
    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        return $model->fresh();
    }

    /**
     * Delete a record.
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    /**
     * Force delete a record.
     */
    public function forceDelete(Model $model): bool
    {
        return $model->forceDelete();
    }

    /**
     * Restore a soft deleted record.
     */
    public function restore(Model $model): Model
    {
        $model->restore();
        return $model;
    }

    /**
     * Paginate records.
     */
    public function paginate(
        int $perPage = 15,
        array $columns = ['*'],
        string $orderBy = 'created_at',
        string $sort = 'desc'
    ): LengthAwarePaginator {
        return $this->model
            ->orderBy($orderBy, $sort)
            ->paginate($perPage, $columns);
    }

    /**
     * Get records with filters.
     */
    public function filter(array $filters): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                $query->where($key, $value);
            }
        }

        return $query->paginate(
            $filters['per_page'] ?? 15,
            $filters['columns'] ?? ['*']
        );
    }

    /**
     * Get count.
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Check if record exists.
     */
    public function exists(int|string $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Check if UUID exists.
     */
    public function uuidExists(string $uuid): bool
    {
        return $this->model->where('uuid', $uuid)->exists();
    }

    /**
     * Get only trashed records.
     */
    public function trashed(): Collection
    {
        return $this->model->onlyTrashed()->get();
    }

    /**
     * Get with trashed.
     */
    public function withTrashed(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->model->withTrashed();
    }
}
