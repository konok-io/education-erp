<?php

declare(strict_types=1);

namespace App\Models\Identity;

use App\Enums\Identity\MFAType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MFAFactor extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'mfa_factors';

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'factor_type',
        'status',
        'phone_number',
        'email',
        'secret',
        'public_key',
        'credential_id',
        'authenticator_type',
        'device_name',
        'device_type',
        'aaguid',
        'sign_count',
        'backup',
        'verified',
        'default',
        'verified_at',
        'last_used_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'secret' => 'encrypted',
        'public_key' => 'encrypted',
        'backup' => 'boolean',
        'verified' => 'boolean',
        'default' => 'boolean',
        'sign_count' => 'integer',
        'verified_at' => 'datetime',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'secret',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->where('verified', true);
    }

    public function scopePrimary($query)
    {
        return $query->where('factor_type', 'primary');
    }

    public function scopeForType($query, MFAType $type)
    {
        return $query->where('type', $type->value);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function isBackup(): bool
    {
        return $this->backup;
    }

    public function verify(): void
    {
        $this->update([
            'verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function setDefault(): void
    {
        $this->update(['default' => true]);
    }

    public function unsetDefault(): void
    {
        $this->update(['default' => false]);
    }

    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);
    }

    public function compromise(): void
    {
        $this->update(['status' => 'compromised']);
    }

    public function updateSignCount(int $count): void
    {
        $this->update(['sign_count' => $count]);
    }

    public function markUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }
}
