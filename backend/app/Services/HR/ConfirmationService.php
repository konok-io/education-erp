<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Models\HR\ConfirmationRecord;
use App\Models\HR\ServiceBook;
use App\Models\Employee\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConfirmationService
{
    // ===================== CONFIRMATION RECORDS =====================

    public function getConfirmations(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = ConfirmationRecord::with(['employee.profile']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['pending'])) {
            $query->where('status', ConfirmationRecord::STATUS_PENDING);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createConfirmation(array $data): ConfirmationRecord
    {
        return ConfirmationRecord::create([
            'uuid' => (string) Str::uuid(),
            'confirmation_no' => ConfirmationRecord::generateConfirmationNo(),
            'employee_id' => $data['employee_id'],
            'probation_start_date' => $data['probation_start_date'],
            'probation_end_date' => $data['probation_end_date'],
            'performance_summary' => $data['performance_summary'] ?? null,
            'recommendation' => $data['recommendation'] ?? ConfirmationRecord::RECOMMEND_CONFIRM,
            'recommendation_remarks' => $data['recommendation_remarks'] ?? null,
            'status' => ConfirmationRecord::STATUS_PENDING,
            'remarks' => $data['remarks'] ?? null,
        ]);
    }

    public function recommendConfirmation(string $uuid, array $data, int $userId): ConfirmationRecord
    {
        $record = ConfirmationRecord::where('uuid', $uuid)->firstOrFail();
        $record->update([
            'recommendation' => $data['recommendation'],
            'recommendation_remarks' => $data['remarks'] ?? null,
            'status' => ConfirmationRecord::STATUS_RECOMMENDED,
            'recommended_by' => $userId,
            'recommended_date' => now(),
        ]);
        return $record->fresh();
    }

    public function reviewConfirmation(string $uuid, int $userId): ConfirmationRecord
    {
        $record = ConfirmationRecord::where('uuid', $uuid)->firstOrFail();
        $record->update([
            'status' => ConfirmationRecord::STATUS_UNDER_REVIEW,
            'reviewed_by' => $userId,
            'reviewed_date' => now(),
        ]);
        return $record->fresh();
    }

    public function approveConfirmation(string $uuid, int $userId): ConfirmationRecord
    {
        return DB::transaction(function () use ($uuid, $userId) {
            $record = ConfirmationRecord::where('uuid', $uuid)->firstOrFail();
            $employee = Employee::findOrFail($record->employee_id);

            // Update employee
            $employee->update([
                'confirmation_date' => now(),
                'status' => Employee::STATUS_ACTIVE,
            ]);

            // Update confirmation record
            $record->update([
                'status' => ConfirmationRecord::STATUS_APPROVED,
                'approved_by' => $userId,
                'approved_date' => now(),
                'confirmation_date' => now(),
            ]);

            // Create service book entry
            ServiceBook::create([
                'uuid' => (string) Str::uuid(),
                'employee_id' => $record->employee_id,
                'entry_no' => ServiceBook::generateEntryNo(),
                'entry_date' => now(),
                'event_type' => ServiceBook::EVENT_CONFIRMATION,
                'title' => 'Employment Confirmed',
                'description' => 'Probation period completed. Employment confirmed.',
                'approved_by' => $userId,
                'approved_date' => now(),
            ]);

            return $record->fresh();
        });
    }

    public function rejectConfirmation(string $uuid, array $data, int $userId): ConfirmationRecord
    {
        $record = ConfirmationRecord::where('uuid', $uuid)->firstOrFail();
        $record->update([
            'status' => ConfirmationRecord::STATUS_REJECTED,
            'remarks' => $data['reason'] ?? null,
            'approved_by' => $userId,
            'approved_date' => now(),
        ]);
        return $record->fresh();
    }

    public function getPendingConfirmations(): \Illuminate\Database\Eloquent\Collection
    {
        return ConfirmationRecord::where('status', ConfirmationRecord::STATUS_PENDING)
            ->whereDate('probation_end_date', '<=', now())
            ->with(['employee.profile', 'employee.department', 'employee.designation'])
            ->get();
    }

    public function getConfirmationStats(): array
    {
        return [
            'pending' => ConfirmationRecord::where('status', ConfirmationRecord::STATUS_PENDING)->count(),
            'under_review' => ConfirmationRecord::where('status', ConfirmationRecord::STATUS_UNDER_REVIEW)->count(),
            'recommended' => ConfirmationRecord::where('status', ConfirmationRecord::STATUS_RECOMMENDED)->count(),
            'approved' => ConfirmationRecord::where('status', ConfirmationRecord::STATUS_APPROVED)->count(),
            'due_this_month' => ConfirmationRecord::where('status', ConfirmationRecord::STATUS_PENDING)
                ->whereMonth('probation_end_date', now()->month)
                ->whereYear('probation_end_date', now()->year)
                ->count(),
        ];
    }
}
