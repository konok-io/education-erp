<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Models\HR\EmployeeTransfer;
use App\Models\HR\ServiceBook;
use App\Models\Employee\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransferService
{
    // ===================== TRANSFERS =====================

    public function getTransfers(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = EmployeeTransfer::with([
            'employee.profile',
            'fromDepartment',
            'toDepartment',
            'fromDesignation',
            'toDesignation',
        ]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['from_department_id'])) {
            $query->where('from_department_id', $filters['from_department_id']);
        }

        if (!empty($filters['to_department_id'])) {
            $query->where('to_department_id', $filters['to_department_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('transfer_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('transfer_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createTransfer(array $data): EmployeeTransfer
    {
        return DB::transaction(function () use ($data) {
            $transfer = EmployeeTransfer::create([
                'uuid' => (string) Str::uuid(),
                'transfer_no' => EmployeeTransfer::generateTransferNo(),
                'employee_id' => $data['employee_id'],
                'from_department_id' => $data['from_department_id'] ?? null,
                'to_department_id' => $data['to_department_id'] ?? null,
                'from_designation_id' => $data['from_designation_id'] ?? null,
                'to_designation_id' => $data['to_designation_id'] ?? null,
                'from_campus_id' => $data['from_campus_id'] ?? null,
                'to_campus_id' => $data['to_campus_id'] ?? null,
                'from_shift_id' => $data['from_shift_id'] ?? null,
                'to_shift_id' => $data['to_shift_id'] ?? null,
                'reporting_manager_id' => $data['reporting_manager_id'] ?? null,
                'transfer_date' => $data['transfer_date'],
                'effective_date' => $data['effective_date'],
                'transfer_type' => $data['transfer_type'] ?? EmployeeTransfer::TYPE_COMBINED,
                'reason' => $data['reason'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'status' => EmployeeTransfer::STATUS_PENDING,
            ]);

            return $transfer;
        });
    }

    public function recommendTransfer(string $uuid, int $userId): EmployeeTransfer
    {
        $transfer = EmployeeTransfer::where('uuid', $uuid)->firstOrFail();
        $transfer->update([
            'status' => EmployeeTransfer::STATUS_RECOMMENDED,
            'recommended_by' => $userId,
            'recommended_date' => now(),
        ]);
        return $transfer->fresh();
    }

    public function approveTransfer(string $uuid, int $userId): EmployeeTransfer
    {
        return DB::transaction(function () use ($uuid, $userId) {
            $transfer = EmployeeTransfer::where('uuid', $uuid)->firstOrFail();
            $employee = Employee::findOrFail($transfer->employee_id);

            // Update employee record
            $updateData = [];

            if ($transfer->to_department_id) {
                $updateData['department_id'] = $transfer->to_department_id;
            }
            if ($transfer->to_designation_id) {
                $updateData['designation_id'] = $transfer->to_designation_id;
            }
            if ($transfer->to_campus_id) {
                $updateData['campus_id'] = $transfer->to_campus_id;
            }
            if ($transfer->to_shift_id) {
                $updateData['shift_id'] = $transfer->to_shift_id;
            }

            if (!empty($updateData)) {
                $employee->update($updateData);
            }

            // Update transfer status
            $transfer->update([
                'status' => EmployeeTransfer::STATUS_APPROVED,
                'approved_by' => $userId,
                'approved_date' => now(),
            ]);

            // Create service book entry
            ServiceBook::create([
                'uuid' => (string) Str::uuid(),
                'employee_id' => $transfer->employee_id,
                'entry_no' => ServiceBook::generateEntryNo(),
                'entry_date' => now(),
                'event_type' => ServiceBook::EVENT_TRANSFER,
                'title' => 'Transfer',
                'description' => $transfer->transfer_summary,
                'metadata' => [
                    'from_department' => $transfer->from_department_id,
                    'to_department' => $transfer->to_department_id,
                    'from_designation' => $transfer->from_designation_id,
                    'to_designation' => $transfer->to_designation_id,
                    'from_campus' => $transfer->from_campus_id,
                    'to_campus' => $transfer->to_campus_id,
                ],
                'approved_by' => $userId,
                'approved_date' => now(),
            ]);

            return $transfer->fresh();
        });
    }

    public function cancelTransfer(string $uuid): EmployeeTransfer
    {
        $transfer = EmployeeTransfer::where('uuid', $uuid)->firstOrFail();
        $transfer->update(['status' => EmployeeTransfer::STATUS_CANCELLED]);
        return $transfer->fresh();
    }

    // ===================== STATISTICS =====================

    public function getTransferStats(): array
    {
        return [
            'pending' => EmployeeTransfer::where('status', EmployeeTransfer::STATUS_PENDING)->count(),
            'recommended' => EmployeeTransfer::where('status', EmployeeTransfer::STATUS_RECOMMENDED)->count(),
            'approved' => EmployeeTransfer::where('status', EmployeeTransfer::STATUS_APPROVED)->count(),
            'total' => EmployeeTransfer::count(),
        ];
    }
}
