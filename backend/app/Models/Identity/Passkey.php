<?php

declare(strict_types=1);

namespace App\Models\Identity;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Passkey extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'passkeys';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'status',
        'credential_id',
        'public_key',
        'aaguid',
        'device_type',
        'device_os',
        'device_name',
        'browser_name',
        'browser_version',
        'sign_count',
        'backup_eligible',
        'backup_state',
        'resident_key',
        'transports',
        'rp_id',
        'created_at',
        'last_used_at',
        'revoked_at',
        'revoke_reason',
        'metadata',
    ];

    protected $casts = [
        'public_key' => 'encrypted',
        'sign_count' => 'integer',
        'backup_eligible' => 'boolean',
        'backup_state' => 'boolean',
        'resident_key' => 'boolean',
        'created_at' => 'datetime',
        'last_used_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    protected $hidden = [
        'public_key',
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

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPlatformAuthenticator(): bool
    {
        return $this->type === 'platform';
    }

    public function isCrossPlatform(): bool
    {
        return $this->type === 'cross-platform';
    }

    public function supportsTransports(): bool
    {
        return !empty($this->transports);
    }

    public function updateSignCount(int $count): void
    {
        $this->update([
            'sign_count' => $count,
            'last_used_at' => now(),
        ]);
    }

    public function revoke(string $reason): void
    {
        $this->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoke_reason' => $reason,
        ]);
    }

    public function markUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }
}
