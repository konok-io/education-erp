<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HostelMessPlan extends Model
{
    use HasFactory;

    protected $table = 'hostel_mess_plans';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'monthly_fee',
        'include_breakfast',
        'include_lunch',
        'include_dinner',
        'is_active',
        'description',
    ];

    protected $casts = [
        'monthly_fee' => 'decimal:2',
        'include_breakfast' => 'boolean',
        'include_lunch' => 'boolean',
        'include_dinner' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(HostelMessSubscription::class, 'mess_plan_id');
    }

    public function getMealCount(): int
    {
        $count = 0;
        if ($this->include_breakfast) $count++;
        if ($this->include_lunch) $count++;
        if ($this->include_dinner) $count++;
        return $count;
    }
}
