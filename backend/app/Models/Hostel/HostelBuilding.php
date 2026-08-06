<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HostelBuilding extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hostel_buildings';

    protected $fillable = [
        'uuid',
        'building_code',
        'name',
        'name_bn',
        'campus_id',
        'gender',
        'total_floors',
        'total_rooms',
        'total_beds',
        'address',
        'description',
        'status',
    ];

    protected $casts = [
        'total_floors' => 'integer',
        'total_rooms' => 'integer',
        'total_beds' => 'integer',
    ];

    public function floors(): HasMany
    {
        return $this->hasMany(HostelFloor::class, 'building_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(HostelRoom::class, 'building_id');
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(HostelAdmission::class, 'building_id');
    }

    public function visitors(): HasMany
    {
        return $this->hasMany(HostelVisitor::class, 'building_id');
    }

    public function getOccupiedBeds(): int
    {
        return $this->rooms()
            ->withCount(['beds' => function ($query) {
                $query->where('status', 'occupied');
            }])
            ->get()
            ->sum('beds_count');
    }

    public function getAvailableBeds(): int
    {
        return $this->total_beds - $this->getOccupiedBeds();
    }
}
