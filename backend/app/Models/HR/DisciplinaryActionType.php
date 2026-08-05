<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DisciplinaryActionType extends Model
{
    use HasUuid;

    protected $table = 'disciplinary_action_types';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'code',
        'description',
        'severity_level',
        'requires_investigation',
        'allows_appeal',
        'is_active',
    ];

    protected $casts = [
        'severity_level' => 'integer',
        'requires_investigation' => 'boolean',
        'allows_appeal' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function actions(): HasMany
    {
        return $this->hasMany(DisciplinaryAction::class, 'action_type');
    }

    // ===================== METHODS =====================

    public static function defaultTypes(): array
    {
        return [
            [
                'name' => 'Verbal Warning',
                'code' => 'VWB',
                'severity_level' => 1,
                'requires_investigation' => false,
                'allows_appeal' => false,
            ],
            [
                'name' => 'Written Warning',
                'code' => 'WWN',
                'severity_level' => 2,
                'requires_investigation' => false,
                'allows_appeal' => true,
            ],
            [
                'name' => 'Show Cause Notice',
                'code' => 'SCN',
                'severity_level' => 3,
                'requires_investigation' => true,
                'allows_appeal' => true,
            ],
            [
                'name' => 'Suspension',
                'code' => 'SUSP',
                'severity_level' => 4,
                'requires_investigation' => true,
                'allows_appeal' => true,
            ],
            [
                'name' => 'Fine/Penalty',
                'code' => 'FINE',
                'severity_level' => 3,
                'requires_investigation' => true,
                'allows_appeal' => true,
            ],
            [
                'name' => 'Demotion',
                'code' => 'DMOT',
                'severity_level' => 4,
                'requires_investigation' => true,
                'allows_appeal' => true,
            ],
            [
                'name' => 'Termination',
                'code' => 'TERM',
                'severity_level' => 5,
                'requires_investigation' => true,
                'allows_appeal' => true,
            ],
        ];
    }
}
