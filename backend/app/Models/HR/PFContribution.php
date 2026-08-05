<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PFContribution extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'pf_contributions';

    protected $fillable = [
        'uuid',
        'pf_id',
        'contribution_month',
        'contribution_year',
        'employee_amount',
        'employer_amount',
        'interest_amount',
        'total_amount',
        'remarks',
    ];

    protected $casts = [
        'employee_amount' => 'decimal:2',
        'employer_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // ===================== RELATIONSHIPS =====================

    public function providentFund(): BelongsTo
    {
        return $this->belongsTo(ProvidentFund::class, 'pf_id');
    }
}
