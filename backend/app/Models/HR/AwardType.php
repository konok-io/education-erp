<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AwardType extends Model
{
    use HasUuid;

    protected $table = 'award_types';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'code',
        'description',
        'default_reward',
        'is_monetary',
        'is_active',
    ];

    protected $casts = [
        'default_reward' => 'decimal:2',
        'is_monetary' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function awards(): HasMany
    {
        return $this->hasMany(EmployeeAward::class, 'award_type_id');
    }

    // ===================== METHODS =====================

    public static function defaultTypes(): array
    {
        return [
            ['name' => 'Employee of the Month', 'code' => 'EOTM', 'is_monetary' => true],
            ['name' => 'Employee of the Year', 'code' => 'EOTY', 'is_monetary' => true],
            ['name' => 'Best Teacher Award', 'code' => 'BTCH', 'is_monetary' => true],
            ['name' => 'Research Award', 'code' => 'RESA', 'is_monetary' => true],
            ['name' => 'Long Service Award', 'code' => 'LNGS', 'is_monetary' => true],
            ['name' => 'Performance Award', 'code' => 'PERF', 'is_monetary' => true],
            ['name' => 'Innovation Award', 'code' => 'INNO', 'is_monetary' => true],
            ['name' => 'Team Spirit Award', 'code' => 'TEAM', 'is_monetary' => false],
            ['name' => 'Customer Service Award', 'code' => 'CUSA', 'is_monetary' => true],
            ['name' => 'Excellence Award', 'code' => 'EXCL', 'is_monetary' => true],
        ];
    }
}
