<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\LoginHistory;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends BaseController
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Login user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $credentials['status'] = 'active';

        $result = $this->authService->login(
            $credentials,
            $request->boolean('remember_me'),
            $request->input('device_name', $request->userAgent())
        );

        if (!$result['success']) {
            return $this->error($result['message'], 401);
        }

        return $this->success([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
            'token_type' => 'Bearer',
            'expires_at' => $result['expires_at'],
        ], 'Login successful', 200);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->success(null, 'Logged out successfully');
    }

    /**
     * Get current user.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['role', 'campus']);

        return $this->success(new UserResource($user));
    }

    /**
     * Refresh token.
     */
    public function refresh(Request $request): JsonResponse
    {
        $result = $this->authService->refreshToken($request->user());

        if (!$result['success']) {
            return $this->error($result['message'], 401);
        }

        return $this->success([
            'token' => $result['token'],
            'token_type' => 'Bearer',
            'expires_at' => $result['expires_at'],
        ], 'Token refreshed');
    }

    /**
     * Change password.
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('Current password is incorrect', 400);
        }

        $this->authService->changePassword($user, $request->password);

        return $this->success(null, 'Password changed successfully');
    }

    /**
     * Forgot password.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->sendResetLink($request->email);

        if ($status === Password::INVALID_USER) {
            return $this->error('We cannot find a user with that email address', 404);
        }

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success(null, 'Password reset link sent to your email');
        }

        return $this->error('Unable to send reset link', 500);
    }

    /**
     * Reset password.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->resetPassword(
            $request->email,
            $request->token,
            $request->password
        );

        if ($status !== Password::PASSWORD_RESET) {
            return $this->error('Invalid token or email', 400);
        }

        return $this->success(null, 'Password reset successfully');
    }

    /**
     * Get login history.
     */
    public function loginHistory(Request $request): JsonResponse
    {
        $history = LoginHistory::where('user_id', $request->user()->id)
            ->orderBy('login_at', 'desc')
            ->paginate(20);

        return $this->paginated($history, 'Login history retrieved');
    }

    /**
     * Get active sessions.
     */
    public function activeSessions(Request $request): JsonResponse
    {
        $sessions = $this->authService->getActiveSessions($request->user());

        return $this->success($sessions);
    }

    /**
     * Logout from specific session.
     */
    public function logoutSession(Request $request, string $sessionId): JsonResponse
    {
        $result = $this->authService->logoutSession($request->user(), $sessionId);

        if (!$result) {
            return $this->error('Session not found', 404);
        }

        return $this->success(null, 'Session terminated');
    }

    /**
     * Logout from all devices.
     */
    public function logoutAllDevices(Request $request): JsonResponse
    {
        $this->authService->logoutAllDevices($request->user());

        return $this->success(null, 'Logged out from all devices');
    }
}
