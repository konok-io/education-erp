<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\ImageHelper;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    /**
     * Get all users with filters and pagination.
     */
    public function getAll(
        int $perPage = 20,
        array $filters = [],
        string $sortBy = 'created_at',
        string $sortOrder = 'desc'
    ): LengthAwarePaginator {
        $query = $this->userRepository->query();

        // Exclude super admin
        $query->excludeSuperAdmin();

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('uuid', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['role'])) {
            $query->byRole($filters['role']);
        }

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (!empty($filters['campus_id'])) {
            $query->byCampus($filters['campus_id']);
        }

        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Generate UUID
            $data['uuid'] = \Illuminate\Support\Str::uuid()->toString();

            // Hash password
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Set default status
            $data['status'] = $data['status'] ?? 'active';

            // Create user
            $user = $this->userRepository->create($data);

            // Assign role
            if (isset($data['role_id'])) {
                $user->role_id = $data['role_id'];
                $user->save();

                $role = DB::table('roles')->where('id', $data['role_id'])->first();
                if ($role) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $role->id,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $user->id,
                    ]);
                }
            }

            return $user;
        });
    }

    /**
     * Find user by UUID.
     */
    public function findByUuid(string $uuid): ?User
    {
        return $this->userRepository->findByUuid($uuid);
    }

    /**
     * Update user.
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            // Remove password from data if empty
            if (isset($data['password']) && empty($data['password'])) {
                unset($data['password']);
            } elseif (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Update role if changed
            if (isset($data['role_id']) && $data['role_id'] !== $user->role_id) {
                DB::table('model_has_roles')
                    ->where('model_id', $user->id)
                    ->where('model_type', 'App\\Models\\User')
                    ->delete();

                DB::table('model_has_roles')->insert([
                    'role_id' => $data['role_id'],
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $user->id,
                ]);
            }

            return $this->userRepository->update($user, $data);
        });
    }

    /**
     * Delete user.
     */
    public function delete(User $user): bool
    {
        // Prevent deleting super admin
        if ($user->isSuperAdmin()) {
            throw new \Exception('Cannot delete super admin');
        }

        // Delete avatar if exists
        if ($user->avatar) {
            ImageHelper::delete($user->avatar);
        }

        return $this->userRepository->delete($user);
    }

    /**
     * Update user avatar.
     */
    public function updateAvatar(User $user, UploadedFile $file): string
    {
        // Delete old avatar
        if ($user->avatar) {
            ImageHelper::delete($user->avatar);
        }

        // Upload new avatar
        $path = ImageHelper::uploadAndResize($file, 'users', 300, 300);

        // Update user
        $this->userRepository->update($user, ['avatar' => $path]);

        return asset('storage/' . $path);
    }

    /**
     * Change user password.
     */
    public function changePassword(User $user, string $password): void
    {
        $this->userRepository->update($user, [
            'password' => Hash::make($password),
        ]);
    }

    /**
     * Update user status.
     */
    public function updateStatus(User $user, string $status): void
    {
        // Prevent changing super admin status
        if ($user->isSuperAdmin()) {
            throw new \Exception('Cannot change super admin status');
        }

        $this->userRepository->update($user, ['status' => $status]);
    }

    /**
     * Assign role to user.
     */
    public function assignRole(User $user, int $roleId): void
    {
        // Remove existing roles
        DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', 'App\\Models\\User')
            ->delete();

        // Assign new role
        $this->userRepository->update($user, ['role_id' => $roleId]);

        DB::table('model_has_roles')->insert([
            'role_id' => $roleId,
            'model_type' => 'App\\Models\\User',
            'model_id' => $user->id,
        ]);
    }

    /**
     * Search users.
     */
    public function search(string $query, int $perPage = 20): LengthAwarePaginator
    {
        return $this->userRepository->query()
            ->excludeSuperAdmin()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('mobile', 'like', "%{$query}%");
            })
            ->paginate($perPage);
    }

    /**
     * Bulk update status.
     */
    public function bulkUpdateStatus(array $uuids, string $status): void
    {
        $users = $this->userRepository->query()
            ->whereIn('uuid', $uuids)
            ->excludeSuperAdmin()
            ->get();

        foreach ($users as $user) {
            $this->userRepository->update($user, ['status' => $status]);
        }
    }

    /**
     * Export users.
     */
    public function export(string $format, array $filters = []): string
    {
        $users = $this->getAll(10000, $filters);

        $filename = 'users_export_' . now()->format('Y_m_d_H_i_s');

        // Export logic would go here with Maatwebsite\Excel
        // For now, return a placeholder URL
        return url('storage/exports/' . $filename . '.' . $format);
    }

    /**
     * Import users from file.
     */
    public function import(UploadedFile $file): array
    {
        // Import logic would go here with Maatwebsite\Excel
        return [
            'total' => 0,
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];
    }

    /**
     * Get user activities.
     */
    public function getActivities(User $user): array
    {
        return $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->toArray();
    }

    /**
     * Get user login history.
     */
    public function getLoginHistory(User $user): LengthAwarePaginator
    {
        return $user->loginHistories()
            ->orderBy('login_at', 'desc')
            ->paginate(20);
    }
}
