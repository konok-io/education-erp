<?php

declare(strict_types=1);

namespace App\Models\Identity;

use App\Enums\Identity\SessionStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSession extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'user_sessions';

    protected $fillable = [
        'user_id',
        'name',
        'device_type',
        'device_name',
        'device_os',
        'device_browser',
        'ip_address',
        'user_agent',
        'location',
        'latitude',
        'longitude',
        'status',
        'token',
        'refresh_token',
        'token_expires_at',
        'refresh_expires_at',
        'last_activity_at',
        'login_at',
        'logout_at',
        'is_current',
        'metadata',
        'environment',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_current' => 'boolean',
        'token_expires_at' => 'datetime',
        'refresh_expires_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    protected $hidden = [
        'token',
        'refresh_token',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', SessionStatus::ACTIVE->value);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isActive(): bool
    {
        return $this->status === SessionStatus::ACTIVE->value;
    }

    public function isExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }

    public function revoke(): void
    {
        $this->update([
            'status' => SessionStatus::REVOKED->value,
            'logout_at' => now(),
        ]);
    }

    public function updateActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    public function markCurrent(): void
    {
        $this->update(['is_current' => true]);
    }
}
