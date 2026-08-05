<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\LoginHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    /**
     * Login user.
     */
    public function login(array $credentials, bool $rememberMe = false, ?string $deviceName = null): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        if ($user->status !== 'active') {
            return ['success' => false, 'message' => 'Your account is ' . $user->status];
        }

        // Create token
        $token = $user->createToken($deviceName ?? 'API');

        // Log login history
        $this->logLogin($user, $deviceName);

        return [
            'success' => true,
            'user' => $user,
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at,
        ];
    }

    /**
     * Logout user.
     */
    public function logout(User $user): void
    {
        // Update login history
        LoginHistory::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->update(['logout_at' => now()]);

        // Revoke current token
        $user->currentAccessToken()->delete();
    }

    /**
     * Refresh token.
     */
    public function refreshToken(User $user): array
    {
        $oldToken = $user->currentAccessToken();

        // Create new token
        $newToken = $user->createToken('refreshed-' . time());

        // Delete old token
        $oldToken->delete();

        return [
            'success' => true,
            'token' => $newToken->plainTextToken,
            'expires_at' => $newToken->accessToken->expires_at,
        ];
    }

    /**
     * Change password.
     */
    public function changePassword(User $user, string $newPassword): void
    {
        $user->password = Hash::make($newPassword);
        $user->save();

        // Revoke all tokens except current
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();
    }

    /**
     * Send password reset link.
     */
    public function sendResetLink(string $email): string
    {
        return Password::sendResetLink(['email' => $email]);
    }

    /**
     * Reset password.
     */
    public function resetPassword(string $email, string $token, string $password): string
    {
        return Password::reset(
            ['email' => $email, 'token' => $token, 'password' => $password],
            function (User $user) use ($password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );
    }

    /**
     * Log login activity.
     */
    private function logLogin(User $user, ?string $deviceName = null): void
    {
        $userAgent = request()->userAgent();
        $browser = $this->parseBrowser($userAgent);
        $platform = $this->parsePlatform($userAgent);

        LoginHistory::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'device_name' => $deviceName,
            'browser' => $browser,
            'platform' => $platform,
            'ip_address' => request()->ip(),
            'login_at' => now(),
            'status' => 'success',
        ]);
    }

    /**
     * Parse browser from user agent.
     */
    private function parseBrowser(?string $userAgent): ?string
    {
        if (!$userAgent) {
            return null;
        }

        $browsers = [
            'Chrome' => 'Chrome',
            'Firefox' => 'Firefox',
            'Safari' => 'Safari',
            'Edge' => 'Edge',
            'Opera' => 'Opera',
            'IE' => 'Internet Explorer',
        ];

        foreach ($browsers as $browser => $name) {
            if (stripos($userAgent, $browser) !== false) {
                return $name;
            }
        }

        return 'Unknown';
    }

    /**
     * Parse platform from user agent.
     */
    private function parsePlatform(?string $userAgent): ?string
    {
        if (!$userAgent) {
            return null;
        }

        $platforms = [
            'Windows' => 'Windows',
            'Mac' => 'macOS',
            'Linux' => 'Linux',
            'Android' => 'Android',
            'iOS' => 'iOS',
        ];

        foreach ($platforms as $platform => $name) {
            if (stripos($userAgent, $platform) !== false) {
                return $name;
            }
        }

        return 'Unknown';
    }

    /**
     * Get active sessions.
     */
    public function getActiveSessions(User $user): array
    {
        return $user->tokens()
            ->where('expires_at', '>', now())
            ->get()
            ->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'created_at' => $token->created_at,
                    'last_used_at' => $token->last_used_at,
                    'expires_at' => $token->expires_at,
                ];
            })
            ->toArray();
    }

    /**
     * Logout from specific session.
     */
    public function logoutSession(User $user, string $sessionId): bool
    {
        $token = $user->tokens()->where('id', $sessionId)->first();

        if (!$token) {
            return false;
        }

        $token->delete();
        return true;
    }

    /**
     * Logout from all devices.
     */
    public function logoutAllDevices(User $user): void
    {
        // Update login history
        LoginHistory::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->update(['logout_at' => now()]);

        // Delete all tokens
        $user->tokens()->delete();
    }
}
