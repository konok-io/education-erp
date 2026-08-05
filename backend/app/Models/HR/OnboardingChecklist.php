<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingChecklist extends Model
{
    use HasUuid;

    protected $table = 'onboarding_checklists';

    protected $fillable = [
        'uuid',
        'checklist_name',
        'category',
        'order',
        'description',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // ===================== CATEGORIES =====================
    public const CATEGORY_ACCOUNT = 'account';
    public const CATEGORY_DOCUMENTS = 'documents';
    public const CATEGORY_EQUIPMENT = 'equipment';
    public const CATEGORY_TRAINING = 'training';
    public const CATEGORY_PAYROLL = 'payroll';

    // ===================== RELATIONSHIPS =====================

    public function completions(): HasMany
    {
        return $this->hasMany(OnboardingCompletion::class, 'checklist_id');
    }

    // ===================== METHODS =====================

    public static function categories(): array
    {
        return [
            self::CATEGORY_ACCOUNT => 'Account Setup',
            self::CATEGORY_DOCUMENTS => 'Documents',
            self::CATEGORY_EQUIPMENT => 'Equipment',
            self::CATEGORY_TRAINING => 'Training',
            self::CATEGORY_PAYROLL => 'Payroll',
        ];
    }

    public static function defaultChecklists(): array
    {
        return [
            ['name' => 'Create User Account', 'category' => self::CATEGORY_ACCOUNT, 'order' => 1],
            ['name' => 'Assign Role/Permission', 'category' => self::CATEGORY_ACCOUNT, 'order' => 2],
            ['name' => 'Create Official Email', 'category' => self::CATEGORY_ACCOUNT, 'order' => 3],
            ['name' => 'Issue Employee ID Card', 'category' => self::CATEGORY_DOCUMENTS, 'order' => 4],
            ['name' => 'Collect Photocopies of Documents', 'category' => self::CATEGORY_DOCUMENTS, 'order' => 5],
            ['name' => 'Biometric Registration', 'category' => self::CATEGORY_DOCUMENTS, 'order' => 6],
            ['name' => 'Laptop/Desktop Assignment', 'category' => self::CATEGORY_EQUIPMENT, 'order' => 7],
            ['name' => 'Mobile Phone Assignment', 'category' => self::CATEGORY_EQUIPMENT, 'order' => 8],
            ['name' => 'Access Card Assignment', 'category' => self::CATEGORY_EQUIPMENT, 'order' => 9],
            ['name' => 'Vehicle/Transport Assignment', 'category' => self::CATEGORY_EQUIPMENT, 'order' => 10],
            ['name' => 'Orientation Training', 'category' => self::CATEGORY_TRAINING, 'order' => 11],
            ['name' => 'Safety Training', 'category' => self::CATEGORY_TRAINING, 'order' => 12],
            ['name' => 'Department-specific Training', 'category' => self::CATEGORY_TRAINING, 'order' => 13],
            ['name' => 'IT System Training', 'category' => self::CATEGORY_TRAINING, 'order' => 14],
            ['name' => 'Setup Payroll', 'category' => self::CATEGORY_PAYROLL, 'order' => 15],
            ['name' => 'Setup Bank Account', 'category' => self::CATEGORY_PAYROLL, 'order' => 16],
            ['name' => 'Setup Provident Fund', 'category' => self::CATEGORY_PAYROLL, 'order' => 17],
            ['name' => 'Assign Leave Policy', 'category' => self::CATEGORY_PAYROLL, 'order' => 18],
        ];
    }
}
