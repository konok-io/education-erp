<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingType extends Model
{
    use HasUuid;

    protected $table = 'training_types';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function trainings(): HasMany
    {
        return $this->hasMany(TrainingRecord::class, 'training_type_id');
    }

    // ===================== METHODS =====================

    public static function defaultTypes(): array
    {
        return [
            ['name' => 'Orientation', 'code' => 'ORIENT'],
            ['name' => 'Technical', 'code' => 'TECH'],
            ['name' => 'Leadership', 'code' => 'LEAD'],
            ['name' => 'Communication', 'code' => 'COMM'],
            ['name' => 'Safety', 'code' => 'SAFETY'],
            ['name' => 'IT System', 'code' => 'ITSYS'],
            ['name' => 'Compliance', 'code' => 'COMPLY'],
            ['name' => 'Customer Service', 'code' => 'CUSVC'],
            ['name' => 'Product Knowledge', 'code' => 'PROD'],
            ['name' => 'Professional Development', 'code' => 'PROF'],
        ];
    }
}
