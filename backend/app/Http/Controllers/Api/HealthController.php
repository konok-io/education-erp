<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    /**
     * Check API health status
     */
    public function check(): JsonResponse
    {
        $status = 'healthy';
        $checks = [];

        // Database check
        try {
            DB::connection()->getPdo();
            $checks['database'] = [
                'status' => 'up',
                'driver' => config('database.default'),
            ];
        } catch (\Exception $e) {
            $status = 'degraded';
            $checks['database'] = [
                'status' => 'down',
                'error' => 'Unable to connect to database',
            ];
        }

        return response()->json([
            'status' => $status,
            'timestamp' => now()->toIso8601String(),
            'version' => '1.0.0',
            'checks' => $checks,
        ], $status === 'healthy' ? 200 : 503);
    }
}
