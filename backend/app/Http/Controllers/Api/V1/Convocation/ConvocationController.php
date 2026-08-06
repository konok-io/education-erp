<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Convocation;

use App\Http\Controllers\Controller;
use App\Services\Convocation\ConvocationService;
use App\Models\Convocation\Convocation;
use App\Models\Convocation\ConvocationRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConvocationController extends Controller
{
    public function __construct(
        private readonly ConvocationService $convocationService
    ) {}

    // ===================== DASHBOARD =====================

    public function getDashboard(): JsonResponse
    {
        $stats = $this->convocationService->getDashboardStats();
        return response()->json(['data' => $stats]);
    }

    // ===================== CONVOCATIONS =====================

    public function getConvocations(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'year', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $convocations = $this->convocationService->getConvocations($perPage, $filters);

        return response()->json([
            'data' => $convocations->items(),
            'meta' => [
                'current_page' => $convocations->currentPage(),
                'last_page' => $convocations->lastPage(),
                'total' => $convocations->total(),
            ],
        ]);
    }

    public function createConvocation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'year' => 'required|integer|min:2000|max:2100',
            'semester' => 'nullable|string|max:50',
            'ceremony_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'venue' => 'required|string|max:200',
            'address' => 'nullable|string',
            'chief_guest' => 'nullable|string|max:150',
            'special_guest' => 'nullable|string|max:200',
            'guest_speaker' => 'nullable|string|max:200',
            'agenda' => 'nullable|string',
            'expected_attendees' => 'nullable|integer|min:0',
            'registration_fee' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $convocation = $this->convocationService->createConvocation($validated);
        return response()->json(['data' => $convocation], 201);
    }

    public function showConvocation(string $uuid): JsonResponse
    {
        $convocation = Convocation::with('registrations')
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $convocation]);
    }

    public function updateConvocation(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'year' => 'sometimes|integer|min:2000|max:2100',
            'semester' => 'nullable|string|max:50',
            'ceremony_date' => 'sometimes|date',
            'start_time' => 'sometimes',
            'end_time' => 'sometimes|after:start_time',
            'venue' => 'sometimes|string|max:200',
            'address' => 'nullable|string',
            'chief_guest' => 'nullable|string|max:150',
            'special_guest' => 'nullable|string|max:200',
            'guest_speaker' => 'nullable|string|max:200',
            'agenda' => 'nullable|string',
            'expected_attendees' => 'nullable|integer|min:0',
            'registration_fee' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|in:planning,registration,confirmed,completed,cancelled',
        ]);

        $convocation = $this->convocationService->updateConvocation($uuid, $validated);
        return response()->json(['data' => $convocation]);
    }

    public function openRegistration(string $uuid): JsonResponse
    {
        $convocation = $this->convocationService->openRegistration($uuid);
        return response()->json(['data' => $convocation]);
    }

    public function closeRegistration(string $uuid): JsonResponse
    {
        $convocation = $this->convocationService->closeRegistration($uuid);
        return response()->json(['data' => $convocation]);
    }

    // ===================== REGISTRATIONS =====================

    public function getRegistrations(Request $request): JsonResponse
    {
        $filters = $request->only(['convocation_id', 'status', 'attendance', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $registrations = $this->convocationService->getRegistrations($perPage, $filters);

        return response()->json([
            'data' => $registrations->items(),
            'meta' => [
                'current_page' => $registrations->currentPage(),
                'last_page' => $registrations->lastPage(),
                'total' => $registrations->total(),
            ],
        ]);
    }

    public function registerAlumni(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'convocation_id' => 'required|exists:convocations,id',
            'alumni_id' => 'nullable|exists:alumni_profiles,id',
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'roll_number' => 'nullable|string|max:50',
            'registration_no_old' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'program' => 'nullable|string|max:100',
            'passing_year' => 'nullable|integer|min:2000|max:2100',
            'guest_name' => 'nullable|string|max:150',
            'guest_relation' => 'nullable|string|max:50',
            'total_guests' => 'nullable|integer|min:0',
            'dietary_requirements' => 'nullable|string',
            'accessibility_needs' => 'nullable|string',
        ]);

        $registration = $this->convocationService->registerAlumni($validated);
        return response()->json(['data' => $registration], 201);
    }

    public function confirmRegistration(string $uuid): JsonResponse
    {
        $registration = $this->convocationService->confirmRegistration($uuid);
        return response()->json(['data' => $registration]);
    }

    public function markAttendance(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:attended,absent',
        ]);

        $registration = $this->convocationService->markAttendance($uuid, $validated['status']);
        return response()->json(['data' => $registration]);
    }
}
