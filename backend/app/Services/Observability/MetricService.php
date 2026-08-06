<?php

declare(strict_types=1);

namespace App\Services\Observability;

use App\Enums\Observability\MetricType;
use App\Models\Observability\ObservabilityMetric;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class MetricService
{
    public function index(array $filters = [], int $perPage = 100): LengthAwarePaginator
    {
        $query = ObservabilityMetric::with('service');

        if (isset($filters['service_id'])) {
            $query->where('service_id', $filters['service_id']);
        }

        if (isset($filters['name'])) {
            $query->where('name', $filters['name']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['environment'])) {
            $query->where('environment', $filters['environment']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->between($filters['start_date'], $filters['end_date']);
        } elseif (isset($filters['start_date'])) {
            $query->where('timestamp', '>=', $filters['start_date']);
        } elseif (isset($filters['end_date'])) {
            $query->where('timestamp', '<=', $filters['end_date']);
        }

        $query->orderBy('timestamp', 'desc');

        return $query->paginate($perPage);
    }

    public function getLatest(string $serviceId, string $metricName, int $limit = 100): Collection
    {
        return ObservabilityMetric::byService($serviceId)
            ->byName($metricName)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getTimeSeries(
        string $serviceId,
        string $metricName,
        string $startDate,
        string $endDate,
        int $interval = 60
    ): array {
        $metrics = ObservabilityMetric::byService($serviceId)
            ->byName($metricName)
            ->between($startDate, $endDate)
            ->orderBy('timestamp', 'asc')
            ->get();

        $buckets = [];
        foreach ($metrics as $metric) {
            $bucketKey = floor($metric->timestamp->timestamp / $interval) * $interval;
            if (!isset($buckets[$bucketKey])) {
                $buckets[$bucketKey] = [
                    'timestamp' => date('c', $bucketKey),
                    'values' => [],
                ];
            }
            $buckets[$bucketKey]['values'][] = (float) $metric->value;
        }

        return array_values(array_map(function ($bucket) {
            $bucket['count'] = count($bucket['values']);
            $bucket['avg'] = array_sum($bucket['values']) / $bucket['count'];
            $bucket['min'] = min($bucket['values']);
            $bucket['max'] = max($bucket['values']);
            return $bucket;
        }, $buckets));
    }

    public function create(array $data): ObservabilityMetric
    {
        return ObservabilityMetric::create($data);
    }

    public function recordMetric(
        string $serviceId,
        string $name,
        float $value,
        MetricType $type = MetricType::GAUGE,
        ?string $unit = null,
        ?array $labels = null,
        string $environment = 'production'
    ): ObservabilityMetric {
        return $this->create([
            'service_id' => $serviceId,
            'name' => $name,
            'type' => $type->value,
            'value' => $value,
            'unit' => $unit,
            'labels' => $labels,
            'environment' => $environment,
            'timestamp' => now(),
        ]);
    }

    public function getAggregates(
        string $serviceId,
        string $metricName,
        string $startDate,
        string $endDate
    ): array {
        $result = ObservabilityMetric::byService($serviceId)
            ->byName($metricName)
            ->between($startDate, $endDate)
            ->selectRaw('
                COUNT(*) as count,
                AVG(value) as avg,
                MIN(value) as min,
                MAX(value) as max,
                SUM(value) as sum
            ')
            ->first();

        return [
            'count' => (int) $result->count,
            'avg' => (float) $result->avg,
            'min' => (float) $result->min,
            'max' => (float) $result->max,
            'sum' => (float) $result->sum,
        ];
    }

    public function deleteOldMetrics(int $daysToKeep = 90): int
    {
        $cutoff = now()->subDays($daysToKeep);
        
        return ObservabilityMetric::where('timestamp', '<', $cutoff)->delete();
    }
}
