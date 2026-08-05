<?php

declare(strict_types=1);

namespace App\Models\Alumni;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Internship extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'internships';

    protected $fillable = [
        'uuid',
        'internship_number',
        'employer_id',
        'internship_title',
        'description',
        'internship_type',
        'department',
        'location',
        'country',
        'positions',
        'requirements',
        'responsibilities',
        'duration',
        'start_date',
        'end_date',
        'stipend',
        'stipend_currency',
        'is_paid',
        'is_remote',
        'is_active',
        'status',
        'posted_by',
        'published_at',
    ];

    protected $casts = [
        'stipend' => 'decimal:2',
        'positions' => 'integer',
        'is_paid' => 'boolean',
        'is_remote' => 'boolean',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'published_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_FILLED = 'filled';

    // ===================== TYPES =====================
    public const TYPE_PAID = 'paid';
    public const TYPE_UNPAID = 'unpaid';
    public const TYPE_RESEARCH = 'research';
    public const TYPE_INDUSTRIAL = 'industrial';
    public const TYPE_TEACHING = 'teaching';

    // ===================== RELATIONSHIPS =====================

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class, 'employer_id');
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'posted_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(InternshipApplication::class, 'internship_id');
    }

    // ===================== SCOPES =====================

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    // ===================== METHODS =====================

    public static function generateInternshipNumber(): string
    {
        $prefix = 'INT';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public static function types(): array
    {
        return [
            self::TYPE_PAID => 'Paid Internship',
            self::TYPE_UNPAID => 'Unpaid Internship',
            self::TYPE_RESEARCH => 'Research Internship',
            self::TYPE_INDUSTRIAL => 'Industrial Training',
            self::TYPE_TEACHING => 'Teaching Practice',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_FILLED => 'Filled',
        ];
    }
}
