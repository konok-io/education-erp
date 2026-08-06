<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\Identity;

use App\Http\Controllers\Controller;
use App\Models\Identity\UserSession;
use App\Services\Identity\SessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function __construct(
        protected SessionService $sessionService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $sessions = $this->sessionService->getActiveSessionsForUser($userId);

        return response()->json([
            'success' => true,
            'data' => $sessions->map(fn($session) => [
                'id' => $session->id,
                'name' => $session->name,
                'device_type' => $session->device_type,
                'device_name' => $session->device_name,
                'device_os' => $session->device_os,
                'device_browser' => $session->device_browser,
                'ip_address' => $session->ip_address,
                'location' => $session->location,
                'status' => $session->status,
                'is_current' => $session->is_current,
                'login_at' => $session->login_at?->toIso8601String(),
                'last_activity_at' => $session->last_activity_at?->toIso8601String(),
            ]),
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $session = UserSession::find($id);

        if (!$session || $session->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $session->id,
                'name' => $session->name,
                'device_type' => $session->device_type,
                'device_name' => $session->device_name,
                'device_os' => $session->device_os,
                'device_browser' => $session->device_browser,
                'ip_address' => $session->ip_address,
                'location' => $session->location,
                'status' => $session->status,
                'is_current' => $session->is_current,
                'login_at' => $session->login_at?->toIso8601String(),
                'last_activity_at' => $session->last_activity_at?->toIso8601String(),
                'token_expires_at' => $session->token_expires_at?->toIso8601String(),
            ],
        ]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $session = UserSession::find($id);

        if (!$session || $session->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found',
            ], 404);
        }

        $this->sessionService->revokeSession($id);

        return response()->json([
            'success' => true,
            'message' => 'Session revoked successfully',
        ]);
    }

    public function destroyAll(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $count = $this->sessionService->revokeAllSessionsForUser($userId);

        return response()->json([
            'success' => true,
            'message' => "Revoked {$count} sessions",
        ]);
    }
}
