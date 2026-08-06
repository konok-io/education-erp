<?php

declare(strict_types=1);

namespace App\Services\Identity;

use App\Models\Identity\UserSession;
use App\Models\User;
use Illuminate\Support\Str;

class SessionService
{
    protected int $accessTokenTTL = 3600;
    protected int $refreshTokenTTL = 1209600;

    public function createSession(User $user, array $data = []): UserSession
    {
        $token = $this->generateToken();
        $refreshToken = $this->generateRefreshToken();

        $session = UserSession::create([
            'user_id' => $user->id,
            'name' => $data['name'] ?? null,
            'device_type' => $data['device_type'] ?? 'web',
            'device_name' => $data['device_name'] ?? null,
            'device_os' => $data['device_os'] ?? null,
            'device_browser' => $data['device_browser'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'location' => $data['location'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'status' => 'active',
            'token' => $token,
            'refresh_token' => $refreshToken,
            'token_expires_at' => now()->addSeconds($this->accessTokenTTL),
            'refresh_expires_at' => now()->addSeconds($this->refreshTokenTTL),
            'login_at' => now(),
            'last_activity_at' => now(),
            'is_current' => false,
            'environment' => $data['environment'] ?? 'production',
        ]);

        return $session;
    }

    public function validateToken(string $token): ?UserSession
    {
        $session = UserSession::where('token', $token)->first();

        if (!$session || !$session->isActive()) {
            return null;
        }

        if ($session->isExpired()) {
            $session->update(['status' => 'expired']);
            return null;
        }

        $session->update(['last_activity_at' => now()]);

        return $session;
    }

    public function getActiveSessionsForUser(string $userId): \Illuminate\Database\Eloquent\Collection
    {
        return UserSession::where('user_id', $userId)
            ->where('status', 'active')
            ->orderBy('last_activity_at', 'desc')
            ->get();
    }

    public function revokeSession(string $sessionId): bool
    {
        $session = UserSession::find($sessionId);
        if (!$session) {
            return false;
        }

        $session->revoke();
        return true;
    }

    public function revokeAllSessionsForUser(string $userId): int
    {
        return UserSession::where('user_id', $userId)
            ->where('status', 'active')
            ->update([
                'status' => 'revoked',
                'logout_at' => now(),
            ]);
    }

    public function cleanupExpiredSessions(): int
    {
        return UserSession::where('status', 'active')
            ->where('token_expires_at', '<', now())
            ->update([
                'status' => 'expired',
            ]);
    }

    public function cleanupInactiveSessions(int $days = 30): int
    {
        return UserSession::where('status', 'active')
            ->where('last_activity_at', '<', now()->subDays($days))
            ->update([
                'status' => 'inactive',
            ]);
    }

    protected function generateToken(): string
    {
        return Str::random(64);
    }

    protected function generateRefreshToken(): string
    {
        return Str::random(64) . '_' . time();
    }
}
