<?php

declare(strict_types=1);

namespace App\Services\Teacher;

use App\Helpers\ImageHelper;
use App\Models\Teacher\Teacher;
use App\Models\Teacher\TeacherProfile;
use App\Models\Teacher\TeacherQualification;
use App\Models\Teacher\TeacherExperience;
use App\Models\Teacher\TeacherSubjectAssignment;
use App\Models\Teacher\TeacherClassAssignment;
use App\Models\Teacher\TeacherSalary;
use App\Models\Teacher\TeacherLeave;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TeacherService
{
    public function getRelations(): array
    {
        return [
            'profile',
            'department',
            'qualifications',
            'experiences',
            'documents',
            'subjectAssignments.subject',
            'classAssignments.class',
            'salary',
            'campus',
        ];
    }

    public function getAll(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Teacher::query()->with(['profile', 'department']);

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['department_id'])) {
            $query->byDepartment($filters['department_id']);
        }

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (!empty($filters['employment_type'])) {
            $query->where('employment_type', $filters['employment_type']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): Teacher
    {
        return DB::transaction(function () use ($data) {
            $teacherNo = Teacher::generateTeacherNo(now()->format('Y'));

            $user = User::create([
                'uuid' => (string) Str::uuid(),
                'campus_id' => $data['campus_id'] ?? auth()->user()->campus_id,
                'name' => $data['first_name'] . ' ' . ($data['last_name'] ?? ''),
                'email' => $data['email'],
                'mobile' => $data['mobile'] ?? null,
                'password' => Hash::make($data['password'] ?? 'Teacher@123'),
                'role_id' => $this->getTeacherRoleId(),
                'status' => 'active',
            ]);

            DB::table('model_has_roles')->insert([
                'role_id' => $this->getTeacherRoleId(),
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id,
            ]);

            $teacher = Teacher::create([
                'uuid' => (string) Str::uuid(),
                'teacher_no' => $teacherNo,
                'user_id' => $user->id,
                'campus_id' => $data['campus_id'] ?? auth()->user()->campus_id,
                'department_id' => $data['department_id'],
                'designation_id' => $data['designation_id'] ?? null,
                'employment_type' => $data['employment_type'],
                'joining_date' => $data['joining_date'] ?? now(),
                'status' => Teacher::STATUS_PENDING,
                'remarks' => $data['remarks'] ?? null,
            ]);

            $this->createProfile($teacher, $data);

            if (!empty($data['qualifications'])) {
                foreach ($data['qualifications'] as $qual) {
                    $this->addQualification($teacher, $qual);
                }
            }

            if (!empty($data['experiences'])) {
                foreach ($data['experiences'] as $exp) {
                    $this->addExperience($teacher, $exp);
                }
            }

            return $teacher;
        });
    }

    private function createProfile(Teacher $teacher, array $data): TeacherProfile
    {
        return TeacherProfile::create([
            'uuid' => (string) Str::uuid(),
            'teacherable_type' => Teacher::class,
            'teacherable_id' => $teacher->id,
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
            'present_address' => $data['present_address'] ?? null,
            'permanent_address' => $data['permanent_address'] ?? null,
        ]);
    }

    public function findByUuid(string $uuid): ?Teacher
    {
        return Teacher::where('uuid', $uuid)->first();
    }

    public function findByTeacherNo(string $teacherNo): ?Teacher
    {
        return Teacher::where('teacher_no', $teacherNo)
            ->with($this->getRelations())
            ->first();
    }

    public function update(Teacher $teacher, array $data): Teacher
    {
        return DB::transaction(function () use ($teacher, $data) {
            $teacherFields = array_intersect_key($data, array_flip([
                'department_id', 'designation_id', 'employment_type', 'joining_date', 'remarks'
            ]));

            if (!empty($teacherFields)) {
                $teacher->update($teacherFields);
            }

            if (!empty($data['profile'])) {
                $this->updateProfile($teacher, $data['profile']);
            }

            return $teacher->fresh($this->getRelations());
        });
    }

    public function updateProfile(Teacher $teacher, array $data): TeacherProfile
    {
        $profile = $teacher->profile;

        if (!$profile) {
            return $this->createProfile($teacher, $data);
        }

        $profile->update(array_intersect_key($data, array_flip([
            'first_name', 'last_name', 'first_name_bn', 'last_name_bn',
            'gender', 'date_of_birth', 'blood_group', 'religion', 'nationality',
            'nid', 'passport', 'email', 'mobile', 'present_address', 'permanent_address'
        ])));

        if (isset($data['first_name']) || isset($data['last_name'])) {
            $teacher->user->update([
                'name' => trim(($data['first_name'] ?? $profile->first_name) . ' ' . ($data['last_name'] ?? $profile->last_name ?? ''))
            ]);
        }

        return $profile->fresh();
    }

    public function updatePhoto(Teacher $teacher, UploadedFile $photo): string
    {
        $profile = $teacher->profile;

        if ($profile?->photo) {
            ImageHelper::delete($profile->photo);
        }

        $path = ImageHelper::uploadAndResize($photo, 'teachers/photos', 300, 300);

        if ($profile) {
            $profile->update(['photo' => $path]);
        }

        return asset('storage/' . $path);
    }

    public function updateStatus(Teacher $teacher, string $status, ?string $remarks = null): void
    {
        $teacher->update([
            'status' => $status,
            'remarks' => $remarks,
        ]);
    }

    public function addQualification(Teacher $teacher, array $data): TeacherQualification
    {
        return TeacherQualification::create([
            'uuid' => (string) Str::uuid(),
            'teacher_id' => $teacher->id,
            'degree' => $data['degree'],
            'degree_bn' => $data['degree_bn'] ?? null,
            'institution' => $data['institution'],
            'board_university' => $data['board_university'] ?? null,
            'subject' => $data['subject'] ?? null,
            'passing_year' => $data['passing_year'] ?? null,
            'result' => $data['result'] ?? null,
            'result_point' => $data['result_point'] ?? null,
            'attachment' => $data['attachment'] ?? null,
        ]);
    }

    public function updateQualification(Teacher $teacher, string $qualUuid, array $data): TeacherQualification
    {
        $qual = $teacher->qualifications()->where('uuid', $qualUuid)->firstOrFail();
        $qual->update(array_intersect_key($data, array_flip([
            'degree', 'degree_bn', 'institution', 'board_university', 'subject',
            'passing_year', 'result', 'result_point', 'attachment'
        ])));
        return $qual->fresh();
    }

    public function deleteQualification(Teacher $teacher, string $qualUuid): bool
    {
        return $teacher->qualifications()->where('uuid', $qualUuid)->delete() > 0;
    }

    public function addExperience(Teacher $teacher, array $data): TeacherExperience
    {
        return TeacherExperience::create([
            'uuid' => (string) Str::uuid(),
            'teacher_id' => $teacher->id,
            'organization' => $data['organization'],
            'organization_bn' => $data['organization_bn'] ?? null,
            'designation' => $data['designation'] ?? null,
            'department' => $data['department'] ?? null,
            'joining_date' => $data['joining_date'] ?? null,
            'resign_date' => $data['resign_date'] ?? null,
            'is_current' => $data['is_current'] ?? false,
            'responsibilities' => $data['responsibilities'] ?? null,
            'document' => $data['document'] ?? null,
            'remarks' => $data['remarks'] ?? null,
        ]);
    }

    public function updateExperience(Teacher $teacher, string $expUuid, array $data): TeacherExperience
    {
        $exp = $teacher->experiences()->where('uuid', $expUuid)->firstOrFail();
        $exp->update(array_intersect_key($data, array_flip([
            'organization', 'organization_bn', 'designation', 'department',
            'joining_date', 'resign_date', 'is_current', 'responsibilities', 'document', 'remarks'
        ])));
        return $exp->fresh();
    }

    public function deleteExperience(Teacher $teacher, string $expUuid): bool
    {
        return $teacher->experiences()->where('uuid', $expUuid)->delete() > 0;
    }

    public function assignSubjects(Teacher $teacher, array $data): void
    {
        foreach ($data['assignments'] ?? [] as $assignment) {
            TeacherSubjectAssignment::updateOrCreate(
                [
                    'teacher_id' => $teacher->id,
                    'subject_id' => $assignment['subject_id'],
                    'program_id' => $assignment['program_id'],
                    'semester_id' => $assignment['semester_id'] ?? null,
                    'session_id' => $assignment['session_id'],
                ],
                [
                    'is_class_teacher' => $assignment['is_class_teacher'] ?? false,
                    'status' => 'active',
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                ]
            );
        }
    }

    public function removeSubject(Teacher $teacher, string $assignmentUuid): bool
    {
        return $teacher->subjectAssignments()->where('uuid', $assignmentUuid)->delete() > 0;
    }

    public function assignClasses(Teacher $teacher, array $data): void
    {
        foreach ($data['assignments'] ?? [] as $assignment) {
            TeacherClassAssignment::updateOrCreate(
                [
                    'teacher_id' => $teacher->id,
                    'class_id' => $assignment['class_id'],
                    'section_id' => $assignment['section_id'] ?? null,
                    'session_id' => $assignment['session_id'],
                ],
                [
                    'is_primary_teacher' => $assignment['is_primary_teacher'] ?? false,
                    'weekly_classes' => $assignment['weekly_classes'] ?? 0,
                    'status' => 'active',
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                ]
            );
        }
    }

    public function removeClass(Teacher $teacher, string $assignmentUuid): bool
    {
        return $teacher->classAssignments()->where('uuid', $assignmentUuid)->delete() > 0;
    }

    public function updateSalary(Teacher $teacher, array $data): TeacherSalary
    {
        $salary = $teacher->salary ?? new TeacherSalary([
            'teacherable_type' => Teacher::class,
            'teacherable_id' => $teacher->id,
            'uuid' => (string) Str::uuid(),
        ]);

        $salary->fill([
            'basic_salary' => $data['basic_salary'] ?? 0,
            'house_rent' => $data['house_rent'] ?? 0,
            'medical_allowance' => $data['medical_allowance'] ?? 0,
            'transport_allowance' => $data['transport_allowance'] ?? 0,
            'other_allowance' => $data['other_allowance'] ?? 0,
            'tax_deduction' => $data['tax_deduction'] ?? 0,
            'provident_fund' => $data['provident_fund'] ?? 0,
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

    public function applyLeave(Teacher $teacher, array $data): TeacherLeave
    {
        return TeacherLeave::create([
            'uuid' => (string) Str::uuid(),
            'teacherable_type' => Teacher::class,
            'teacherable_id' => $teacher->id,
            'leave_type' => $data['leave_type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'total_days' => $data['total_days'] ?? 1,
            'reason' => $data['reason'],
            'status' => TeacherLeave::STATUS_PENDING,
            'applied_by' => auth()->id(),
            'applied_at' => now(),
        ]);
    }

    public function generateQRCode(Teacher $teacher): string
    {
        $data = [
            'uuid' => $teacher->uuid,
            'teacher_no' => $teacher->teacher_no,
            'name' => $teacher->full_name,
        ];

        return 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')
                ->size(200)
                ->generate(json_encode($data))
        );
    }

    public function search(string $query, int $perPage = 20): LengthAwarePaginator
    {
        return Teacher::search($query)
            ->with(['profile', 'department'])
            ->paginate($perPage);
    }

    public function delete(Teacher $teacher): bool
    {
        return $teacher->delete();
    }

    public function getStatistics(): array
    {
        return [
            'total' => Teacher::count(),
            'active' => Teacher::where('status', Teacher::STATUS_ACTIVE)->count(),
            'pending' => Teacher::where('status', Teacher::STATUS_PENDING)->count(),
            'on_leave' => Teacher::where('status', Teacher::STATUS_ON_LEAVE)->count(),
        ];
    }

    public function getActiveCount(): int
    {
        return Teacher::where('status', Teacher::STATUS_ACTIVE)->count();
    }

    public function import(UploadedFile $file): array
    {
        return ['total' => 0, 'success' => 0, 'failed' => 0, 'errors' => []];
    }

    public function export(string $format, array $filters = []): string
    {
        $filename = 'teachers_export_' . now()->format('Y_m_d_H_i_s');
        return url('storage/exports/' . $filename . '.' . $format);
    }

    private function getTeacherRoleId(): int
    {
        return DB::table('roles')->where('name', 'teacher')->value('id') ?? 0;
    }
}
