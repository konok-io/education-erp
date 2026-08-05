<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    /**
     * The model instance.
     */
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Find by UUID.
     */
    public function findByUuid(string $uuid): ?User
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    /**
     * Find by email.
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Find by mobile.
     */
    public function findByMobile(string $mobile): ?User
    {
        return $this->model->where('mobile', $mobile)->first();
    }

    /**
     * Check if email exists.
     */
    public function emailExists(string $email, ?string $excludeUuid = null): bool
    {
        $query = $this->model->where('email', $email);

        if ($excludeUuid) {
            $query->where('uuid', '!=', $excludeUuid);
        }

        return $query->exists();
    }

    /**
     * Check if mobile exists.
     */
    public function mobileExists(string $mobile, ?string $excludeUuid = null): bool
    {
        $query = $this->model->where('mobile', $mobile);

        if ($excludeUuid) {
            $query->where('uuid', '!=', $excludeUuid);
        }

        return $query->exists();
    }

    /**
     * Get query builder.
     */
    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->model->newQuery();
    }
}
