<?php

declare(strict_types=1);

namespace App\Models\Identity;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class APIKey extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'api_keys';

    protected $fillable = [
        'name',
        'slug',
        'status',
        'type',
        'api_key_hash',
        'api_key_prefix',
        'secret_hash',
        'user_id',
        'oauth_client_id',
        'scopes',
        'permissions',
        'rate_limit',
        'expires_at',
        'last_used_at',
        'last_ip_address',
        'request_count',
        'error_count',
        'tenant_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scopes' => 'array',
        'permissions' => 'array',
        'request_count' => 'integer',
        'error_count' => 'integer',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'api_key_hash',
        'secret_hash',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeServiceKeys($query)
    {
        return $query->where('type', 'service');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isServiceKey(): bool
    {
        return $this->type === 'service';
    }

    public function hasScope(string $scope): bool
    {
        $scopes = $this->scopes ?? [];
        return in_array($scope, $scopes);
    }

    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions);
    }

    public function revoke(): void
    {
        $this->update(['status' => 'revoked']);
    }

    public function incrementRequestCount(): void
    {
        $this->increment('request_count');
        $this->update(['last_used_at' => now()]);
    }

    public function incrementErrorCount(): void
    {
        $this->increment('error_count');
    }
}
