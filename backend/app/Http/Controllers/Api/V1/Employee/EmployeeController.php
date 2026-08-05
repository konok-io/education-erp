<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Employee;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Employee\EmployeeResource;
use App\Services\Employee\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeController extends BaseController
{
    public function __construct(
        private readonly EmployeeService $employeeService
    ) {}

    // ===================== CRUD =====================

    public function index(Request $request): AnonymousResourceCollection
    {
        $employees = $this->employeeService->getAll(
            perPage: $request->input('per_page', 20),
            filters: $request->only([
                'search', 'department_id', 'designation_id', 'status', 
                'employment_type_id', 'shift_id'
            ])
        );

        return EmployeeResource::collection($employees);
    }

    public function store(Request $request): JsonResponse
    {
        $employee = $this->employeeService->create($request->all());

        return $this->created(
            new EmployeeResource($employee->load($this->employeeService->getRelations())),
            'Employee registered successfully'
        );
    }

    public function show(string $uuid): JsonResponse
    {
        $employee = $this->employeeService->findByUuid($uuid);

        if (!$employee) {
            return $this->notFound('Employee not found');
        }

        return $this->success(
            new EmployeeResource($employee->load($this->employeeService->getRelations()))
        );
    }

    public function update(Request $request, string $uuid): JsonResponse
    {
        $employee = $this->employeeService->findByUuid($uuid);

        if (!$employee) {
            return $this->notFound('Employee not found');
        }

        $updatedEmployee = $this->employeeService->update($employee, $request->all());

        return $this->updated(
            new EmployeeResource($updatedEmployee->load($this->employeeService->getRelations())),
            'Employee updated successfully'
        );
    }

    public function destroy(string $uuid): JsonResponse
    {
        $employee = $this->employeeService->findByUuid($uuid);

        if (!$employee) {
            return $this->notFound('Employee not found');
        }

        $this->employeeService->delete($employee);

        return $this->deleted('Employee deleted successfully');
    }

    // ===================== SEARCH =====================

    public function search(Request $request): AnonymousResourceCollection
    {
        $employees = $this->employeeService->search(
            query: $request->input('q'),
            perPage: $request->input('per_page', 20)
        );

        return EmployeeResource::collection($employees);
    }

    // ===================== PROFILE =====================

    public function updateProfile(Request $request, string $uuid): JsonResponse
    {
        $employee = $this->employeeService->findByUuid($uuid);

        if (!$employee) {
            return $this->notFound('Employee not found');
        }

        $this->employeeService->updateProfile($employee, $request->all());

        return $this->success(null, 'Profile updated successfully');
    }

    public function updatePhoto(Request $request, string $uuid): JsonResponse
    {
        $employee = $this->employeeService->findByUuid($uuid);

        if (!$employee) {
            return $this->notFound('Employee not found');
        }

        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $photoUrl = $this->employeeService->updatePhoto($employee, $request->file('photo'));

        return $this->success(['photo_url' => $photoUrl], 'Photo updated successfully');
    }

    // ===================== STATUS =====================

    public function updateStatus(Request $request, string $uuid): JsonResponse
    {
        $employee = $this->employeeService->findByUuid($uuid);

        if (!$employee) {
            return $this->notFound('Employee not found');
        }

        $request->validate([
            'status' => ['required', 'string', 'in:pending,active,inactive,on_leave,suspended,retired,resigned,terminated'],
            'remarks' => ['nullable', 'string'],
        ]);

        $this->employeeService->updateStatus($employee, $request->status, $request->remarks);

        return $this->success(null, 'Status updated successfully');
    }

    // ===================== SALARY =====================

    public function updateSalary(Request $request, string $uuid): JsonResponse
    {
        $employee = $this->employeeService->findByUuid($uuid);

        if (!$employee) {
            return $this->notFound('Employee not found');
        }

        $salary = $this->employeeService->updateSalary($employee, $request->all());

        return $this->success($salary, 'Salary updated successfully');
    }

    // ===================== LEAVE =====================

    public function applyLeave(Request $request, string $uuid): JsonResponse
    {
        $employee = $this->employeeService->findByUuid($uuid);

        if (!$employee) {
            return $this->notFound('Employee not found');
        }

        $leave = $this->employeeService->applyLeave($employee, $request->all());

        return $this->success($leave, 'Leave applied successfully');
    }

    public function getLeaveHistory(string $uuid): JsonResponse
    {
        $employee = $this->employeeService->findByUuid($uuid);

        if (!$employee) {
            return $this->notFound('Employee not found');
        }

        return $this->success($employee->leaves);
    }

    // ===================== QR CODE =====================

    public function generateQRCode(string $uuid): JsonResponse
    {
        $employee = $this->employeeService->findByUuid($uuid);

        if (!$employee) {
            return $this->notFound('Employee not found');
        }

        $qrCode = $this->employeeService->generateQRCode($employee);

        return $this->success(['qr_code' => $qrCode]);
    }

    // ===================== IMPORT/EXPORT =====================

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,xlsx,xls', 'max:10240'],
        ]);

        $result = $this->employeeService->import($request->file('file'));

        return $this->success($result, 'Import completed');
    }

    public function export(Request $request): JsonResponse
    {
        $url = $this->employeeService->export(
            $request->input('format', 'excel'),
            $request->only(['department_id', 'status'])
        );

        return $this->success(['url' => $url], 'Export ready');
    }

    // ===================== STATISTICS =====================

    public function statistics(): JsonResponse
    {
        $stats = $this->employeeService->getStatistics();

        return $this->success($stats);
    }

    public function activeCount(): JsonResponse
    {
        $count = $this->employeeService->getActiveCount();

        return $this->success(['count' => $count]);
    }

    // ===================== LOOKUPS =====================

    public function getDepartments(): JsonResponse
    {
        $departments = $this->employeeService->getDepartments();

        return $this->success($departments);
    }

    public function getDesignations(): JsonResponse
    {
        $designations = $this->employeeService->getDesignations();

        return $this->success($designations);
    }

    public function getShifts(): JsonResponse
    {
        $shifts = $this->employeeService->getShifts();

        return $this->success($shifts);
    }

    public function getSalaryGrades(): JsonResponse
    {
        $grades = $this->employeeService->getSalaryGrades();

        return $this->success($grades);
    }
}
