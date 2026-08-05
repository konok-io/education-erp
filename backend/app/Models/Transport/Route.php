<?php

declare(strict_types=1);

namespace App\Models\Transport;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'routes';

    protected $fillable = [
        'uuid',
        'route_code',
        'route_name',
        'starting_point',
        'ending_point',
        'distance',
        'estimated_time',
        'monthly_fee',
        'description',
        'status',
        'is_active',
    ];

    protected $casts = [
        'distance' => 'decimal:2',
        'monthly_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    // ===================== RELATIONSHIPS =====================

    public function stops(): HasMany
    {
        return $this->hasMany(RouteStop::class, 'route_id')->orderBy('sequence');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'route_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TransportAssignment::class, 'route_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)->where('is_active', true);
    }

    // ===================== METHODS =====================

    public static function generateRouteCode(): string
    {
        $prefix = 'RTE';
        $count = self::count() + 1;
        return sprintf('%s%03d', $prefix, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    public function getActiveAssignmentsCount(): int
    {
        return $this->assignments()->where('status', 'active')->count();
    }

    public function getTotalStopsCount(): int
    {
        return $this->stops()->count();
    }
}
