<?php

declare(strict_types=1);

namespace App\Services\Observability;

use App\Enums\Observability\HealthCheckStatus;
use App\Enums\Observability\HealthCheckType;
use App\Models\Observability\ObservabilityHealthCheck;
use App\Models\Observability\ObservabilityHealthCheckResult;
use App\Models\Observability\ObservabilityAuditLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\RequestException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

class HealthCheckService
{
    public function index(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ObservabilityHealthCheck::with('service');

        if (isset($filters['service_id'])) {
            $query->where('service_id', $filters['service_id']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['environment'])) {
            $query->where('environment', $filters['environment']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $query->orderBy('name');

        return $query->paginate($perPage);
    }

    public function getActiveChecks(): Collection
    {
        return ObservabilityHealthCheck::active()
            ->with('service')
            ->orderBy('name')
            ->get();
    }

    public function getByType(HealthCheckType $type): Collection
    {
        return ObservabilityHealthCheck::active()
            ->byType($type)
            ->with('service')
            ->get();
    }

    public function findOrFail(string $id): ObservabilityHealthCheck
    {
        return ObservabilityHealthCheck::with('service')->findOrFail($id);
    }

    public function create(array $data): ObservabilityHealthCheck
    {
        return ObservabilityHealthCheck::create($data);
    }

    public function update(string $id, array $data): ObservabilityHealthCheck
    {
        $healthCheck = $this->findOrFail($id);
        $healthCheck->update($data);
        return $healthCheck->fresh();
    }

    public function toggleActive(string $id): ObservabilityHealthCheck
    {
        $healthCheck = $this->findOrFail($id);
        $healthCheck->update(['is_active' => !$healthCheck->is_active]);
        return $healthCheck->fresh();
    }

    public function delete(string $id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    public function execute(string $id): ObservabilityHealthCheckResult
    {
        $healthCheck = $this->findOrFail($id);

        $startTime = microtime(true);

        try {
            $response = $this->performHealthCheck($healthCheck);
            $status = 'healthy';
            $errorMessage = null;
            $httpStatusCode = $response['status_code'] ?? null;
            $responseBody = $response['body'] ?? null;
        } catch (RequestException $e) {
            $status = 'unhealthy';
            $errorMessage = $e->getMessage();
            $httpStatusCode = $e->response?->status();
            $responseBody = $e->response?->json();
        } catch (\Exception $e) {
            $status = 'unhealthy';
            $errorMessage = $e->getMessage();
            $httpStatusCode = null;
            $responseBody = null;
        }

        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;

        $result = ObservabilityHealthCheckResult::create([
            'health_check_id' => $healthCheck->id,
            'status' => $status,
            'response_time_ms' => $responseTime,
            'http_status_code' => $httpStatusCode,
            'error_message' => $errorMessage,
            'response_body' => $responseBody,
            'checked_at' => now(),
        ]);

        $healthCheckStatus = $this->determineStatus($healthCheck, $result);
        $healthCheck->updateStatus($healthCheckStatus, $errorMessage, $responseTime);

        ObservabilityAuditLog::log(
            eventType: $healthCheckStatus === 'healthy' ? 'health_check_restored' : 'health_check_failed',
            severity: $healthCheckStatus === 'unhealthy' ? 'high' : 'info',
            serviceId: $healthCheck->service_id,
            eventData: [
                'health_check_id' => $healthCheck->id,
                'name' => $healthCheck->name,
                'status' => $healthCheckStatus,
                'response_time_ms' => $responseTime,
            ],
        );

        return $result;
    }

    protected function performHealthCheck(ObservabilityHealthCheck $healthCheck): array
    {
        $endpoint = $healthCheck->endpoint;
        $method = $healthCheck->method;
        $timeout = $healthCheck->timeout_seconds;
        $headers = $healthCheck->headers ?? [];

        $http = Http::timeout($timeout)->withHeaders($headers);

        if ($healthCheck->follow_redirects) {
            $http->allowRedirects();
        } else {
            $http->withoutRedirecting();
        }

        if (!empty($healthCheck->cookies)) {
            $http->withCookies($healthCheck->cookies, parse_url($endpoint, PHP_URL_HOST));
        }

        $response = match (strtoupper($method)) {
            'GET' => $http->get($endpoint),
            'POST' => $http->post($endpoint, $healthCheck->request_config ?? []),
            'PUT' => $http->put($endpoint, $healthCheck->request_config ?? []),
            'PATCH' => $http->patch($endpoint, $healthCheck->request_config ?? []),
            'HEAD' => $http->head($endpoint),
            default => throw new \Exception("Unsupported HTTP method: {$method}"),
        };

        if ($response->failed()) {
            throw new RequestException($response->toRequest());
        }

        return [
            'status_code' => $response->status(),
            'body' => $response->json(),
        ];
    }

    protected function determineStatus(
        ObservabilityHealthCheck $healthCheck,
        ObservabilityHealthCheckResult $result
    ): HealthCheckStatus {
        if ($result->status === 'failed' || $result->error_message) {
            return HealthCheckStatus::UNHEALTHY;
        }

        $expectedResponse = $healthCheck->expected_response;

        if ($expectedResponse) {
            if (isset($expectedResponse['status_code']) && 
                $result->http_status_code !== $expectedResponse['status_code']) {
                return HealthCheckStatus::UNHEALTHY;
            }
        }

        if ($result->http_status_code >= 500) {
            return HealthCheckStatus::UNHEALTHY;
        }

        if ($result->http_status_code >= 400) {
            return HealthCheckStatus::DEGRADED;
        }

        if ($result->response_time_ms > ($healthCheck->timeout_seconds * 1000 * 0.8)) {
            return HealthCheckStatus::DEGRADED;
        }

        return HealthCheckStatus::HEALTHY;
    }

    public function executeAll(): array
    {
        $checks = $this->getActiveChecks();
        $results = [];

        foreach ($checks as $check) {
            $results[$check->id] = $this->execute($check->id);
        }

        return $results;
    }

    public function getResults(string $healthCheckId, int $limit = 100): Collection
    {
        return ObservabilityHealthCheckResult::byHealthCheck($healthCheckId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getSummary(): array
    {
        $checks = $this->getActiveChecks();

        return [
            'total' => $checks->count(),
            'healthy' => $checks->where('status', HealthCheckStatus::HEALTHY)->count(),
            'degraded' => $checks->where('status', HealthCheckStatus::DEGRADED)->count(),
            'unhealthy' => $checks->where('status', HealthCheckStatus::UNHEALTHY)->count(),
            'unknown' => $checks->where('status', HealthCheckStatus::UNKNOWN)->count(),
        ];
    }
}
