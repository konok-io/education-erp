<?php

declare(strict_types=1);

namespace App\Services\Student;

use App\Helpers\ImageHelper;
use App\Models\Student\Student;
use App\Models\Student\StudentProfile;
use App\Models\Student\Guardian;
use App\Models\Student\StudentMedical;
use App\Models\Student\StudentDocument;
use App\Models\Student\StudentPromotion;
use App\Models\Student\StudentTransfer;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentService
{
    /**
     * Get relations to load.
     */
    public function getRelations(): array
    {
        return [
            'profile',
            'guardian',
            'medical',
            'documents',
            'session',
            'academicLevel',
            'faculty',
            'department',
            'program',
            'semester',
            'class',
            'section',
            'group',
            'campus',
        ];
    }

    /**
     * Get all students with filters.
     */
    public function getAll(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Student::query()->with(['profile', 'session', 'program']);

        // Apply filters
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['session_id'])) {
            $query->bySession($filters['session_id']);
        }

        if (!empty($filters['academic_level_id'])) {
            $query->where('academic_level_id', $filters['academic_level_id']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['program_id'])) {
            $query->byProgram($filters['program_id']);
        }

        if (!empty($filters['class_id'])) {
            $query->byClass($filters['class_id']);
        }

        if (!empty($filters['section_id'])) {
            $query->bySection($filters['section_id']);
        }

        if (!empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (!empty($filters['gender'])) {
            $query->whereHas('profile', fn($q) => $q->where('gender', $filters['gender']));
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Create new student.
     */
    public function create(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            // Generate student number
            $sessionCode = $data['session_code'] ?? now()->format('Y');
            $studentNo = Student::generateStudentNo($sessionCode);

            // Create user account
            $user = User::create([
                'uuid' => (string) Str::uuid(),
                'campus_id' => $data['campus_id'] ?? auth()->user()->campus_id,
                'name' => $data['first_name'] . ' ' . ($data['last_name'] ?? ''),
                'email' => $data['email'],
                'mobile' => $data['mobile'] ?? null,
                'password' => Hash::make($data['password'] ?? 'Student@123'),
                'role_id' => $this->getStudentRoleId(),
                'status' => 'active',
            ]);

            // Assign student role
            DB::table('model_has_roles')->insert([
                'role_id' => $this->getStudentRoleId(),
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id,
            ]);

            // Create student
            $student = Student::create([
                'uuid' => (string) Str::uuid(),
                'student_no' => $studentNo,
                'user_id' => $user->id,
                'campus_id' => $data['campus_id'] ?? auth()->user()->campus_id,
                'session_id' => $data['session_id'],
                'academic_level_id' => $data['academic_level_id'],
                'faculty_id' => $data['faculty_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'program_id' => $data['program_id'],
                'semester_id' => $data['semester_id'] ?? null,
                'class_id' => $data['class_id'] ?? null,
                'section_id' => $data['section_id'] ?? null,
                'group_id' => $data['group_id'] ?? null,
                'status' => 'pending',
                'admission_date' => $data['admission_date'] ?? now(),
                'remarks' => $data['remarks'] ?? null,
            ]);

            // Create profile
            $this->createProfile($student, $data);

            // Create guardian
            if (!empty($data['guardian'])) {
                $this->createGuardian($student, $data['guardian']);
            }

            // Create medical info
            if (!empty($data['medical'])) {
                $this->createMedical($student, $data['medical']);
            }

            return $student;
        });
    }

    /**
     * Create student profile.
     */
    private function createProfile(Student $student, array $data): StudentProfile
    {
        return StudentProfile::create([
            'uuid' => (string) Str::uuid(),
            'studentable_type' => Student::class,
            'studentable_id' => $student->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'first_name_bn' => $data['first_name_bn'] ?? null,
            'last_name_bn' => $data['last_name_bn'] ?? null,
            'gender' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'blood_group' => $data['blood_group'] ?? null,
            'religion' => $data['religion'] ?? null,
            'nationality' => $data['nationality'] ?? 'Bangladeshi',
            'birth_certificate' => $data['birth_certificate'] ?? null,
            'nid' => $data['nid'] ?? null,
            'passport' => $data['passport'] ?? null,
            'email' => $data['email'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'present_address' => $data['present_address'] ?? null,
            'permanent_address' => $data['permanent_address'] ?? null,
        ]);
    }

    /**
     * Create guardian.
     */
    private function createGuardian(Student $student, array $data): Guardian
    {
        return Guardian::create([
            'uuid' => (string) Str::uuid(),
            'studentable_type' => Student::class,
            'studentable_id' => $student->id,
            'guardian_type' => $data['guardian_type'] ?? 'father',
            'name' => $data['name'],
            'name_bn' => $data['name_bn'] ?? null,
            'relation' => $data['relation'] ?? null,
            'occupation' => $data['occupation'] ?? null,
            'organization' => $data['organization'] ?? null,
            'designation' => $data['designation'] ?? null,
            'mobile' => $data['mobile'],
            'email' => $data['email'] ?? null,
            'nid' => $data['nid'] ?? null,
            'annual_income' => $data['annual_income'] ?? null,
            'address' => $data['address'] ?? null,
            'is_emergency_contact' => $data['is_emergency_contact'] ?? true,
        ]);
    }

    /**
     * Create medical info.
     */
    private function createMedical(Student $student, array $data): StudentMedical
    {
        return StudentMedical::create([
            'uuid' => (string) Str::uuid(),
            'studentable_type' => Student::class,
            'studentable_id' => $student->id,
            'height' => $data['height'] ?? null,
            'weight' => $data['weight'] ?? null,
            'blood_group' => $data['blood_group'] ?? null,
            'allergy' => $data['allergy'] ?? false,
            'allergy_details' => $data['allergy_details'] ?? null,
            'chronic_disease' => $data['chronic_disease'] ?? false,
            'chronic_disease_details' => $data['chronic_disease_details'] ?? null,
            'disability' => $data['disability'] ?? false,
            'disability_details' => $data['disability_details'] ?? null,
            'medical_note' => $data['medical_note'] ?? null,
        ]);
    }

    /**
     * Find student by UUID.
     */
    public function findByUuid(string $uuid): ?Student
    {
        return Student::where('uuid', $uuid)->first();
    }

    /**
     * Find student by student number.
     */
    public function findByStudentNo(string $studentNo): ?Student
    {
        return Student::where('student_no', $studentNo)
            ->with($this->getRelations())
            ->first();
    }

    /**
     * Update student.
     */
    public function update(Student $student, array $data): Student
    {
        return DB::transaction(function () use ($student, $data) {
            // Update student fields
            $studentFields = array_intersect_key($data, array_flip([
                'session_id', 'academic_level_id', 'faculty_id', 'department_id',
                'program_id', 'semester_id', 'class_id', 'section_id', 'group_id', 'remarks'
            ]));

            if (!empty($studentFields)) {
                $student->update($studentFields);
            }

            // Update profile
            if (!empty($data['profile'])) {
                $this->updateProfile($student, $data['profile']);
            }

            // Update guardian
            if (!empty($data['guardian'])) {
                $this->updateGuardian($student, $data['guardian']);
            }

            // Update medical
            if (!empty($data['medical'])) {
                $this->updateMedical($student, $data['medical']);
            }

            return $student->fresh($this->getRelations());
        });
    }

    /**
     * Update profile.
     */
    public function updateProfile(Student $student, array $data): StudentProfile
    {
        $profile = $student->profile;

        if (!$profile) {
            return $this->createProfile($student, $data);
        }

        $profile->update(array_intersect_key($data, array_flip([
            'first_name', 'last_name', 'first_name_bn', 'last_name_bn',
            'gender', 'date_of_birth', 'blood_group', 'religion', 'nationality',
            'birth_certificate', 'nid', 'passport', 'email', 'mobile',
            'present_address', 'permanent_address'
        ])));

        // Update user name if profile updated
        if (isset($data['first_name']) || isset($data['last_name'])) {
            $student->user->update([
                'name' => trim(($data['first_name'] ?? $profile->first_name) . ' ' . ($data['last_name'] ?? $profile->last_name ?? ''))
            ]);
        }

        return $profile->fresh();
    }

    /**
     * Update guardian.
     */
    public function updateGuardian(Student $student, array $data): Guardian
    {
        $guardian = $student->guardian;

        if (!$guardian) {
            return $this->createGuardian($student, $data);
        }

        $guardian->update(array_intersect_key($data, array_flip([
            'guardian_type', 'name', 'name_bn', 'relation', 'occupation',
            'organization', 'designation', 'mobile', 'email', 'nid',
            'annual_income', 'address', 'is_emergency_contact'
        ])));

        return $guardian->fresh();
    }

    /**
     * Update medical info.
     */
    public function updateMedical(Student $student, array $data): StudentMedical
    {
        $medical = $student->medical;

        if (!$medical) {
            return $this->createMedical($student, $data);
        }

        $medical->update(array_intersect_key($data, array_flip([
            'height', 'weight', 'blood_group', 'allergy', 'allergy_details',
            'chronic_disease', 'chronic_disease_details', 'disability',
            'disability_details', 'medication', 'medical_note'
        ])));

        return $medical->fresh();
    }

    /**
     * Update photo.
     */
    public function updatePhoto(Student $student, UploadedFile $photo): string
    {
        $profile = $student->profile;

        // Delete old photo
        if ($profile?->photo) {
            ImageHelper::delete($profile->photo);
        }

        // Upload new photo
        $path = ImageHelper::uploadAndResize($photo, 'students/photos', 300, 300);

        // Update profile
        if ($profile) {
            $profile->update(['photo' => $path]);
        }

        return asset('storage/' . $path);
    }

    /**
     * Update status.
     */
    public function updateStatus(Student $student, string $status, ?string $remarks = null): void
    {
        $student->update([
            'status' => $status,
            'remarks' => $remarks,
        ]);
    }

    /**
     * Upload document.
     */
    public function uploadDocument(Student $student, UploadedFile $file, array $data): StudentDocument
    {
        $path = $file->store('students/documents', 'public');

        return StudentDocument::create([
            'uuid' => (string) Str::uuid(),
            'studentable_type' => Student::class,
            'studentable_id' => $student->id,
            'document_type' => $data['document_type'],
            'title' => $data['title'],
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'issue_date' => $data['issue_date'] ?? null,
            'expiry_date' => $data['expiry_date'] ?? null,
        ]);
    }

    /**
     * Delete document.
     */
    public function deleteDocument(Student $student, string $documentUuid): bool
    {
        $document = $student->documents()->where('uuid', $documentUuid)->first();

        if (!$document) {
            return false;
        }

        // Delete file
        if ($document->file_path) {
            \Storage::disk('public')->delete($document->file_path);
        }

        return $document->delete();
    }

    /**
     * Promote student.
     */
    public function promote(Student $student, array $data): StudentPromotion
    {
        return DB::transaction(function () use ($student, $data) {
            // Create promotion record
            $promotion = StudentPromotion::create([
                'uuid' => (string) Str::uuid(),
                'student_id' => $student->id,
                'from_session_id' => $student->session_id,
                'to_session_id' => $data['to_session_id'],
                'from_semester_id' => $student->semester_id,
                'to_semester_id' => $data['to_semester_id'] ?? null,
                'from_class_id' => $student->class_id,
                'to_class_id' => $data['to_class_id'],
                'from_section_id' => $student->section_id,
                'to_section_id' => $data['to_section_id'] ?? null,
                'from_group_id' => $student->group_id,
                'to_group_id' => $data['to_group_id'] ?? null,
                'result' => $data['result'] ?? null,
                'status' => $data['status'] ?? 'promoted',
                'promoted_by' => auth()->id(),
                'promotion_date' => now(),
                'remarks' => $data['remarks'] ?? null,
            ]);

            // Update student
            $student->update([
                'session_id' => $data['to_session_id'],
                'semester_id' => $data['to_semester_id'] ?? $student->semester_id,
                'class_id' => $data['to_class_id'],
                'section_id' => $data['to_section_id'] ?? $student->section_id,
                'group_id' => $data['to_group_id'] ?? $student->group_id,
            ]);

            return $promotion;
        });
    }

    /**
     * Transfer student.
     */
    public function transfer(Student $student, array $data): StudentTransfer
    {
        return DB::transaction(function () use ($student, $data) {
            $transfer = StudentTransfer::create([
                'uuid' => (string) Str::uuid(),
                'student_id' => $student->id,
                'transfer_type' => $data['transfer_type'],
                'from_campus_id' => $student->campus_id,
                'to_campus_id' => $data['to_campus_id'] ?? $student->campus_id,
                'from_department_id' => $student->department_id,
                'to_department_id' => $data['to_department_id'] ?? $student->department_id,
                'from_program_id' => $student->program_id,
                'to_program_id' => $data['to_program_id'] ?? $student->program_id,
                'from_class_id' => $student->class_id,
                'to_class_id' => $data['to_class_id'] ?? $student->class_id,
                'from_section_id' => $student->section_id,
                'to_section_id' => $data['to_section_id'] ?? $student->section_id,
                'from_group_id' => $student->group_id,
                'to_group_id' => $data['to_group_id'] ?? $student->group_id,
                'reason' => $data['reason'],
                'transfer_date' => now(),
                'approved_by' => auth()->id(),
                'status' => 'approved',
                'remarks' => $data['remarks'] ?? null,
            ]);

            // Update student
            $student->update([
                'campus_id' => $data['to_campus_id'] ?? $student->campus_id,
                'department_id' => $data['to_department_id'] ?? $student->department_id,
                'program_id' => $data['to_program_id'] ?? $student->program_id,
                'class_id' => $data['to_class_id'] ?? $student->class_id,
                'section_id' => $data['to_section_id'] ?? $student->section_id,
                'group_id' => $data['to_group_id'] ?? $student->group_id,
            ]);

            return $transfer;
        });
    }

    /**
     * Generate QR code.
     */
    public function generateQRCode(Student $student): string
    {
        $data = [
            'uuid' => $student->uuid,
            'student_no' => $student->student_no,
            'name' => $student->full_name,
        ];

        return 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')
                ->size(200)
                ->generate(json_encode($data))
        );
    }

    /**
     * Search students.
     */
    public function search(string $query, int $perPage = 20): LengthAwarePaginator
    {
        return Student::search($query)
            ->with(['profile', 'session', 'program'])
            ->paginate($perPage);
    }

    /**
     * Delete student (soft delete).
     */
    public function delete(Student $student): bool
    {
        return $student->delete();
    }

    /**
     * Get statistics.
     */
    public function getStatistics(array $filters = []): array
    {
        $query = Student::query();

        if (!empty($filters['session_id'])) {
            $query->bySession($filters['session_id']);
        }

        if (!empty($filters['program_id'])) {
            $query->byProgram($filters['program_id']);
        }

        if (!empty($filters['class_id'])) {
            $query->byClass($filters['class_id']);
        }

        return [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'transferred' => (clone $query)->where('status', 'transferred')->count(),
            'graduated' => (clone $query)->where('status', 'graduated')->count(),
        ];
    }

    /**
     * Get active students count.
     */
    public function getActiveCount(?string $sessionId = null): int
    {
        $query = Student::active();

        if ($sessionId) {
            $query->bySession($sessionId);
        }

        return $query->count();
    }

    /**
     * Import students.
     */
    public function import(UploadedFile $file, string $sessionId): array
    {
        // Import logic would go here with Maatwebsite\Excel
        return [
            'total' => 0,
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];
    }

    /**
     * Export students.
     */
    public function export(string $format, array $filters = []): string
    {
        // Export logic would go here with Maatwebsite\Excel
        $filename = 'students_export_' . now()->format('Y_m_d_H_i_s');
        return url('storage/exports/' . $filename . '.' . $format);
    }

    /**
     * Get student role ID.
     */
    private function getStudentRoleId(): int
    {
        return DB::table('roles')->where('name', 'student')->value('id') ?? 0;
    }
}
