<?php

declare(strict_types=1);

namespace App\Models\Alumni;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employer extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'employers';

    protected $fillable = [
        'uuid',
        'company_name',
        'company_code',
        'industry',
        'description',
        'website',
        'logo',
        'contact_person',
        'contact_designation',
        'email',
        'phone',
        'country',
        'city',
        'address',
        'company_size',
        'company_type',
        'founded_year',
        'social_links',
        'is_verified',
        'is_featured',
        'status',
        'user_id',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'social_links' => 'array',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'verified_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    // ===================== COMPANY SIZES =====================
    public const SIZE_STARTUP = '1-10';
    public const SIZE_SMALL = '11-50';
    public const SIZE_MEDIUM = '51-200';
    public const SIZE_LARGE = '201-500';
    public const SIZE_ENTERPRISE = '501-1000';
    public const SIZE_CORPORATION = '1000+';

    // ===================== RELATIONSHIPS =====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'employer_id');
    }

    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class, 'employer_id');
    }

    public function placements(): HasMany
    {
        return $this->hasMany(Placement::class, 'employer_id');
    }

    // ===================== SCOPES =====================

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // ===================== METHODS =====================

    public static function generateCompanyCode(): string
    {
        return 'EMP-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }

    public static function companySizes(): array
    {
        return [
            self::SIZE_STARTUP => '1-10 employees',
            self::SIZE_SMALL => '11-50 employees',
            self::SIZE_MEDIUM => '51-200 employees',
            self::SIZE_LARGE => '201-500 employees',
            self::SIZE_ENTERPRISE => '501-1000 employees',
            self::SIZE_CORPORATION => '1000+ employees',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    public function verify(int $userId): void
    {
        $this->update([
            'is_verified' => true,
            'verified_by' => $userId,
            'verified_at' => now(),
        ]);
    }
}
