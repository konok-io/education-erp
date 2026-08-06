<?php

declare(strict_types=1);

namespace App\Services\Convocation;

use App\Models\Convocation\Convocation;
use App\Models\Convocation\ConvocationRegistration;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConvocationService
{
    public function getConvocations(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Convocation::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('convocation_no', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('ceremony_date', 'desc')->paginate($perPage);
    }

    public function createConvocation(array $data): Convocation
    {
        return DB::transaction(function () use ($data) {
            return Convocation::create([
                'uuid' => (string) Str::uuid(),
                'convocation_no' => Convocation::generateConvocationNo(),
                'name' => $data['name'],
                'name_bn' => $data['name_bn'] ?? null,
                'year' => $data['year'],
                'semester' => $data['semester'] ?? null,
                'ceremony_date' => $data['ceremony_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'venue' => $data['venue'],
                'address' => $data['address'] ?? null,
                'chief_guest' => $data['chief_guest'] ?? null,
                'special_guest' => $data['special_guest'] ?? null,
                'guest_speaker' => $data['guest_speaker'] ?? null,
                'agenda' => $data['agenda'] ?? null,
                'expected_attendees' => $data['expected_attendees'] ?? 0,
                'registration_fee' => $data['registration_fee'] ?? 0,
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'planning',
            ]);
        });
    }

    public function updateConvocation(string $uuid, array $data): Convocation
    {
        $convocation = Convocation::where('uuid', $uuid)->firstOrFail();
        $convocation->update($data);
        return $convocation->fresh();
    }

    public function openRegistration(string $uuid): Convocation
    {
        $convocation = Convocation::where('uuid', $uuid)->firstOrFail();
        $convocation->update(['status' => 'registration']);
        return $convocation->fresh();
    }

    public function closeRegistration(string $uuid): Convocation
    {
        $convocation = Convocation::where('uuid', $uuid)->firstOrFail();
        $convocation->update(['status' => 'confirmed']);
        return $convocation->fresh();
    }

    // ===================== REGISTRATIONS =====================

    public function getRegistrations(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = ConvocationRegistration::with(['convocation', 'alumni']);

        if (!empty($filters['convocation_id'])) {
            $query->where('convocation_id', $filters['convocation_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['attendance'])) {
            $query->where('attendance', $filters['attendance']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('roll_number', 'like', "%{$filters['search']}%")
                    ->orWhere('registration_no', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function registerAlumni(array $data): ConvocationRegistration
    {
        return DB::transaction(function () use ($data) {
            $convocation = Convocation::findOrFail($data['convocation_id']);

            $registration = ConvocationRegistration::create([
                'uuid' => (string) Str::uuid(),
                'registration_no' => ConvocationRegistration::generateRegistrationNo(),
                'convocation_id' => $data['convocation_id'],
                'alumni_id' => $data['alumni_id'] ?? null,
                'name' => $data['name'],
                'name_bn' => $data['name_bn'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'roll_number' => $data['roll_number'] ?? null,
                'registration_no_old' => $data['registration_no_old'] ?? null,
                'department' => $data['department'] ?? null,
                'program' => $data['program'] ?? null,
                'passing_year' => $data['passing_year'] ?? null,
                'registration_fee' => $convocation->registration_fee,
                'guest_name' => $data['guest_name'] ?? null,
                'guest_relation' => $data['guest_relation'] ?? null,
                'total_guests' => $data['total_guests'] ?? 0,
                'dietary_requirements' => $data['dietary_requirements'] ?? null,
                'accessibility_needs' => $data['accessibility_needs'] ?? null,
                'status' => 'pending',
            ]);

            $convocation->updateRegistrationCount();

            return $registration;
        });
    }

    public function confirmRegistration(string $uuid): ConvocationRegistration
    {
        $registration = ConvocationRegistration::where('uuid', $uuid)->firstOrFail();
        $registration->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'paid_amount' => $registration->registration_fee,
            'payment_date' => now(),
        ]);

        $registration->convocation->updateRegistrationCount();

        return $registration->fresh();
    }

    public function markAttendance(string $uuid, string $status = 'attended'): ConvocationRegistration
    {
        $registration = ConvocationRegistration::where('uuid', $uuid)->firstOrFail();
        $registration->update(['attendance' => $status]);
        return $registration->fresh();
    }

    // ===================== DASHBOARD =====================

    public function getDashboardStats(): array
    {
        $totalConvocations = Convocation::count();
        $upcoming = Convocation::where('ceremony_date', '>=', now()->toDateString())
            ->whereIn('status', ['planning', 'registration'])
            ->count();
        $totalRegistrations = ConvocationRegistration::count();
        $confirmedRegistrations = ConvocationRegistration::where('status', 'confirmed')->count();
        $totalAttendees = ConvocationRegistration::where('attendance', 'attended')->count();
        $totalCollected = ConvocationRegistration::where('status', 'confirmed')
            ->sum('paid_amount');

        return [
            'total_convocations' => $totalConvocations,
            'upcoming_convocations' => $upcoming,
            'total_registrations' => $totalRegistrations,
            'confirmed_registrations' => $confirmedRegistrations,
            'total_attendees' => $totalAttendees,
            'total_collected' => $totalCollected,
        ];
    }
}
