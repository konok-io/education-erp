<?php

declare(strict_types=1);

namespace App\Models\Identity;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OAuthClient extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'oauth_clients';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'status',
        'client_id',
        'client_secret',
        'grant_types',
        'redirect_uris',
        'logo_url',
        'description',
        'website_url',
        'privacy_policy_url',
        'terms_of_service_url',
        'scopes',
        'default_scopes',
        'pkce_required',
        'refresh_token_rotation',
        'access_token_ttl',
        'refresh_token_ttl',
        'id_token_ttl',
        'user_id',
        'tenant_id',
        'environment',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'grant_types' => 'array',
        'redirect_uris' => 'array',
        'scopes' => 'array',
        'pkce_required' => 'boolean',
        'refresh_token_rotation' => 'boolean',
        'access_token_ttl' => 'integer',
        'refresh_token_ttl' => 'integer',
        'id_token_ttl' => 'integer',
    ];

    protected $hidden = [
        'client_secret',
    ];

    protected $casts = [
        'grant_types' => 'array',
        'redirect_uris' => 'array',
        'scopes' => 'array',
        'pkce_required' => 'boolean',
        'refresh_token_rotation' => 'boolean',
        'access_token_ttl' => 'integer',
        'refresh_token_ttl' => 'integer',
        'id_token_ttl' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(OAuthToken::class, 'client_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPublic(): bool
    {
        return $this->type === 'public';
    }

    public function isConfidential(): bool
    {
        return $this->type === 'confidential';
    }

    public function supportsGrant(string $grantType): bool
    {
        $grants = $this->grant_types ?? [];
        return in_array($grantType, $grants);
    }

    public function supportsPKCE(): bool
    {
        return $this->pkce_required;
    }

    public function allowsRefreshTokenRotation(): bool
    {
        return $this->refresh_token_rotation;
    }

    public function revoke(): void
    {
        $this->update(['status' => 'revoked']);
        $this->tokens()->update(['status' => 'revoked']);
    }
}
