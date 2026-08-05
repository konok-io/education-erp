<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollDetail extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'payroll_details';

    protected $fillable = [
        'uuid',
        'payroll_id',
        'component_type',
        'component_name',
        'amount',
        'is_earning',
        'is_taxable',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_earning' => 'boolean',
        'is_taxable' => 'boolean',
    ];

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class, 'payroll_id');
    }
}
