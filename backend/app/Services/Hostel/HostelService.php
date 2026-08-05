<?php

declare(strict_types=1);

namespace App\Services\Hostel;

use App\Models\Hostel\Bed;
use App\Models\Hostel\Building;
use App\Models\Hostel\Floor;
use App\Models\Hostel\GatePass;
use App\Models\Hostel\Hostel;
use App\Models\Hostel\HostelAllocation;
use App\Models\Hostel\HostelAttendance;
use App\Models\Hostel\HostelComplaint;
use App\Models\Hostel\HostelMaintenanceRequest;
use App\Models\Hostel\HostelVisitor;
use App\Models\Hostel\Room;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class HostelService
{
    // ===================== HOSTEL METHODS =====================

    public function getHostels(array $filters = []): LengthAwarePaginator
    {
        $query = Hostel::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('hostel_name', 'like', "%{$filters['search']}%")
                  ->orWhere('hostel_code', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['hostel_type'])) {
            $query->where('hostel_type', $filters['hostel_type']);
        }

        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->where('is_active', true);

        return $query->orderBy('hostel_name')->paginate($filters['per_page'] ?? 20);
    }

    public function createHostel(array $data): Hostel
    {
        return Hostel::create($data);
    }

    public function updateHostel(Hostel $hostel, array $data): Hostel
    {
        $hostel->update($data);
        return $hostel->fresh();
    }

    public function deleteHostel(Hostel $hostel): void
    {
        $hostel->delete();
    }

    // ===================== BUILDING METHODS =====================

    public function getBuildings(array $filters = []): LengthAwarePaginator
    {
        $query = Building::with('hostel');

        if (!empty($filters['hostel_id'])) {
            $query->where('hostel_id', $filters['hostel_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('building_name', 'like', "%{$filters['search']}%")
                  ->orWhere('building_code', 'like', "%{$filters['search']}%");
            });
        }

        $query->where('is_active', true);

        return $query->orderBy('building_name')->paginate($filters['per_page'] ?? 20);
    }

    public function createBuilding(array $data): Building
    {
        if (empty($data['building_code'])) {
            $data['building_code'] = 'BLD-' . strtoupper(substr(uniqid(), -6));
        }

        return Building::create($data);
    }

    public function updateBuilding(Building $building, array $data): Building
    {
        $building->update($data);
        return $building->fresh();
    }

    // ===================== FLOOR METHODS =====================

    public function getFloors(array $filters = []): LengthAwarePaginator
    {
        $query = Floor::with('building');

        if (!empty($filters['building_id'])) {
            $query->where('building_id', $filters['building_id']);
        }

        return $query->orderBy('floor_number')->paginate($filters['per_page'] ?? 50);
    }

    public function createFloor(array $data): Floor
    {
        return Floor::create($data);
    }

    public function createFloorsForBuilding(Building $building, int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            Floor::create([
                'building_id' => $building->id,
                'floor_number' => $i,
                'floor_name' => 'Floor ' . $i,
            ]);
        }
    }

    // ===================== ROOM METHODS =====================

    public function getRooms(array $filters = []): LengthAwarePaginator
    {
        $query = Room::with(['hostel', 'building', 'floor']);

        if (!empty($filters['hostel_id'])) {
            $query->where('hostel_id', $filters['hostel_id']);
        }

        if (!empty($filters['building_id'])) {
            $query->where('building_id', $filters['building_id']);
        }

        if (!empty($filters['room_type'])) {
            $query->where('room_type', $filters['room_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['available'])) {
            $query->available();
        }

        $query->where('is_active', true);

        return $query->orderBy('room_number')->paginate($filters['per_page'] ?? 20);
    }

    public function createRoom(array $data): Room
    {
        if (empty($data['room_code'])) {
            $data['room_code'] = 'RM-' . strtoupper(substr(uniqid(), -8));
        }

        return DB::transaction(function () use ($data) {
            $room = Room::create($data);

            // Create beds based on capacity
            for ($i = 1; $i <= $data['capacity']; $i++) {
                Bed::create([
                    'room_id' => $room->id,
                    'bed_number' => 'B' . $i,
                    'bed_code' => $room->room_code . '-B' . $i,
                    'position' => $this->getBedPosition($i),
                    'status' => Bed::STATUS_AVAILABLE,
                ]);
            }

            return $room->load('beds');
        });
    }

    public function updateRoom(Room $room, array $data): Room
    {
        $room->update($data);
        return $room->fresh();
    }

    private function getBedPosition(int $number): string
    {
        return match ($number) {
            1 => Bed::POSITION_TOP_LEFT,
            2 => Bed::POSITION_TOP_RIGHT,
            3 => Bed::POSITION_BOTTOM_LEFT,
            4 => Bed::POSITION_BOTTOM_RIGHT,
            default => 'bed_' . $number,
        };
    }

    // ===================== BED METHODS =====================

    public function getBeds(array $filters = []): LengthAwarePaginator
    {
        $query = Bed::with(['room', 'room.hostel']);

        if (!empty($filters['room_id'])) {
            $query->where('room_id', $filters['room_id']);
        }

        if (!empty($filters['hostel_id'])) {
            $query->whereHas('room', function ($q) use ($filters) {
                $q->where('hostel_id', $filters['hostel_id']);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['available'])) {
            $query->available();
        }

        return $query->orderBy('bed_code')->paginate($filters['per_page'] ?? 50);
    }

    public function getAvailableBeds(int $roomId): \Illuminate\Database\Eloquent\Collection
    {
        return Bed::where('room_id', $roomId)
            ->available()
            ->get();
    }

    // ===================== ALLOCATION METHODS =====================

    public function getAllocations(array $filters = []): LengthAwarePaginator
    {
        $query = HostelAllocation::with(['hostel', 'room', 'bed']);

        if (!empty($filters['hostel_id'])) {
            $query->where('hostel_id', $filters['hostel_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['allocatable_type']) && !empty($filters['allocatable_id'])) {
            $query->where('allocatable_type', $filters['allocatable_type'])
                  ->where('allocatable_id', $filters['allocatable_id']);
        }

        return $query->orderByDesc('created_at')->paginate($filters['per_page'] ?? 20);
    }

    public function createAllocation(array $data): HostelAllocation
    {
        $data['allocation_no'] = HostelAllocation::generateAllocationNo();

        return DB::transaction(function () use ($data) {
            $allocation = HostelAllocation::create($data);

            // Allocate bed if provided
            if (!empty($data['bed_id'])) {
                $allocation->approve(auth()->id());
            }

            // Update hostel stats
            if ($allocation->hostel) {
                $allocation->hostel->updateStats();
            }

            return $allocation;
        });
    }

    public function approveAllocation(HostelAllocation $allocation): HostelAllocation
    {
        return DB::transaction(function () use ($allocation) {
            $allocation->approve(auth()->id());

            if ($allocation->hostel) {
                $allocation->hostel->updateStats();
            }

            return $allocation->fresh();
        });
    }

    public function checkInAllocation(HostelAllocation $allocation): HostelAllocation
    {
        $allocation->checkIn();
        return $allocation->fresh();
    }

    public function checkOutAllocation(HostelAllocation $allocation): HostelAllocation
    {
        return DB::transaction(function () use ($allocation) {
            $allocation->checkOut();

            if ($allocation->hostel) {
                $allocation->hostel->updateStats();
            }

            return $allocation->fresh();
        });
    }

    public function transferAllocation(HostelAllocation $allocation, array $newData): HostelAllocation
    {
        return DB::transaction(function () use ($allocation, $newData) {
            // Free old bed
            if ($allocation->bed) {
                $allocation->bed->checkout();
            }

            // Update allocation
            $allocation->update($newData);

            // Allocate new bed
            if (!empty($newData['bed_id'])) {
                $allocation->approve(auth()->id());
            }

            // Update hostel stats
            if ($allocation->hostel) {
                $allocation->hostel->updateStats();
            }

            return $allocation->fresh();
        });
    }

    // ===================== VISITOR METHODS =====================

    public function getVisitors(array $filters = []): LengthAwarePaginator
    {
        $query = HostelVisitor::with('hostel');

        if (!empty($filters['hostel_id'])) {
            $query->where('hostel_id', $filters['hostel_id']);
        }

        if (!empty($filters['visit_date'])) {
            $query->whereDate('visit_date', $filters['visit_date']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('visit_date')->paginate($filters['per_page'] ?? 20);
    }

    public function createVisitor(array $data): HostelVisitor
    {
        $data['visitor_no'] = HostelVisitor::generateVisitorNo();
        return HostelVisitor::create($data);
    }

    public function approveVisitor(HostelVisitor $visitor): HostelVisitor
    {
        $visitor->approve(auth()->id());
        return $visitor->fresh();
    }

    public function checkInVisitor(HostelVisitor $visitor): HostelVisitor
    {
        $visitor->checkIn();
        return $visitor->fresh();
    }

    public function checkOutVisitor(HostelVisitor $visitor): HostelVisitor
    {
        $visitor->checkOut();
        return $visitor->fresh();
    }

    // ===================== GATE PASS METHODS =====================

    public function getGatePasses(array $filters = []): LengthAwarePaginator
    {
        $query = GatePass::with('hostel');

        if (!empty($filters['hostel_id'])) {
            $query->where('hostel_id', $filters['hostel_id']);
        }

        if (!empty($filters['pass_type'])) {
            $query->where('pass_type', $filters['pass_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['valid_from']) && !empty($filters['valid_to'])) {
            $query->whereBetween('valid_from', [$filters['valid_from'], $filters['valid_to']]);
        }

        return $query->orderByDesc('issue_date')->paginate($filters['per_page'] ?? 20);
    }

    public function createGatePass(array $data): GatePass
    {
        $data['pass_no'] = GatePass::generatePassNo();
        $data['issued_by'] = auth()->id();

        return GatePass::create($data);
    }

    public function approveGatePass(GatePass $gatePass): GatePass
    {
        $gatePass->approve(auth()->id());
        return $gatePass->fresh();
    }

    // ===================== COMPLAINT METHODS =====================

    public function getComplaints(array $filters = []): LengthAwarePaginator
    {
        $query = HostelComplaint::with(['hostel', 'room']);

        if (!empty($filters['hostel_id'])) {
            $query->where('hostel_id', $filters['hostel_id']);
        }

        if (!empty($filters['complaint_type'])) {
            $query->where('complaint_type', $filters['complaint_type']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('created_at')->paginate($filters['per_page'] ?? 20);
    }

    public function createComplaint(array $data): HostelComplaint
    {
        $data['complaint_no'] = HostelComplaint::generateComplaintNo();
        $data['reported_by'] = auth()->id();

        return HostelComplaint::create($data);
    }

    public function respondToComplaint(HostelComplaint $complaint, string $response, string $assignedTo): HostelComplaint
    {
        $complaint->respond($response, $assignedTo);
        return $complaint->fresh();
    }

    public function resolveComplaint(HostelComplaint $complaint, string $resolution): HostelComplaint
    {
        $complaint->resolve($resolution);
        return $complaint->fresh();
    }

    // ===================== MAINTENANCE METHODS =====================

    public function getMaintenanceRequests(array $filters = []): LengthAwarePaginator
    {
        $query = HostelMaintenanceRequest::with(['hostel', 'room']);

        if (!empty($filters['hostel_id'])) {
            $query->where('hostel_id', $filters['hostel_id']);
        }

        if (!empty($filters['request_type'])) {
            $query->where('request_type', $filters['request_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('created_at')->paginate($filters['per_page'] ?? 20);
    }

    public function createMaintenanceRequest(array $data): HostelMaintenanceRequest
    {
        $data['request_no'] = HostelMaintenanceRequest::generateRequestNo();
        $data['created_by'] = auth()->id();

        return HostelMaintenanceRequest::create($data);
    }

    public function completeMaintenanceRequest(HostelMaintenanceRequest $request, string $workDone, float $cost = null): HostelMaintenanceRequest
    {
        $request->complete($workDone, $cost);
        return $request->fresh();
    }

    // ===================== ATTENDANCE METHODS =====================

    public function getAttendances(array $filters = []): LengthAwarePaginator
    {
        $query = HostelAttendance::with('hostel');

        if (!empty($filters['hostel_id'])) {
            $query->where('hostel_id', $filters['hostel_id']);
        }

        if (!empty($filters['attendance_date'])) {
            $query->whereDate('attendance_date', $filters['attendance_date']);
        }

        if (!empty($filters['attendance_type'])) {
            $query->where('attendance_type', $filters['attendance_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('attendance_date')->paginate($filters['per_page'] ?? 50);
    }

    public function recordAttendance(array $data): HostelAttendance
    {
        $data['recorded_by'] = auth()->id();
        return HostelAttendance::create($data);
    }

    public function bulkRecordAttendance(array $records): void
    {
        foreach ($records as $record) {
            $record['recorded_by'] = auth()->id();
            HostelAttendance::create($record);
        }
    }

    // ===================== DASHBOARD METHODS =====================

    public function getDashboardData(): array
    {
        return [
            'total_hostels' => Hostel::where('is_active', true)->count(),
            'active_hostels' => Hostel::active()->count(),
            'total_buildings' => Building::where('is_active', true)->count(),
            'total_rooms' => Room::where('is_active', true)->count(),
            'total_beds' => Room::where('is_active', true)->sum('capacity'),
            'occupied_beds' => HostelAllocation::active()->count(),
            'available_beds' => Room::where('is_active', true)->sum('capacity') - HostelAllocation::active()->count(),
            'today_visitors' => HostelVisitor::today()->count(),
            'pending_complaints' => HostelComplaint::unresolved()->count(),
            'pending_maintenance' => HostelMaintenanceRequest::due()->count(),
            'today_check_ins' => HostelAllocation::whereDate('check_in_date', now()->toDateString())->count(),
            'today_check_outs' => HostelAllocation::whereDate('actual_checkout', now()->toDateString())->count(),
            'pending_approvals' => GatePass::pending()->count(),
        ];
    }
}
