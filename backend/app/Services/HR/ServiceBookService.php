<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Models\HR\ServiceBook;
use App\Models\Employee\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ServiceBookService
{
    // ===================== SERVICE BOOK =====================

    public function getServiceBooks(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = ServiceBook::with(['employee.profile']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('entry_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('entry_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('entry_date', 'desc')->paginate($perPage);
    }

    public function getEmployeeServiceBook(int $employeeId): \Illuminate\Database\Eloquent\Collection
    {
        return ServiceBook::where('employee_id', $employeeId)
            ->with('approver')
            ->orderBy('entry_date', 'desc')
            ->get();
    }

    public function createServiceBookEntry(array $data): ServiceBook
    {
        return ServiceBook::create([
            'uuid' => (string) Str::uuid(),
            'employee_id' => $data['employee_id'],
            'entry_no' => ServiceBook::generateEntryNo(),
            'entry_date' => $data['entry_date'],
            'event_type' => $data['event_type'],
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'approved_by' => $data['approved_by'] ?? null,
            'approved_date' => $data['approved_date'] ?? null,
            'remarks' => $data['remarks'] ?? null,
        ]);
    }

    public function addPromotionEntry(array $data, int $userId): ServiceBook
    {
        return $this->createServiceBookEntry([
            'employee_id' => $data['employee_id'],
            'entry_date' => $data['promotion_date'] ?? now(),
            'event_type' => ServiceBook::EVENT_PROMOTION,
            'title' => 'Promotion',
            'description' => $data['description'] ?? null,
            'metadata' => [
                'previous_designation' => $data['previous_designation_id'] ?? null,
                'new_designation' => $data['new_designation_id'] ?? null,
                'previous_department' => $data['previous_department_id'] ?? null,
                'new_department' => $data['new_department_id'] ?? null,
                'previous_basic' => $data['previous_basic'] ?? null,
                'new_basic' => $data['new_basic'] ?? null,
                'previous_grade' => $data['previous_grade_id'] ?? null,
                'new_grade' => $data['new_grade_id'] ?? null,
            ],
            'approved_by' => $userId,
            'approved_date' => now(),
        ]);
    }

    public function getServiceBookTimeline(int $employeeId): array
    {
        $entries = ServiceBook::where('employee_id', $employeeId)
            ->with('approver')
            ->orderBy('entry_date', 'asc')
            ->get();

        $timeline = [];
        foreach ($entries as $entry) {
            $timeline[] = [
                'id' => $entry->uuid,
                'date' => $entry->entry_date->format('Y-m-d'),
                'event_type' => $entry->event_type,
                'event_label' => ServiceBook::eventTypes()[$entry->event_type] ?? $entry->event_type,
                'icon' => $entry->event_icon,
                'title' => $entry->title,
                'description' => $entry->description,
                'metadata' => $entry->metadata,
                'approved_by' => $entry->approver?->name,
                'remarks' => $entry->remarks,
            ];
        }

        return $timeline;
    }

    public function getEmployeeTenure(int $employeeId): array
    {
        $employee = Employee::findOrFail($employeeId);
        $joiningDate = $employee->joining_date;

        if (!$joiningDate) {
            return ['years' => 0, 'months' => 0, 'days' => 0, 'total_days' => 0];
        }

        $now = now();
        $diff = $joiningDate->diff($now);

        return [
            'years' => $diff->y,
            'months' => $diff->m,
            'days' => $diff->d,
            'total_days' => $joiningDate->diffInDays($now),
        ];
    }
}
