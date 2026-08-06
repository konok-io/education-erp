<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Hostel;

use App\Http\Controllers\Controller;
use App\Services\Hostel\HostelService;
use App\Models\Hostel\HostelBuilding;
use App\Models\Hostel\HostelRoom;
use App\Models\Hostel\HostelBed;
use App\Models\Hostel\HostelAdmission;
use App\Models\Hostel\HostelVisitor;
use App\Models\Hostel\HostelAttendance;
use App\Models\Hostel\HostelLeave;
use App\Models\Hostel\HostelFee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HostelController extends Controller
{
    public function __construct(
        private readonly HostelService $hostelService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['gender', 'status', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $buildings = $this->hostelService->getBuildings($perPage, $filters);

        return response()->json([
            'data' => $buildings->items(),
            'meta' => [
                'current_page' => $buildings->currentPage(),
                'last_page' => $buildings->lastPage(),
                'per_page' => $buildings->perPage(),
                'total' => $buildings->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'building_code' => 'nullable|string|max:50|unique:hostel_buildings',
            'campus_id' => 'nullable|exists:campuses,id',
            'gender' => 'nullable|in:male,female,mixed',
            'total_floors' => 'nullable|integer|min:1',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'create_rooms' => 'nullable|boolean',
            'rooms_per_floor' => 'nullable|integer|min:1',
            'beds_per_room' => 'nullable|integer|min:1',
            'room_type' => 'nullable|in:single,double,triple,four_seat,dormitory,vip,guest',
            'rent' => 'nullable|numeric|min:0',
        ]);

        $building = $this->hostelService->createBuilding($validated);
        return response()->json(['data' => $building], 201);
    }

    public function show(string $uuid): JsonResponse
    {
        $building = HostelBuilding::with(['rooms.beds', 'admissions.student'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $building]);
    }

    public function getRooms(Request $request): JsonResponse
    {
        $query = HostelRoom::with(['building', 'beds']);

        if ($request->building_id) {
            $query->where('building_id', $request->building_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $rooms = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $rooms->items(),
            'meta' => [
                'current_page' => $rooms->currentPage(),
                'last_page' => $rooms->lastPage(),
                'total' => $rooms->total(),
            ],
        ]);
    }

    public function getAdmissions(Request $request): JsonResponse
    {
        $query = HostelAdmission::with(['building', 'room', 'bed', 'student']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $admissions = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $admissions->items(),
            'meta' => [
                'current_page' => $admissions->currentPage(),
                'last_page' => $admissions->lastPage(),
                'total' => $admissions->total(),
            ],
        ]);
    }

    public function admitStudent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'student_name' => 'required|string|max:255',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'building_id' => 'required|exists:hostel_buildings,id',
            'bed_id' => 'nullable|exists:hostel_beds,uuid',
            'room_id' => 'nullable|exists:hostel_rooms,id',
            'admission_date' => 'nullable|date',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $admission = $this->hostelService->admitStudent($validated);
        return response()->json(['data' => $admission], 201);
    }

    public function checkInStudent(string $uuid): JsonResponse
    {
        try {
            $admission = $this->hostelService->checkInStudent($uuid);
            return response()->json(['data' => $admission]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function registerVisitor(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'visitor_name' => 'required|string|max:150',
            'relation' => 'nullable|string|max:100',
            'student_id' => 'nullable|exists:students,id',
            'building_id' => 'required|exists:hostel_buildings,id',
            'id_proof' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'purpose' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $visitor = $this->hostelService->registerVisitor($validated);
        return response()->json(['data' => $visitor], 201);
    }

    public function getVisitors(Request $request): JsonResponse
    {
        $query = HostelVisitor::with(['building', 'student']);

        if ($request->building_id) {
            $query->where('building_id', $request->building_id);
        }

        if ($request->date) {
            $query->whereDate('entry_time', $request->date);
        }

        $visitors = $query->orderBy('entry_time', 'desc')->paginate(20);

        return response()->json([
            'data' => $visitors->items(),
            'meta' => [
                'current_page' => $visitors->currentPage(),
                'last_page' => $visitors->lastPage(),
                'total' => $visitors->total(),
            ],
        ]);
    }

    public function getDashboard(): JsonResponse
    {
        $stats = $this->hostelService->getDashboardStats();
        return response()->json(['data' => $stats]);
    }
}
