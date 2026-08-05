<?php

declare(strict_types=1);

namespace App\Services\Employee;

use App\Helpers\ImageHelper;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeProfile;
use App\Models\Employee\EmployeeDocument;
use App\Models\Employee\EmployeeEmergencyContact;
use App\Models\Employee\EmployeeLeave;
use App\Models\Employee\EmployeeSalary;
use App\Models\Employee\Designation;
use App\Models\Employee\EmploymentType;
use App\Models\Employee\SalaryGrade;
use App\Models\Employee\Shift;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EmployeeService
{
    public function getRelations(): array
    {
        return [
            'profile',
            'department',
            'designation',
            'employmentType',
            'salaryGrade',
            'shift',
            'documents',
            'emergencyContacts',
            'salary',
            'campus',
        ];
    }

    public function getAll(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Employee::query()->with(['profile', 'department', 'designation']);

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['department_id'])) {
            $query->byDepartment($filters['department_id']);
        }

        if (!empty($filters['designation_id'])) {
            $query->where('designation_id', $filters['designation_id']);
        }

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (!empty($filters['employment_type_id'])) {
            $query->where('employment_type_id', $filters['employment_type_id']);
        }

        if (!empty($filters['shift_id'])) {
            $query->where('shift_id', $filters['shift_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            $employeeNo = Employee::generateEmployeeNo(now()->format('Y'));

            $user = User::create([
                'uuid' => (string) Str::uuid(),
                'campus_id' => $data['campus_id'] ?? auth()->user()->campus_id,
                'name' => $data['first_name'] . ' ' . ($data['last_name'] ?? ''),
                'email' => $data['email'],
                'mobile' => $data['mobile'] ?? null,
                'password' => Hash::make($data['password'] ?? 'Employee@123'),
                'role_id' => $this->getEmployeeRoleId(),
                'status' => 'active',
            ]);

            DB::table('model_has_roles')->insert([
                'role_id' => $this->getEmployeeRoleId(),
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id,
            ]);

            $employee = Employee::create([
                'uuid' => (string) Str::uuid(),
                'employee_no' => $employeeNo,
                'user_id' => $user->id,
                'campus_id' => $data['campus_id'] ?? auth()->user()->campus_id,
                'department_id' => $data['department_id'],
                'designation_id' => $data['designation_id'] ?? null,
                'employment_type_id' => $data['employment_type_id'] ?? null,
                'salary_grade_id' => $data['salary_grade_id'] ?? null,
                'shift_id' => $data['shift_id'] ?? null,
                'joining_date' => $data['joining_date'] ?? now(),
                'status' => Employee::STATUS_PENDING,
                'remarks' => $data['remarks'] ?? null,
            ]);

            $this->createProfile($employee, $data);

            return $employee;
        });
    }

    private function createProfile(Employee $employee, array $data): EmployeeProfile
    {
        return EmployeeProfile::create([
            'uuid' => (string) Str::uuid(),
            'employeeable_type' => Employee::class,
            'employeeable_id' => $employee->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'first_name_bn' => $data['first_name_bn'] ?? null,
            'last_name_bn' => $data['last_name_bn'] ?? null,
            'gender' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'blood_group' => $data['blood_group'] ?? null,
            'religion' => $data['religion'] ?? null,
            'nationality' => $data['nationality'] ?? 'Bangladeshi',
            'nid' => $data['nid'] ?? null,
            'passport' => $data['passport'] ?? null,
            'email' => $data['email'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'marital_status' => $data['marital_status'] ?? null,
            'father_name' => $data['father_name'] ?? null,
            'mother_name' => $data['mother_name'] ?? null,
            'present_address' => $data['present_address'] ?? null,
            'permanent_address' => $data['permanent_address'] ?? null,
        ]);
    }

    public function findByUuid(string $uuid): ?Employee
    {
        return Employee::where('uuid', $uuid)->first();
    }

    public function update(Employee $employee, array $data): Employee
    {
        return DB::transaction(function () use ($employee, $data) {
            $employeeFields = array_intersect_key($data, array_flip([
                'department_id', 'designation_id', 'employment_type_id', 
                'salary_grade_id', 'shift_id', 'joining_date', 'remarks'
            ]));

            if (!empty($employeeFields)) {
                $employee->update($employeeFields);
            }

            if (!empty($data['profile'])) {
                $this->updateProfile($employee, $data['profile']);
            }

            return $employee->fresh($this->getRelations());
        });
    }

    public function updateProfile(Employee $employee, array $data): EmployeeProfile
    {
        $profile = $employee->profile;

        if (!$profile) {
            return $this->createProfile($employee, $data);
        }

        $profile->update(array_intersect_key($data, array_flip([
            'first_name', 'last_name', 'first_name_bn', 'last_name_bn',
            'gender', 'date_of_birth', 'blood_group', 'religion', 'nationality',
            'nid', 'passport', 'email', 'mobile', 'marital_status',
            'father_name', 'mother_name', 'present_address', 'permanent_address'
        ])));

        if (isset($data['first_name']) || isset($data['last_name'])) {
            $employee->user->update([
                'name' => trim(($data['first_name'] ?? $profile->first_name) . ' ' . ($data['last_name'] ?? $profile->last_name ?? ''))
            ]);
        }

        return $profile->fresh();
    }

    public function updatePhoto(Employee $employee, UploadedFile $photo): string
    {
        $profile = $employee->profile;

        if ($profile?->photo) {
            ImageHelper::delete($profile->photo);
        }

        $path = ImageHelper::uploadAndResize($photo, 'employees/photos', 300, 300);

        if ($profile) {
            $profile->update(['photo' => $path]);
        }

        return asset('storage/' . $path);
    }

    public function updateStatus(Employee $employee, string $status, ?string $remarks = null): void
    {
        $employee->update([
            'status' => $status,
            'remarks' => $remarks,
        ]);
    }

    public function updateSalary(Employee $employee, array $data): EmployeeSalary
    {
        $salary = $employee->salary ?? new EmployeeSalary([
            'employeeable_type' => Employee::class,
            'employeeable_id' => $employee->id,
            'uuid' => (string) Str::uuid(),
        ]);

        $salary->fill([
            'basic_salary' => $data['basic_salary'] ?? 0,
            'house_rent' => $data['house_rent'] ?? 0,
            'medical_allowance' => $data['medical_allowance'] ?? 0,
            'transport_allowance' => $data['transport_allowance'] ?? 0,
            'special_allowance' => $data['special_allowance'] ?? 0,
            'provident_fund' => $data['provident_fund'] ?? 0,
            'tax_deduction' => $data['tax_deduction'] ?? 0,
            'other_deduction' => $data['other_deduction'] ?? 0,
            'effective_date' => $data['effective_date'] ?? now(),
            'payment_method' => $data['payment_method'] ?? 'bank',
            'bank_name' => $data['bank_name'] ?? null,
            'account_number' => $data['account_number'] ?? null,
        ]);

        $salary->calculateNetSalary();
        $salary->is_current = true;
        $salary->save();

        return $salary;
    }

    public function applyLeave(Employee $employee, array $data): EmployeeLeave
    {
        return EmployeeLeave::create([
            'uuid' => (string) Str::uuid(),
            'employeeable_type' => Employee::class,
            'employeeable_id' => $employee->id,
            'leave_type' => $data['leave_type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'total_days' => $data['total_days'] ?? 1,
            'reason' => $data['reason'],
            'status' => EmployeeLeave::STATUS_PENDING,
            'applied_by' => auth()->id(),
            'applied_at' => now(),
        ]);
    }

    public function generateQRCode(Employee $employee): string
    {
        $data = [
            'uuid' => $employee->uuid,
            'employee_no' => $employee->employee_no,
            'name' => $employee->full_name,
        ];

        return 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')
                ->size(200)
                ->generate(json_encode($data))
        );
    }

    public function search(string $query, int $perPage = 20): LengthAwarePaginator
    {
        return Employee::search($query)
            ->with(['profile', 'department', 'designation'])
            ->paginate($perPage);
    }

    public function delete(Employee $employee): bool
    {
        return $employee->delete();
    }

    public function getStatistics(): array
    {
        return [
            'total' => Employee::count(),
            'active' => Employee::where('status', Employee::STATUS_ACTIVE)->count(),
            'pending' => Employee::where('status', Employee::STATUS_PENDING)->count(),
            'on_leave' => Employee::where('status', Employee::STATUS_ON_LEAVE)->count(),
        ];
    }

    public function getActiveCount(): int
    {
        return Employee::where('status', Employee::STATUS_ACTIVE)->count();
    }

    public function import(UploadedFile $file): array
    {
        return ['total' => 0, 'success' => 0, 'failed' => 0, 'errors' => []];
    }

    public function export(string $format, array $filters = []): string
    {
        $filename = 'employees_export_' . now()->format('Y_m_d_H_i_s');
        return url('storage/exports/' . $filename . '.' . $format);
    }

    // ===================== LOOKUPS =====================

    public function getDepartments(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\Academic\Department::active()->get();
    }

    public function getDesignations(): \Illuminate\Database\Eloquent\Collection
    {
        return Designation::active()->orderBy('level', 'desc')->get();
    }

    public function getEmploymentTypes(): \Illuminate\Database\Eloquent\Collection
    {
        return EmploymentType::active()->get();
    }

    public function getSalaryGrades(): \Illuminate\Database\Eloquent\Collection
    {
        return SalaryGrade::active()->orderBy('grade_name')->get();
    }

    public function getShifts(): \Illuminate\Database\Eloquent\Collection
    {
        return Shift::active()->get();
    }

    private function getEmployeeRoleId(): int
    {
        return DB::table('roles')->where('name', 'employee')->value('id') ?? 0;
    }
}
