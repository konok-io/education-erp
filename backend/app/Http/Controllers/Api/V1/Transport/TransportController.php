<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Transport;

use App\Http\Controllers\Controller;
use App\Services\Transport\TransportService;
use App\Models\Transport\TransportVehicle;
use App\Models\Transport\TransportDriver;
use App\Models\Transport\TransportRoute;
use App\Models\Transport\TransportStop;
use App\Models\Transport\TransportAllocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function __construct(
        private readonly TransportService $transportService
    ) {}

    // ===================== VEHICLES =====================

    public function getVehicles(Request $request): JsonResponse
    {
        $filters = $request->only(['vehicle_type', 'status', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $vehicles = $this->transportService->getVehicles($perPage, $filters);

        return response()->json([
            'data' => $vehicles->items(),
            'meta' => [
                'current_page' => $vehicles->currentPage(),
                'last_page' => $vehicles->lastPage(),
                'per_page' => $vehicles->perPage(),
                'total' => $vehicles->total(),
            ],
        ]);
    }

    public function createVehicle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string|max:50',
            'registration_no' => 'required|string|max:50|unique:transport_vehicles',
            'vehicle_type' => 'nullable|in:bus,mini_bus,micro_bus,van,car,ambulance,other',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'capacity' => 'nullable|integer|min:1',
            'color' => 'nullable|string|max:50',
            'chassis_no' => 'nullable|string|max:100',
            'engine_no' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'insurance_expiry' => 'nullable|date',
            'tax_token' => 'nullable|date',
            'fitness_expiry' => 'nullable|date',
            'fuel_type' => 'nullable|in:petrol,diesel,cng,electric,hybrid',
            'avg_mileage' => 'nullable|numeric',
            'photo' => 'nullable|string',
        ]);

        $vehicle = $this->transportService->createVehicle($validated);
        return response()->json(['data' => $vehicle], 201);
    }

    public function showVehicle(string $uuid): JsonResponse
    {
        $vehicle = TransportVehicle::with(['routes', 'fuelLogs', 'maintenanceLogs'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $vehicle]);
    }

    // ===================== DRIVERS =====================

    public function getDrivers(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $drivers = $this->transportService->getDrivers($perPage, $filters);

        return response()->json([
            'data' => $drivers->items(),
            'meta' => [
                'current_page' => $drivers->currentPage(),
                'last_page' => $drivers->lastPage(),
                'per_page' => $drivers->perPage(),
                'total' => $drivers->total(),
            ],
        ]);
    }

    public function createDriver(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'license_no' => 'required|string|max:50|unique:transport_drivers',
            'license_type' => 'nullable|string|max:50',
            'license_expiry' => 'nullable|date',
            'experience_years' => 'nullable|integer|min:0',
            'joining_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'photo' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $driver = $this->transportService->createDriver($validated);
        return response()->json(['data' => $driver], 201);
    }

    // ===================== ROUTES =====================

    public function getRoutes(Request $request): JsonResponse
    {
        $filters = $request->only(['status']);
        $perPage = (int) $request->get('per_page', 20);

        $routes = $this->transportService->getRoutes($perPage, $filters);

        return response()->json([
            'data' => $routes->items(),
            'meta' => [
                'current_page' => $routes->currentPage(),
                'last_page' => $routes->lastPage(),
                'per_page' => $routes->perPage(),
                'total' => $routes->total(),
            ],
        ]);
    }

    public function createRoute(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'route_code' => 'nullable|string|max:50|unique:transport_routes',
            'distance' => 'nullable|numeric|min:0',
            'distance_unit' => 'nullable|string|max:20',
            'estimated_time' => 'nullable|integer|min:1',
            'vehicle_id' => 'nullable|exists:transport_vehicles,id',
            'driver_id' => 'nullable|exists:transport_drivers,id',
            'stops' => 'nullable|array',
            'stops.*.name' => 'required_with:stops|string|max:255',
            'stops.*.address' => 'nullable|string',
            'stops.*.latitude' => 'nullable|numeric',
            'stops.*.longitude' => 'nullable|numeric',
            'stops.*.pickup_time' => 'nullable',
            'stops.*.drop_time' => 'nullable',
            'stops.*.extra_fee' => 'nullable|numeric|min:0',
        ]);

        $route = $this->transportService->createRoute($validated);
        return response()->json(['data' => $route], 201);
    }

    // ===================== ALLOCATIONS =====================

    public function getAllocations(Request $request): JsonResponse
    {
        $query = TransportAllocation::with(['route', 'student', 'pickupStop', 'dropStop']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->route_id) {
            $query->where('route_id', $request->route_id);
        }

        $allocations = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $allocations->items(),
            'meta' => [
                'current_page' => $allocations->currentPage(),
                'last_page' => $allocations->lastPage(),
                'total' => $allocations->total(),
            ],
        ]);
    }

    public function allocateStudent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'route_id' => 'required|exists:transport_routes,id',
            'pickup_stop_id' => 'nullable|exists:transport_stops,id',
            'drop_stop_id' => 'nullable|exists:transport_stops,id',
            'seat_number' => 'nullable|integer|min:1',
            'monthly_fee' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $allocation = $this->transportService->allocateStudent($validated);
        return response()->json(['data' => $allocation], 201);
    }

    public function getDashboard(): JsonResponse
    {
        $stats = $this->transportService->getDashboardStats();
        return response()->json(['data' => $stats]);
    }
}
