<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Facility;

use App\Http\Controllers\Controller;
use App\Services\Facility\FacilityService;
use App\Models\Facility\FacilityType;
use App\Models\Facility\Facility;
use App\Models\Facility\FacilityBooking;
use App\Models\Facility\MaintenanceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function __construct(
        private readonly FacilityService $facilityService
    ) {}

    public function getFacilityTypes(): JsonResponse
    {
        $types = FacilityType::where('is_active', true)->get();
        return response()->json(['data' => $types]);
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['facility_type_id', 'status', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $facilities = $this->facilityService->getFacilities($perPage, $filters);

        return response()->json([
            'data' => $facilities->items(),
            'meta' => [
                'current_page' => $facilities->currentPage(),
                'last_page' => $facilities->lastPage(),
                'per_page' => $facilities->perPage(),
                'total' => $facilities->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'facility_type_id' => 'nullable|exists:facility_types,id',
            'code' => 'nullable|string|max:50|unique:facilities',
            'location' => 'nullable|string|max:200',
            'capacity' => 'nullable|integer|min:1',
            'equipment' => 'nullable|array',
            'available_from' => 'nullable',
            'available_to' => 'nullable',
            'description' => 'nullable|string',
            'photo' => 'nullable|string',
        ]);

        $facility = $this->facilityService->createFacility($validated);
        return response()->json(['data' => $facility], 201);
    }

    public function show(string $uuid): JsonResponse
    {
        $facility = Facility::with(['facilityType', 'bookings'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $facility]);
    }

    // ===================== BOOKINGS =====================

    public function getBookings(Request $request): JsonResponse
    {
        $query = FacilityBooking::with(['facility', 'booker']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->facility_id) {
            $query->where('facility_id', $request->facility_id);
        }

        if ($request->date) {
            $query->whereDate('booking_date', $request->date);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $bookings->items(),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'total' => $bookings->total(),
            ],
        ]);
    }

    public function createBooking(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'organizer_name' => 'nullable|string|max:255',
            'event_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'expected_attendees' => 'nullable|integer|min:1',
            'security_deposit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $booking = $this->facilityService->createBooking($validated);
            return response()->json(['data' => $booking], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function approveBooking(string $uuid): JsonResponse
    {
        $booking = FacilityBooking::where('uuid', $uuid)->firstOrFail();
        $booking->approve();

        return response()->json(['data' => $booking->fresh()]);
    }

    public function rejectBooking(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'remarks' => 'required|string',
        ]);

        $booking = FacilityBooking::where('uuid', $uuid)->firstOrFail();
        $booking->reject($validated['remarks']);

        return response()->json(['data' => $booking->fresh()]);
    }

    // ===================== MAINTENANCE =====================

    public function getMaintenanceRequests(Request $request): JsonResponse
    {
        $query = MaintenanceRequest::with(['reporter', 'assignee']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $requests->items(),
            'meta' => [
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'total' => $requests->total(),
            ],
        ]);
    }

    public function createMaintenanceRequest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'required|in:electrical,plumbing,furniture,cleaning,it_support,building,vehicle,other',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'location' => 'required|string|max:200',
            'description' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        $request_ = $this->facilityService->createMaintenanceRequest($validated);
        return response()->json(['data' => $request_], 201);
    }

    public function assignMaintenance(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $maintenance = MaintenanceRequest::where('uuid', $uuid)->firstOrFail();
        $maintenance->assignTo($validated['assigned_to']);

        return response()->json(['data' => $maintenance->fresh()]);
    }

    public function completeMaintenance(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'resolution' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $maintenance = MaintenanceRequest::where('uuid', $uuid)->firstOrFail();
        $maintenance->complete($validated['resolution']);

        if (!empty($validated['cost'])) {
            $maintenance->update(['cost' => $validated['cost']]);
        }

        return response()->json(['data' => $maintenance->fresh()]);
    }

    public function getDashboard(): JsonResponse
    {
        $stats = $this->facilityService->getDashboardStats();
        return response()->json(['data' => $stats]);
    }
}
