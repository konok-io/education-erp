<?php

declare(strict_types=1);

namespace App\Services\Identity;

use App\DTO\Identity\AuthResultDTO;
use App\Enums\Identity\AuthMethod;
use App\Models\Identity\IdentityAuditLog;
use App\Models\Identity\UserSession;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthenticationService
{
    protected int $accessTokenTTL = 3600;
    protected int $refreshTokenTTL = 1209600;

    public function __construct(
        protected SessionService $sessionService,
        protected MFAService $mfaService
    ) {}

    public function login(
        string $email,
        string $password,
        string $deviceType = 'web',
        ?string $deviceName = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): AuthResultDTO {
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->logFailedLogin($email, 'User not found', $ipAddress, $userAgent);
            return AuthResultDTO::failure('Invalid credentials');
        }

        if (!Hash::check($password, $user->password)) {
            $this->logFailedLogin($email, 'Invalid password', $ipAddress, $userAgent, $user->id);
            return AuthResultDTO::failure('Invalid credentials');
        }

        if (!$user->is_active) {
            $this->logFailedLogin($email, 'User inactive', $ipAddress, $userAgent, $user->id);
            return AuthResultDTO::failure('Account is inactive');
        }

        // Check if MFA is required
        $mfaFactors = $this->mfaService->getActiveFactorsForUser($user->id);
        if ($mfaFactors->isNotEmpty()) {
            return AuthResultDTO::mfaRequired([
                'factors' => $mfaFactors->map(fn($f) => [
                    'id' => $f->id,
                    'type' => $f->type,
                    'name' => $f->name,
                    'is_backup' => $f->backup,
                ])->toArray(),
                'user_id' => $user->id,
            ]);
        }

        return $this->createAuthenticatedSession($user, $deviceType, $deviceName, $ipAddress, $userAgent);
    }

    public function loginWithMFA(
        string $email,
        string $password,
        string $mfaCode,
        string $mfaFactorId,
        string $deviceType = 'web',
        ?string $deviceName = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): AuthResultDTO {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return AuthResultDTO::failure('Invalid credentials');
        }

        if (!Hash::check($password, $user->password)) {
            return AuthResultDTO::failure('Invalid credentials');
        }

        $mfaFactor = $this->mfaService->getFactorById($mfaFactorId);
        if (!$mfaFactor || $mfaFactor->user_id !== $user->id) {
            return AuthResultDTO::failure('Invalid MFA factor');
        }

        if (!$this->mfaService->verifyCode($mfaFactor, $mfaCode)) {
            $this->logFailedLogin($email, 'Invalid MFA code', $ipAddress, $userAgent, $user->id);
            return AuthResultDTO::failure('Invalid MFA code');
        }

        return $this->createAuthenticatedSession($user, $deviceType, $deviceName, $ipAddress, $userAgent);
    }

    protected function createAuthenticatedSession(
        User $user,
        string $deviceType,
        ?string $deviceName,
        ?string $ipAddress,
        ?string $userAgent
    ): AuthResultDTO {
        $session = $this->sessionService->createSession($user, [
            'device_type' => $deviceType,
            'device_name' => $deviceName ?? 'Unknown Device',
            'device_os' => $this->parseOS($userAgent),
            'device_browser' => $this->parseBrowser($userAgent),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        $this->logSuccessfulLogin($user, $session, $ipAddress);

        return AuthResultDTO::success(
            accessToken: $session->token,
            refreshToken: $session->refresh_token,
            expiresIn: $this->accessTokenTTL,
            userId: $user->id,
            sessionId: $session->id,
            user: [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            metadata: [
                'login_at' => $session->login_at->toIso8601String(),
            ]
        );
    }

    public function logout(string $sessionId, ?string $userId = null): bool
    {
        $session = UserSession::find($sessionId);
        if (!$session) {
            return false;
        }

        $session->revoke();
        $this->logLogout($session);

        return true;
    }

    public function logoutAllSessions(string $userId): int
    {
        $count = UserSession::where('user_id', $userId)
            ->where('status', 'active')
            ->update([
                'status' => 'revoked',
                'logout_at' => now(),
            ]);

        IdentityAuditLog::log(
            eventType: IdentityAuditLog::EVENT_SESSION_REVOKED,
            severity: 'info',
            category: IdentityAuditLog::CATEGORY_AUTHENTICATION,
            description: "All sessions revoked for user",
            userId: $userId,
            success: true,
        );

        return $count;
    }

    public function refreshToken(string $refreshToken): ?AuthResultDTO
    {
        $session = UserSession::where('refresh_token', $refreshToken)->first();

        if (!$session || !$session->isActive()) {
            return null;
        }

        if ($session->refresh_expires_at && $session->refresh_expires_at->isPast()) {
            return null;
        }

        $newToken = Str::random(64);
        $newRefreshToken = Str::random(64);

        $session->update([
            'token' => $newToken,
            'refresh_token' => $newRefreshToken,
            'token_expires_at' => now()->addSeconds($this->accessTokenTTL),
            'refresh_expires_at' => now()->addSeconds($this->refreshTokenTTL),
            'last_activity_at' => now(),
        ]);

        return AuthResultDTO::success(
            accessToken: $newToken,
            refreshToken: $newRefreshToken,
            expiresIn: $this->accessTokenTTL,
            userId: $session->user_id,
            sessionId: $session->id,
            user: $session->user ? [
                'id' => $session->user->id,
                'name' => $session->user->name,
                'email' => $session->user->email,
                'role' => $session->user->role,
            ] : [],
        );
    }

    protected function logSuccessfulLogin(User $user, UserSession $session, ?string $ipAddress): void
    {
        IdentityAuditLog::log(
            eventType: IdentityAuditLog::EVENT_LOGIN,
            severity: 'info',
            category: IdentityAuditLog::CATEGORY_AUTHENTICATION,
            description: 'User logged in successfully',
            userId: $user->id,
            userEmail: $user->email,
            sessionId: $session->id,
            success: true,
            eventData: [
                'method' => AuthMethod::PASSWORD->value,
                'device_type' => $session->device_type,
            ],
            ipAddress: $ipAddress,
            userAgent: $session->user_agent,
        );
    }

    protected function logFailedLogin(
        string $email,
        string $reason,
        ?string $ipAddress,
        ?string $userAgent,
        ?string $userId = null
    ): void {
        IdentityAuditLog::log(
            eventType: IdentityAuditLog::EVENT_LOGIN_FAILED,
            severity: 'warning',
            category: IdentityAuditLog::CATEGORY_AUTHENTICATION,
            description: "Login failed: {$reason}",
            userId: $userId,
            userEmail: $email,
            success: false,
            failureReason: $reason,
            eventData: [
                'reason' => $reason,
            ],
            ipAddress: $ipAddress,
            userAgent: $userAgent,
        );
    }

    protected function logLogout(UserSession $session): void
    {
        IdentityAuditLog::log(
            eventType: IdentityAuditLog::EVENT_LOGOUT,
            severity: 'info',
            category: IdentityAuditLog::CATEGORY_AUTHENTICATION,
            description: 'User logged out',
            userId: $session->user_id,
            sessionId: $session->id,
            success: true,
            ipAddress: $session->ip_address,
            userAgent: $session->user_agent,
        );
    }

    protected function parseOS(?string $userAgent): ?string
    {
        if (!$userAgent) {
            return null;
        }

        if (preg_match('/Windows NT 10/i', $userAgent)) {
            return 'Windows 10/11';
        }
        if (preg_match('/Windows/i', $userAgent)) {
            return 'Windows';
        }
        if (preg_match('/Mac OS X/i', $userAgent)) {
            return 'macOS';
        }
        if (preg_match('/Linux/i', $userAgent)) {
            return 'Linux';
        }
        if (preg_match('/Android/i', $userAgent)) {
            return 'Android';
        }
        if (preg_match('/iOS|iPhone|iPad/i', $userAgent)) {
            return 'iOS';
        }

        return 'Unknown';
    }

    protected function parseBrowser(?string $userAgent): ?string
    {
        if (!$userAgent) {
            return null;
        }

        if (preg_match('/Chrome/i', $userAgent) && !preg_match('/Edge/i', $userAgent)) {
            return 'Chrome';
        }
        if (preg_match('/Firefox/i', $userAgent)) {
            return 'Firefox';
        }
        if (preg_match('/Safari/i', $userAgent) && !preg_match('/Chrome/i', $userAgent)) {
            return 'Safari';
        }
        if (preg_match('/Edge/i', $userAgent)) {
            return 'Edge';
        }

        return 'Unknown';
    }
}
