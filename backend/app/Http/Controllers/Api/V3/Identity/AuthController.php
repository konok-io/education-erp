<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\Identity;

use App\Http\Controllers\Controller;
use App\Services\Identity\AuthenticationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct(
        protected AuthenticationService $authService
    ) {}

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'device_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->authService->login(
            email: $request->input('email'),
            password: $request->input('password'),
            deviceType: $this->detectDeviceType($request->userAgent()),
            deviceName: $request->input('device_name'),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent()
        );

        return response()->json([
            'success' => $result->success,
            'message' => $result->message,
            'data' => $result->success ? [
                'access_token' => $result->access_token,
                'refresh_token' => $result->refresh_token,
                'token_type' => $result->token_type,
                'expires_in' => $result->expires_in,
                'user' => $result->user,
            ] : null,
            'mfa_required' => $result->mfa_required,
        ], $result->success ? 200 : 401);
    }

    public function loginWithMFA(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'mfa_code' => 'required|string',
            'mfa_factor_id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->authService->loginWithMFA(
            email: $request->input('email'),
            password: $request->input('password'),
            mfaCode: $request->input('mfa_code'),
            mfaFactorId: $request->input('mfa_factor_id'),
            deviceType: $this->detectDeviceType($request->userAgent()),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent()
        );

        return response()->json([
            'success' => $result->success,
            'message' => $result->message,
            'data' => $result->success ? [
                'access_token' => $result->access_token,
                'refresh_token' => $result->refresh_token,
                'token_type' => $result->token_type,
                'expires_in' => $result->expires_in,
                'user' => $result->user,
            ] : null,
        ], $result->success ? 200 : 401);
    }

    public function logout(Request $request): JsonResponse
    {
        $sessionId = $request->user()?->id;

        $this->authService->logout($sessionId);

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $count = $this->authService->logoutAllSessions($userId);

        return response()->json([
            'success' => true,
            'message' => "Logged out from {$count} sessions",
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->authService->refreshToken($request->input('refresh_token'));

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired refresh token',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'access_token' => $result->access_token,
                'refresh_token' => $result->refresh_token,
                'token_type' => $result->token_type,
                'expires_in' => $result->expires_in,
                'user' => $result->user,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'tenant_id' => $user->tenant_id,
            ],
        ]);
    }

    protected function detectDeviceType(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'web';
        }

        if (preg_match('/Android/i', $userAgent)) {
            return 'mobile';
        }
        if (preg_match('/iOS|iPhone|iPad/i', $userAgent)) {
            return 'mobile';
        }
        if (preg_match('/electron/i', $userAgent)) {
            return 'desktop';
        }

        return 'web';
    }
}
