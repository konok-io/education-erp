<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\ImportUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends BaseController
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Display a listing of users.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $users = $this->userService->getAll(
            perPage: $request->input('per_page', 20),
            filters: $request->only(['search', 'role', 'status', 'campus_id']),
            sortBy: $request->input('sort_by', 'created_at'),
            sortOrder: $request->input('sort_order', 'desc')
        );

        return UserResource::collection($users);
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return $this->created(
            new UserResource($user->load(['role', 'campus'])),
            'User created successfully'
        );
    }

    /**
     * Display the specified user.
     */
    public function show(string $uuid): JsonResponse
    {
        $user = $this->userService->findByUuid($uuid);

        if (!$user) {
            return $this->notFound('User not found');
        }

        return $this->success(new UserResource($user->load(['role', 'campus'])));
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, string $uuid): JsonResponse
    {
        $user = $this->userService->findByUuid($uuid);

        if (!$user) {
            return $this->notFound('User not found');
        }

        $updatedUser = $this->userService->update($user, $request->validated());

        return $this->updated(
            new UserResource($updatedUser->load(['role', 'campus'])),
            'User updated successfully'
        );
    }

    /**
     * Remove the specified user.
     */
    public function destroy(string $uuid): JsonResponse
    {
        $user = $this->userService->findByUuid($uuid);

        if (!$user) {
            return $this->notFound('User not found');
        }

        $this->userService->delete($user);

        return $this->deleted('User deleted successfully');
    }

    /**
     * Update user avatar.
     */
    public function updateAvatar(Request $request, string $uuid): JsonResponse
    {
        $user = $this->userService->findByUuid($uuid);

        if (!$user) {
            return $this->notFound('User not found');
        }

        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $avatarPath = $this->userService->updateAvatar($user, $request->file('avatar'));

        return $this->success(['avatar' => $avatarPath], 'Avatar updated successfully');
    }

    /**
     * Change user password (admin).
     */
    public function changePassword(Request $request, string $uuid): JsonResponse
    {
        $user = $this->userService->findByUuid($uuid);

        if (!$user) {
            return $this->notFound('User not found');
        }

        $request->validate([
            'password' => [
                'required',
                'string',
                'min:12',
                'max:255',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                'confirmed',
            ],
        ]);

        $this->userService->changePassword($user, $request->password);

        return $this->success(null, 'Password changed successfully');
    }

    /**
     * Update user status.
     */
    public function updateStatus(Request $request, string $uuid): JsonResponse
    {
        $user = $this->userService->findByUuid($uuid);

        if (!$user) {
            return $this->notFound('User not found');
        }

        $request->validate([
            'status' => ['required', 'string', 'in:active,inactive,blocked,suspended'],
        ]);

        $this->userService->updateStatus($user, $request->status);

        return $this->success(null, 'Status updated successfully');
    }

    /**
     * Assign role to user.
     */
    public function assignRole(Request $request, string $uuid): JsonResponse
    {
        $user = $this->userService->findByUuid($uuid);

        if (!$user) {
            return $this->notFound('User not found');
        }

        $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $this->userService->assignRole($user, $request->role_id);

        return $this->success(null, 'Role assigned successfully');
    }

    /**
     * Search users.
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $users = $this->userService->search(
            query: $request->input('q'),
            perPage: $request->input('per_page', 20)
        );

        return UserResource::collection($users);
    }

    /**
     * Bulk update status.
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['required', 'uuid', 'exists:users,uuid'],
            'status' => ['required', 'string', 'in:active,inactive,blocked,suspended'],
        ]);

        $this->userService->bulkUpdateStatus($request->user_ids, $request->status);

        return $this->success(null, 'Status updated successfully');
    }

    /**
     * Export users.
     */
    public function export(Request $request): JsonResponse
    {
        $format = $request->input('format', 'excel');
        $filters = $request->only(['role', 'status', 'campus_id']);

        $downloadUrl = $this->userService->export($format, $filters);

        return $this->success(['url' => $downloadUrl], 'Export ready');
    }

    /**
     * Import users.
     */
    public function import(ImportUserRequest $request): JsonResponse
    {
        $result = $this->userService->import($request->file('file'));

        return $this->success($result, 'Import completed');
    }

    /**
     * Get user activities.
     */
    public function activities(string $uuid): JsonResponse
    {
        $user = $this->userService->findByUuid($uuid);

        if (!$user) {
            return $this->notFound('User not found');
        }

        $activities = $this->userService->getActivities($user);

        return $this->success($activities);
    }

    /**
     * Get user login history.
     */
    public function loginHistory(string $uuid): JsonResponse
    {
        $user = $this->userService->findByUuid($uuid);

        if (!$user) {
            return $this->notFound('User not found');
        }

        $history = $this->userService->getLoginHistory($user);

        return $this->paginated($history, 'Login history retrieved');
    }
}
