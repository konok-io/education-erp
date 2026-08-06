<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservabilitySyntheticTestResult extends Model
{
    use HasUuids, HasUuid;

    protected $table = 'observability_synthetic_test_results';

    protected $fillable = [
        'test_id',
        'status',
        'duration_ms',
        'http_status_code',
        'error_message',
        'response_body',
        'assertion_results',
        'environment',
        'executed_at',
    ];

    protected $casts = [
        'duration_ms' => 'decimal:6',
        'response_body' => 'array',
        'assertion_results' => 'array',
        'executed_at' => 'datetime',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(ObservabilitySyntheticTest::class, 'test_id');
    }

    public function scopeByTest($query, string $testId)
    {
        return $query->where('test_id', $testId);
    }

    public function scopeBetween($query, $start, $end)
    {
        return $query->whereBetween('executed_at', [$start, $end]);
    }

    public function scopeLatest($query, int $limit = 100)
    {
        return $query->orderBy('executed_at', 'desc')->limit($limit);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
