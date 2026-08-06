<?php

declare(strict_types=1);

namespace App\Models\Identity;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OAuthToken extends Model
{
    use HasUuids, HasUuid;

    protected $table = 'oauth_tokens';

    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'user_id',
        'name',
        'type',
        'status',
        'token',
        'token_identifier',
        'hashed_token',
        'scope',
        'scopes',
        'code',
        'code_challenge',
        'code_challenge_method',
        'state',
        'nonce',
        'redirect_uri',
        'token_expires_at',
        'refresh_expires_at',
        'issued_at',
        'revoked_at',
        'revoke_reason',
        'revoked_by',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'scopes' => 'array',
        'token_expires_at' => 'datetime',
        'refresh_expires_at' => 'datetime',
        'issued_at' => 'datetime',
        'revoked_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected $hidden = [
        'token',
        'code',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(OAuthClient::class, 'client_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAccessTokens($query)
    {
        return $query->where('type', 'access_token');
    }

    public function scopeRefreshTokens($query)
    {
        return $query->where('type', 'refresh_token');
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForClient($query, string $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }

    public function isAccessToken(): bool
    {
        return $this->type === 'access_token';
    }

    public function isRefreshToken(): bool
    {
        return $this->type === 'refresh_token';
    }

    public function revoke(string $reason = null, ?string $revokedBy = null): void
    {
        $this->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoke_reason' => $reason,
            'revoked_by' => $revokedBy,
        ]);
    }

    public function hasScope(string $scope): bool
    {
        $scopes = $this->scopes ?? [];
        return in_array($scope, $scopes);
    }

    public function scopesContain(array $requestedScopes): bool
    {
        $tokenScopes = $this->scopes ?? [];
        foreach ($requestedScopes as $scope) {
            if (!in_array($scope, $tokenScopes)) {
                return false;
            }
        }
        return true;
    }
}
