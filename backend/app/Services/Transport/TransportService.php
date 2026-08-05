<?php

declare(strict_types=1);

namespace App\Services\Transport;

use App\Models\Transport\Accident;
use App\Models\Transport\Driver;
use App\Models\Transport\FuelRecord;
use App\Models\Transport\Incident;
use App\Models\Transport\Route;
use App\Models\Transport\RouteStop;
use App\Models\Transport\TransportAssignment;
use App\Models\Transport\Trip;
use App\Models\Transport\Vehicle;
use App\Models\Transport\VehicleDocument;
use App\Models\Transport\VehicleInsurance;
use App\Models\Transport\VehicleMaintenance;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TransportService
{
    // ===================== VEHICLE METHODS =====================

    public function getVehicles(array $filters = []): LengthAwarePaginator
    {
        $query = Vehicle::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('vehicle_number', 'like', "%{$filters['search']}%")
                  ->orWhere('registration_number', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['vehicle_type'])) {
            $query->where('vehicle_type', $filters['vehicle_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['fuel_type'])) {
            $query->where('fuel_type', $filters['fuel_type']);
        }

        $query->where('is_active', true);

        return $query->orderBy('vehicle_number')->paginate($filters['per_page'] ?? 20);
    }

    public function createVehicle(array $data): Vehicle
    {
        return Vehicle::create($data);
    }

    public function updateVehicle(Vehicle $vehicle, array $data): Vehicle
    {
        $vehicle->update($data);
        return $vehicle->fresh();
    }

    public function deleteVehicle(Vehicle $vehicle): void
    {
        $vehicle->delete();
    }

    public function updateVehicleStatus(Vehicle $vehicle, string $status): Vehicle
    {
        $vehicle->update(['status' => $status]);
        return $vehicle->fresh();
    }

    // ===================== DRIVER METHODS =====================

    public function getDrivers(array $filters = []): LengthAwarePaginator
    {
        $query = Driver::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('full_name', 'like', "%{$filters['search']}%")
                  ->orWhere('driver_id', 'like', "%{$filters['search']}%")
                  ->orWhere('license_number', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['license_expiring'])) {
            $query->licenseExpiring((int) $filters['license_expiring']);
        }

        $query->where('is_active', true);

        return $query->orderBy('full_name')->paginate($filters['per_page'] ?? 20);
    }

    public function createDriver(array $data): Driver
    {
        if (empty($data['driver_id'])) {
            $data['driver_id'] = Driver::generateDriverId();
        }

        return Driver::create($data);
    }

    public function updateDriver(Driver $driver, array $data): Driver
    {
        $driver->update($data);
        return $driver->fresh();
    }

    public function deleteDriver(Driver $driver): void
    {
        $driver->delete();
    }

    public function getDriversWithExpiringLicense(int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return Driver::active()->licenseExpiring($days)->get();
    }

    // ===================== ROUTE METHODS =====================

    public function getRoutes(array $filters = []): LengthAwarePaginator
    {
        $query = Route::with('stops');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('route_code', 'like', "%{$filters['search']}%")
                  ->orWhere('route_name', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->where('is_active', true);

        return $query->orderBy('route_code')->paginate($filters['per_page'] ?? 20);
    }

    public function createRoute(array $data): Route
    {
        if (empty($data['route_code'])) {
            $data['route_code'] = Route::generateRouteCode();
        }

        return DB::transaction(function () use ($data) {
            $route = Route::create($data);

            if (!empty($data['stops'])) {
                foreach ($data['stops'] as $stopData) {
                    $stopData['route_id'] = $route->id;
                    RouteStop::create($stopData);
                }
            }

            return $route->load('stops');
        });
    }

    public function updateRoute(Route $route, array $data): Route
    {
        return DB::transaction(function () use ($route, $data) {
            $route->update($data);

            if (isset($data['stops'])) {
                $route->stops()->delete();
                foreach ($data['stops'] as $stopData) {
                    $stopData['route_id'] = $route->id;
                    RouteStop::create($stopData);
                }
            }

            return $route->fresh()->load('stops');
        });
    }

    public function deleteRoute(Route $route): void
    {
        $route->stops()->delete();
        $route->delete();
    }

    // ===================== ROUTE STOP METHODS =====================

    public function addRouteStop(Route $route, array $data): RouteStop
    {
        $data['route_id'] = $route->id;
        $data['sequence'] = $route->stops()->max('sequence') + 1;

        return RouteStop::create($data);
    }

    public function removeRouteStop(RouteStop $stop): void
    {
        $stop->delete();
    }

    public function reorderStops(Route $route, array $stopOrders): void
    {
        foreach ($stopOrders as $order) {
            RouteStop::where('id', $order['id'])
                ->where('route_id', $route->id)
                ->update(['sequence' => $order['sequence']]);
        }
    }

    // ===================== TRIP METHODS =====================

    public function getTrips(array $filters = []): LengthAwarePaginator
    {
        $query = Trip::with(['vehicle', 'driver', 'route']);

        if (!empty($filters['trip_date'])) {
            $query->where('trip_date', $filters['trip_date']);
        }

        if (!empty($filters['vehicle_id'])) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        if (!empty($filters['driver_id'])) {
            $query->where('driver_id', $filters['driver_id']);
        }

        if (!empty($filters['trip_type'])) {
            $query->where('trip_type', $filters['trip_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('trip_date')->orderBy('start_time')->paginate($filters['per_page'] ?? 20);
    }

    public function createTrip(array $data): Trip
    {
        if (empty($data['trip_no'])) {
            $data['trip_no'] = Trip::generateTripNo();
        }

        if (empty($data['created_by']) && auth()->check()) {
            $data['created_by'] = auth()->id();
        }

        return Trip::create($data);
    }

    public function updateTrip(Trip $trip, array $data): Trip
    {
        $trip->update($data);
        return $trip->fresh();
    }

    public function startTrip(Trip $trip, string $startOdometer): Trip
    {
        $trip->update([
            'status' => Trip::STATUS_STARTED,
            'start_time' => now()->format('H:i:s'),
            'start_odometer' => $startOdometer,
        ]);

        return $trip->fresh();
    }

    public function completeTrip(Trip $trip, string $endOdometer, float $distance = null, int $passengers = 0): Trip
    {
        $trip->update([
            'status' => Trip::STATUS_COMPLETED,
            'end_time' => now()->format('H:i:s'),
            'end_odometer' => $endOdometer,
            'distance' => $distance,
            'passenger_count' => $passengers,
        ]);

        // Update vehicle odometer
        if ($trip->vehicle && $endOdometer) {
            $trip->vehicle->update(['current_odometer' => $endOdometer]);
        }

        return $trip->fresh();
    }

    public function cancelTrip(Trip $trip): Trip
    {
        $trip->update(['status' => Trip::STATUS_CANCELLED]);
        return $trip->fresh();
    }

    // ===================== FUEL METHODS =====================

    public function getFuelRecords(array $filters = []): LengthAwarePaginator
    {
        $query = FuelRecord::with('vehicle');

        if (!empty($filters['vehicle_id'])) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('fuel_date', [$filters['date_from'], $filters['date_to']]);
        }

        return $query->orderByDesc('fuel_date')->paginate($filters['per_page'] ?? 20);
    }

    public function createFuelRecord(array $data): FuelRecord
    {
        if (empty($data['total_cost']) && $data['quantity'] && $data['price_per_liter']) {
            $data['total_cost'] = $data['quantity'] * $data['price_per_liter'];
        }

        return DB::transaction(function () use ($data) {
            $fuel = FuelRecord::create($data);

            // Update vehicle odometer
            if (!empty($data['odometer_reading']) && !empty($data['vehicle_id'])) {
                Vehicle::where('id', $data['vehicle_id'])
                    ->update(['current_odometer' => $data['odometer_reading']]);
            }

            return $fuel;
        });
    }

    public function getMonthlyFuelCost(int $vehicleId = null): array
    {
        $query = FuelRecord::thisMonth();

        if ($vehicleId) {
            $query->byVehicle($vehicleId);
        }

        $records = $query->get();

        return [
            'total_cost' => $records->sum('total_cost'),
            'total_quantity' => $records->sum('quantity'),
            'record_count' => $records->count(),
        ];
    }

    // ===================== MAINTENANCE METHODS =====================

    public function getMaintenances(array $filters = []): LengthAwarePaginator
    {
        $query = VehicleMaintenance::with('vehicle');

        if (!empty($filters['vehicle_id'])) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['maintenance_type'])) {
            $query->where('maintenance_type', $filters['maintenance_type']);
        }

        return $query->orderByDesc('service_date')->paginate($filters['per_page'] ?? 20);
    }

    public function createMaintenance(array $data): VehicleMaintenance
    {
        $data['maintenance_no'] = VehicleMaintenance::generateMaintenanceNo();
        $data['created_by'] = auth()->id();

        return VehicleMaintenance::create($data);
    }

    public function completeMaintenance(VehicleMaintenance $maintenance, string $workDone, float $cost = null): VehicleMaintenance
    {
        $maintenance->update([
            'status' => VehicleMaintenance::STATUS_COMPLETED,
            'work_done' => $workDone,
            'cost' => $cost ?? $maintenance->cost,
        ]);

        // Update vehicle status
        if ($maintenance->vehicle) {
            $maintenance->vehicle->update(['status' => Vehicle::STATUS_ACTIVE]);
        }

        return $maintenance->fresh();
    }

    public function getUpcomingMaintenances(int $days = 7): \Illuminate\Database\Eloquent\Collection
    {
        return VehicleMaintenance::scheduled()
            ->due()
            ->with('vehicle')
            ->get();
    }

    // ===================== INSURANCE METHODS =====================

    public function getInsurances(array $filters = []): LengthAwarePaginator
    {
        $query = VehicleInsurance::with('vehicle');

        if (!empty($filters['vehicle_id'])) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('expiry_date')->paginate($filters['per_page'] ?? 20);
    }

    public function createInsurance(array $data): VehicleInsurance
    {
        return VehicleInsurance::create($data);
    }

    public function renewInsurance(VehicleInsurance $insurance, array $data): VehicleInsurance
    {
        $insurance->update(['status' => VehicleInsurance::STATUS_RENEWED]);

        $data['vehicle_id'] = $insurance->vehicle_id;
        $data['policy_number'] = $data['policy_number'] ?? $insurance->policy_number . '-R';

        return VehicleInsurance::create($data);
    }

    public function getExpiringInsurances(int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return VehicleInsurance::expiring($days)->with('vehicle')->get();
    }

    // ===================== DOCUMENT METHODS =====================

    public function getDocuments(array $filters = []): LengthAwarePaginator
    {
        $query = VehicleDocument::with('vehicle');

        if (!empty($filters['vehicle_id'])) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        if (!empty($filters['document_type'])) {
            $query->where('document_type', $filters['document_type']);
        }

        return $query->orderByDesc('expiry_date')->paginate($filters['per_page'] ?? 20);
    }

    public function createDocument(array $data): VehicleDocument
    {
        return VehicleDocument::create($data);
    }

    public function getExpiringDocuments(int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return VehicleDocument::expiring($days)->with('vehicle')->get();
    }

    // ===================== ASSIGNMENT METHODS =====================

    public function createAssignment(array $data): TransportAssignment
    {
        $data['assignment_no'] = TransportAssignment::generateAssignmentNo();

        return TransportAssignment::create($data);
    }

    public function endAssignment(TransportAssignment $assignment): TransportAssignment
    {
        $assignment->update([
            'status' => TransportAssignment::STATUS_INACTIVE,
            'end_date' => now()->toDateString(),
        ]);

        return $assignment->fresh();
    }

    public function getActiveAssignments(string $assignableType = null, int $assignableId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = TransportAssignment::active()->with(['route', 'vehicle', 'driver']);

        if ($assignableType) {
            $query->where('assignable_type', $assignableType);
        }

        if ($assignableId) {
            $query->where('assignable_id', $assignableId);
        }

        return $query->get();
    }

    // ===================== ACCIDENT METHODS =====================

    public function getAccidents(array $filters = []): LengthAwarePaginator
    {
        $query = Accident::with(['vehicle', 'driver']);

        if (!empty($filters['vehicle_id'])) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('accident_date')->paginate($filters['per_page'] ?? 20);
    }

    public function createAccident(array $data): Accident
    {
        $data['accident_no'] = Accident::generateAccidentNo();
        $data['reported_by'] = auth()->id();

        return Accident::create($data);
    }

    // ===================== INCIDENT METHODS =====================

    public function getIncidents(array $filters = []): LengthAwarePaginator
    {
        $query = Incident::with(['vehicle', 'driver']);

        if (!empty($filters['vehicle_id'])) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        if (!empty($filters['incident_type'])) {
            $query->where('incident_type', $filters['incident_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('incident_date')->paginate($filters['per_page'] ?? 20);
    }

    public function createIncident(array $data): Incident
    {
        $data['incident_no'] = Incident::generateIncidentNo();
        $data['reported_by'] = auth()->id();

        return Incident::create($data);
    }

    public function resolveIncident(Incident $incident, string $resolution): Incident
    {
        $incident->resolve($resolution);
        return $incident->fresh();
    }

    // ===================== DASHBOARD METHODS =====================

    public function getDashboardData(): array
    {
        return [
            'total_vehicles' => Vehicle::where('is_active', true)->count(),
            'active_vehicles' => Vehicle::active()->count(),
            'inactive_vehicles' => Vehicle::where('status', Vehicle::STATUS_INACTIVE)->count(),
            'under_maintenance' => Vehicle::where('status', Vehicle::STATUS_MAINTENANCE)->count(),
            'total_drivers' => Driver::where('is_active', true)->count(),
            'active_drivers' => Driver::active()->count(),
            'total_routes' => Route::where('is_active', true)->count(),
            'active_routes' => Route::active()->count(),
            'today_trips' => Trip::today()->count(),
            'completed_trips' => Trip::today()->completed()->count(),
            'scheduled_trips' => Trip::today()->scheduled()->count(),
            'monthly_fuel_cost' => FuelRecord::thisMonth()->sum('total_cost'),
            'maintenance_due' => VehicleMaintenance::scheduled()->due()->count(),
            'insurance_expiring' => VehicleInsurance::expiring(30)->count(),
            'license_expiring' => Driver::active()->licenseExpiring(30)->count(),
            'pending_incidents' => Incident::unresolved()->count(),
        ];
    }
}
