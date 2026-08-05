<?php

declare(strict_types=1);

namespace App\Models\Alumni;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundraisingCampaign extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'fundraising_campaigns';

    protected $fillable = [
        'uuid',
        'campaign_code',
        'campaign_title',
        'description',
        'banner_image',
        'goal_amount',
        'raised_amount',
        'currency',
        'fund_category',
        'start_date',
        'end_date',
        'donor_count',
        'is_featured',
        'is_active',
        'status',
        'created_by',
    ];

    protected $casts = [
        'goal_amount' => 'decimal:2',
        'raised_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'donor_count' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== RELATIONSHIPS =====================

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'campaign_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // ===================== METHODS =====================

    public static function generateCampaignCode(): string
    {
        return 'CMP-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function getProgressPercentage(): float
    {
        if ($this->goal_amount <= 0) {
            return 0;
        }
        return min(100, ($this->raised_amount / $this->goal_amount) * 100);
    }

    public function updateRaisedAmount(): void
    {
        $total = $this->donations()
            ->where('payment_status', Donation::PAYMENT_COMPLETED)
            ->sum('amount');
        
        $count = $this->donations()
            ->where('payment_status', Donation::PAYMENT_COMPLETED)
            ->count();

        $this->update([
            'raised_amount' => $total,
            'donor_count' => $count,
        ]);
    }
}
