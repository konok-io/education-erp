<?php

declare(strict_types=1);

namespace App\Models\Payment;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeCategory extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'fee_categories';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'code',
        'category_type',
        'is_system',
        'is_active',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ===================== CATEGORY TYPES =====================
    public const TYPE_ADMISSION = 'admission';
    public const TYPE_REGISTRATION = 'registration';
    public const TYPE_TUITION = 'tuition';
    public const TYPE_EXAM = 'exam';
    public const TYPE_LIBRARY = 'library';
    public const TYPE_LABORATORY = 'laboratory';
    public const TYPE_SPORTS = 'sports';
    public const TYPE_TRANSPORT = 'transport';
    public const TYPE_HOSTEL = 'hostel';
    public const TYPE_CERTIFICATE = 'certificate';
    public const TYPE_DEVELOPMENT = 'development';
    public const TYPE_FINE = 'fine';
    public const TYPE_MISC = 'miscellaneous';

    public function feeStructures(): HasMany
    {
        return $this->hasMany(FeeStructure::class, 'category_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'category_id');
    }

    public static function categoryTypes(): array
    {
        return [
            self::TYPE_ADMISSION => 'Admission Fee',
            self::TYPE_REGISTRATION => 'Registration Fee',
            self::TYPE_TUITION => 'Tuition Fee',
            self::TYPE_EXAM => 'Exam Fee',
            self::TYPE_LIBRARY => 'Library Fee',
            self::TYPE_LABORATORY => 'Laboratory Fee',
            self::TYPE_SPORTS => 'Sports Fee',
            self::TYPE_TRANSPORT => 'Transport Fee',
            self::TYPE_HOSTEL => 'Hostel Fee',
            self::TYPE_CERTIFICATE => 'Certificate Fee',
            self::TYPE_DEVELOPMENT => 'Development Fee',
            self::TYPE_FINE => 'Fine',
            self::TYPE_MISC => 'Miscellaneous',
        ];
    }

    public static function getSystemCategories(): array
    {
        return [
            self::TYPE_ADMISSION,
            self::TYPE_REGISTRATION,
            self::TYPE_TUITION,
            self::TYPE_EXAM,
        ];
    }
}
