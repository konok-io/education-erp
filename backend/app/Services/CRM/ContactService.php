<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Models\CRM\CrmContact;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContactService
{
    public function getContacts(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = CrmContact::with(['student', 'guardian', 'employee']);

        if (!empty($filters['contact_type'])) {
            $query->where('contact_type', $filters['contact_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['tags'])) {
            $query->whereJsonContains('tags', $filters['tags']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createContact(array $data): CrmContact
    {
        return CrmContact::create([
            'uuid' => (string) Str::uuid(),
            'contact_no' => CrmContact::generateContactNo(),
            'full_name' => $data['full_name'],
            'photo' => $data['photo'] ?? null,
            'contact_type' => $data['contact_type'],
            'mobile' => $data['mobile'] ?? null,
            'alternative_mobile' => $data['alternative_mobile'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'present_address' => $data['present_address'] ?? null,
            'permanent_address' => $data['permanent_address'] ?? null,
            'district' => $data['district'] ?? null,
            'division' => $data['division'] ?? null,
            'country' => $data['country'] ?? null,
            'organization' => $data['organization'] ?? null,
            'designation' => $data['designation'] ?? null,
            'student_id' => $data['student_id'] ?? null,
            'guardian_id' => $data['guardian_id'] ?? null,
            'employee_id' => $data['employee_id'] ?? null,
            'social_links' => $data['social_links'] ?? null,
            'tags' => $data['tags'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'] ?? CrmContact::STATUS_ACTIVE,
        ]);
    }

    public function updateContact(string $uuid, array $data): CrmContact
    {
        $contact = CrmContact::where('uuid', $uuid)->firstOrFail();
        $contact->update($data);
        return $contact->fresh();
    }

    public function getContactStats(): array
    {
        $contacts = CrmContact::query();
        
        return [
            'total' => $contacts->count(),
            'by_type' => CrmContact::selectRaw('contact_type, COUNT(*) as count')
                ->groupBy('contact_type')
                ->pluck('count', 'contact_type'),
            'active' => (clone $contacts)->where('status', CrmContact::STATUS_ACTIVE)->count(),
            'inactive' => (clone $contacts)->where('status', CrmContact::STATUS_INACTIVE)->count(),
            'blocked' => (clone $contacts)->where('status', CrmContact::STATUS_BLOCKED)->count(),
        ];
    }

    public function checkDuplicate(string $mobile, ?string $email, ?int $excludeId = null): ?CrmContact
    {
        $query = CrmContact::query();

        if ($mobile) {
            $query->orWhere('mobile', $mobile);
        }

        if ($email) {
            $query->orWhere('email', $email);
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }
}
