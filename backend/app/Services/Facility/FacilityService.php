<?php

declare(strict_types=1);

namespace App\Services\Facility;

use App\Models\Facility\FacilityType;
use App\Models\Facility\Facility;
use App\Models\Facility\FacilityBooking;
use App\Models\Facility\MaintenanceRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FacilityService
{
    public function getFacilities(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Facility::with(['facilityType']);

        if (!empty($filters['facility_type_id'])) {
            $query->where('facility_type_id', $filters['facility_type_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('code', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createFacility(array $data): Facility
    {
        return DB::transaction(function () use ($data) {
            return Facility::create([
                'uuid' => (string) Str::uuid(),
                'name' => $data['name'],
                'name_bn' => $data['name_bn'] ?? null,
                'facility_type_id' => $data['facility_type_id'] ?? null,
                'code' => $data['code'] ?? $this->generateFacilityCode(),
                'location' => $data['location'] ?? null,
                'capacity' => $data['capacity'] ?? 0,
                'equipment' => $data['equipment'] ?? null,
                'available_from' => $data['available_from'] ?? '08:00:00',
                'available_to' => $data['available_to'] ?? '20:00:00',
                'description' => $data['description'] ?? null,
                'photo' => $data['photo'] ?? null,
                'status' => $data['status'] ?? 'available',
            ]);
        });
    }

    private function generateFacilityCode(): string
    {
        $prefix = 'F';
        $last = Facility::orderBy('id', 'desc')->first();
        $sequence = $last ? ((int) substr($last->code, 1)) + 1 : 1;
        return sprintf('%s%03d', $prefix, $sequence);
    }

    public function createBooking(array $data): FacilityBooking
    {
        return DB::transaction(function () use ($data) {
            $facility = Facility::findOrFail($data['facility_id']);

            // Check for conflicts
            if ($facility->isBookedOn($data['booking_date'], $data['start_time'], $data['end_time'])) {
                throw new \Exception('Facility is already booked for this time slot');
            }

            // Calculate rental fee
            $hourlyRate = $facility->facilityType?->hourly_rate ?? 0;
            $start = strtotime($data['start_time']);
            $end = strtotime($data['end_time']);
            $hours = round(($end - $start) / 3600, 2);
            $rentalFee = $hourlyRate * $hours;

            return FacilityBooking::create([
                'uuid' => (string) Str::uuid(),
                'booking_no' => FacilityBooking::generateBookingNo(),
                'facility_id' => $data['facility_id'],
                'booked_by' => auth()->id(),
                'organizer_name' => $data['organizer_name'] ?? null,
                'event_name' => $data['event_name'],
                'description' => $data['description'] ?? null,
                'booking_date' => $data['booking_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'expected_attendees' => $data['expected_attendees'] ?? 0,
                'status' => 'pending',
                'rental_fee' => $rentalFee,
                'security_deposit' => $data['security_deposit'] ?? 0,
                'payment_status' => 'unpaid',
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    public function createMaintenanceRequest(array $data): MaintenanceRequest
    {
        return DB::transaction(function () use ($data) {
            return MaintenanceRequest::create([
                'uuid' => (string) Str::uuid(),
                'request_no' => MaintenanceRequest::generateRequestNo(),
                'reported_by' => auth()->id(),
                'category' => $data['category'],
                'priority' => $data['priority'] ?? 'medium',
                'location' => $data['location'],
                'description' => $data['description'],
                'status' => 'pending',
                'remarks' => $data['remarks'] ?? null,
            ]);
        });
    }

    public function getDashboardStats(): array
    {
        return [
            'total_facilities' => Facility::count(),
            'available_facilities' => Facility::where('status', 'available')->count(),
            'total_bookings' => FacilityBooking::count(),
            'pending_bookings' => FacilityBooking::where('status', 'pending')->count(),
            'today_bookings' => FacilityBooking::whereDate('booking_date', now()->toDateString())->count(),
            'total_maintenance_requests' => MaintenanceRequest::count(),
            'pending_requests' => MaintenanceRequest::where('status', 'pending')->count(),
            'in_progress_requests' => MaintenanceRequest::where('status', 'in_progress')->count(),
            'completed_requests' => MaintenanceRequest::where('status', 'completed')->count(),
        ];
    }
}
