<?php

declare(strict_types=1);

namespace App\Models\Identity;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class IdentityAuditLog extends Model
{
    use HasUuids;

    protected $table = 'identity_audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'event_type',
        'severity',
        'category',
        'user_id',
        'user_email',
        'session_id',
        'device_id',
        'identity_provider_id',
        'ip_address',
        'user_agent',
        'location',
        'description',
        'event_data',
        'old_values',
        'new_values',
        'success',
        'failure_reason',
        'environment',
        'region',
        'occurred_at',
    ];

    protected $casts = [
        'event_data' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
        'success' => 'boolean',
        'occurred_at' => 'datetime',
    ];

    // Event types
    public const EVENT_LOGIN = 'login';
    public const EVENT_LOGOUT = 'logout';
    public const EVENT_LOGIN_FAILED = 'login_failed';
    public const EVENT_MFA_ENABLED = 'mfa_enabled';
    public const EVENT_MFA_DISABLED = 'mfa_disabled';
    public const EVENT_MFA_VERIFIED = 'mfa_verified';
    public const EVENT_PASSWORD_CHANGED = 'password_changed';
    public const EVENT_PASSWORD_RESET = 'password_reset';
    public const EVENT_SESSION_CREATED = 'session_created';
    public const EVENT_SESSION_REVOKED = 'session_revoked';
    public const EVENT_DEVICE_BLOCKED = 'device_blocked';
    public const EVENT_DEVICE_TRUSTED = 'device_trusted';
    public const EVENT_ROLE_UPDATED = 'role_updated';
    public const EVENT_PERMISSION_UPDATED = 'permission_updated';
    public const EVENT_IDENTITY_LINKED = 'identity_linked';
    public const EVENT_IDENTITY_UNLINKED = 'identity_unlinked';
    public const EVENT_PASSKEY_REGISTERED = 'passkey_registered';
    public const EVENT_PASSKEY_REVOKED = 'passkey_revoked';
    public const EVENT_TOKEN_REVOKED = 'token_revoked';
    public const EVENT_SUSPICIOUS_ACTIVITY = 'suspicious_activity';

    // Categories
    public const CATEGORY_AUTHENTICATION = 'authentication';
    public const CATEGORY_AUTHORIZATION = 'authorization';
    public const CATEGORY_IDENTITY = 'identity';
    public const CATEGORY_DEVICE = 'device';
    public const CATEGORY_MFA = 'mfa';

    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByIP($query, string $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    public function scopeLatest($query, int $limit = 100)
    {
        return $query->orderBy('occurred_at', 'desc')->limit($limit);
    }

    public static function log(
        string $eventType,
        string $severity,
        string $category,
        string $description,
        ?string $userId = null,
        ?string $userEmail = null,
        bool $success = true,
        ?string $failureReason = null,
        ?array $eventData = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $sessionId = null,
        ?string $deviceId = null,
        ?string $identityProviderId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?string $location = null,
        string $environment = 'production',
        ?string $region = null
    ): self {
        return self::create([
            'event_type' => $eventType,
            'severity' => $severity,
            'category' => $category,
            'user_id' => $userId,
            'user_email' => $userEmail,
            'session_id' => $sessionId,
            'device_id' => $deviceId,
            'identity_provider_id' => $identityProviderId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'location' => $location,
            'description' => $description,
            'event_data' => $eventData,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'success' => $success,
            'failure_reason' => $failureReason,
            'environment' => $environment,
            'region' => $region,
            'occurred_at' => now(),
        ]);
    }
}
