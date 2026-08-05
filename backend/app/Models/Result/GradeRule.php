<?php

declare(strict_types=1);

namespace App\Models\Result;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeRule extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'grade_rules';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'scale_type',
        'min_percentage',
        'max_percentage',
        'grade',
        'grade_point',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'min_percentage' => 'decimal:2',
        'max_percentage' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    // ===================== SCALE TYPES =====================
    public const SCALE_FIVE = '5.00';
    public const SCALE_FOUR = '4.00';
    public const SCALE_GPA = 'gpa';

    public function ranges(): HasMany
    {
        return $this->hasMany(GradeRange::class, 'grade_rule_id');
    }

    public function calculateGrade(float $percentage): array
    {
        $range = $this->ranges()
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();

        if ($range) {
            return [
                'grade' => $range->grade,
                'point' => (float) $range->grade_point,
            ];
        }

        return ['grade' => 'F', 'point' => 0.00];
    }

    public static function getDefault(): ?self
    {
        return self::where('is_default', true)->first();
    }

    public static function scales(): array
    {
        return [
            self::SCALE_FIVE => '5.00 Scale',
            self::SCALE_FOUR => '4.00 Scale',
            self::SCALE_GPA => 'GPA System',
        ];
    }
}
