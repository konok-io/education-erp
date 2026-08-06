<?php

declare(strict_types=1);

namespace App\Models\Identity;

use App\Enums\Identity\DeviceTrustLevel;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDevice extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'user_devices';

    protected $fillable = [
        'user_id',
        'device_uuid',
        'name',
        'type',
        'manufacturer',
        'model',
        'os',
        'os_version',
        'browser',
        'browser_version',
        'ip_address',
        'status',
        'trust_level',
        'public_key',
        'certificate',
        'risk_score',
        'is_compliant',
        'has_mfa',
        'last_location',
        'first_seen_at',
        'last_seen_at',
        'blocked_at',
        'block_reason',
        'security_attributes',
        'metadata',
    ];

    protected $casts = [
        'public_key' => 'encrypted',
        'certificate' => 'encrypted',
        'security_attributes' => 'array',
        'metadata' => 'array',
        'risk_score' => 'float',
        'is_compliant' => 'boolean',
        'has_mfa' => 'boolean',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'blocked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTrusted($query)
    {
        return $query->where('trust_level', DeviceTrustLevel::TRUSTED->value);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isTrusted(): bool
    {
        return $this->trust_level === DeviceTrustLevel::TRUSTED->value;
    }

    public function isVerified(): bool
    {
        return $this->trust_level === DeviceTrustLevel::VERIFIED->value;
    }

    public function block(string $reason): void
    {
        $this->update([
            'status' => 'blocked',
            'blocked_at' => now(),
            'block_reason' => $reason,
        ]);
    }

    public function trust(): void
    {
        $this->update([
            'trust_level' => DeviceTrustLevel::TRUSTED->value,
        ]);
    }

    public function verify(): void
    {
        $this->update([
            'trust_level' => DeviceTrustLevel::VERIFIED->value,
        ]);
    }

    public function updateRiskScore(float $score): void
    {
        $this->update(['risk_score' => $score]);
    }

    public function markSeen(): void
    {
        $this->update([
            'last_seen_at' => now(),
        ]);
    }
}
